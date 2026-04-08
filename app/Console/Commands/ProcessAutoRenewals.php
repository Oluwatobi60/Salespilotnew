<?php

namespace App\Console\Commands;

use App\Mail\SubscriptionRenewed;
use App\Mail\SubscriptionExpiryReminder;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProcessAutoRenewals extends Command
{

    protected $signature   = 'subscriptions:process-renewals';
    protected $description = 'Auto-renew eligible subscriptions and send expiry reminder emails';

    public function handle(): int
    {
        $this->info('Processing auto-renewals…');

        $this->expireOverdue();
        $this->autoRenew();
        $this->sendExpiryReminders();

        $this->info('Done.');

        return Command::SUCCESS;
    }

    // ─── Step 1: mark plain-expired subscriptions (no auto_renew) ─────────────
    private function expireOverdue(): void
    {
        $count = UserSubscription::where('status', 'active')
            ->where('auto_renew', false)
            ->where('end_date', '<', Carbon::today())
            ->update(['status' => 'expired']);

        $this->line("  Marked {$count} subscription(s) as expired.");
    }

    // ─── Step 2: auto-renew subscriptions due today or overdue ────────────────
    private function autoRenew(): void
    {
        $due = UserSubscription::with(['user', 'subscriptionPlan'])
            ->where('status', 'active')
            ->where('auto_renew', true)
            ->where('end_date', '<=', Carbon::today())
            ->get();

        $renewed = 0;

        foreach ($due as $sub) {
            try {
                DB::beginTransaction();

                $sub->status = 'expired';
                $sub->save();

                $newSub = UserSubscription::create([
                    'user_id'              => $sub->user_id,
                    'subscription_plan_id' => $sub->subscription_plan_id,
                    'duration_months'      => $sub->duration_months,
                    'amount_paid'          => $sub->amount_paid,
                    'discount_percentage'  => $sub->discount_percentage,
                    'start_date'           => Carbon::today(),
                    'end_date'             => Carbon::today()->addMonths($sub->duration_months),
                    'status'               => 'active',
                    'payment_reference'    => 'AUTO-' . strtoupper(uniqid()),
                    'auto_renew'           => true,
                    'last_renewed_at'      => Carbon::now(),
                ]);

                DB::commit();

                if ($sub->user && $sub->user->email) {
                    Mail::to($sub->user->email)
                        ->send(new SubscriptionRenewed($sub->user, $newSub));
                }

                $renewed++;
                $this->line("  ✔ Renewed: {$sub->user->business_name} (user #{$sub->user_id})");
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Auto-renewal failed for user #{$sub->user_id}: " . $e->getMessage());
                $this->warn("  ✘ Failed for user #{$sub->user_id}: " . $e->getMessage());
            }
        }

        $this->line("  Auto-renewed {$renewed} subscription(s).");
    }

    // ─── Step 3: send expiry reminders at 7, 3, and 1 day(s) out ─────────────
    private function sendExpiryReminders(): void
    {
        $reminderDays = [7, 3, 1];
        $sent = 0;

        foreach ($reminderDays as $days) {
            $targetDate = Carbon::today()->addDays($days);

            $expiring = UserSubscription::with(['user', 'subscriptionPlan'])
                ->where('status', 'active')
                ->whereDate('end_date', $targetDate)
                // Only send if we haven't already sent a reminder today
                ->where(function ($q) {
                    $q->whereNull('renewal_notified_at')
                      ->orWhereDate('renewal_notified_at', '<', Carbon::today());
                })
                ->get();

            foreach ($expiring as $sub) {
                try {
                    if ($sub->user && $sub->user->email) {
                        Mail::to($sub->user->email)
                            ->send(new SubscriptionExpiryReminder($sub->user, $sub, $days));

                        $sub->renewal_notified_at = Carbon::now();
                        $sub->save();

                        $sent++;
                        $this->line("  📧 Reminder ({$days}d): {$sub->user->email}");
                    }
                } catch (\Exception $e) {
                    Log::error("Reminder email failed for user #{$sub->user_id}: " . $e->getMessage());
                }
            }
        }

        $this->line("  Sent {$sent} expiry reminder(s).");
    }
}
