<?php

namespace App\Console\Commands;

use App\Mail\SubscriptionExpiryReminder;
use App\Mail\SubscriptionRenewed;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProcessAutoRenewals extends Command
{
    protected $signature = 'subscriptions:process-renewals';

    protected $description = 'Auto-renew eligible subscriptions and send expiry reminder emails';

    public function handle(): int
    {
        $this->info('Processing auto-renewalsâ€¦');

        $this->expireOverdue();
        $this->autoRenew();
        $this->sendExpiryReminders();

        $this->info('Done.');

        return Command::SUCCESS;
    }

    // â”€â”€â”€ Step 1: mark plain-expired subscriptions (no auto_renew) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    private function expireOverdue(): void
    {
        $count = UserSubscription::where('status', 'active')
            ->where('auto_renew', false)
            ->where('end_date', '<', Carbon::today())
            ->update(['status' => 'expired']);

        $this->line("  Marked {$count} subscription(s) as expired.");
    }

    // â”€â”€â”€ Step 2: auto-renew subscriptions due today or overdue â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
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
                // Check if user has an authorization code
                if (!$sub->user || !$sub->user->paystack_authorization_code) {
                    $sub->status = 'expired';
                    $sub->auto_renew = false;
                    $sub->save();
                    $this->warn("  âœ˜ Skipped auto-renewal for user #{$sub->user_id}: No Paystack authorization code (likely manual transfer)");
                    continue;
                }

                $amountKobo = $sub->amount_paid * 100;
                
                // If amount is 0, just renew it for free without calling Paystack
                if ($amountKobo == 0) {
                    $isSuccess = true;
                    $reference = 'AUTO-FREE-'.strtoupper(uniqid());
                } else {
                    // Call Paystack Charge Authorization API
                    $url = "https://api.paystack.co/transaction/charge_authorization";
                    $response = \Illuminate\Support\Facades\Http::withHeaders([
                        'Authorization' => 'Bearer ' . config('services.paystack.secret_key'),
                        'Content-Type' => 'application/json',
                    ])->post($url, [
                        'authorization_code' => $sub->user->paystack_authorization_code,
                        'email' => $sub->user->email,
                        'amount' => $amountKobo,
                    ]);

                    $result = $response->json();
                    $isSuccess = $response->successful() && isset($result['status']) && $result['status'] === true && isset($result['data']) && $result['data']['status'] === 'success';
                    $reference = $isSuccess ? $result['data']['reference'] : null;
                    $failureReason = isset($result['data']['gateway_response']) ? $result['data']['gateway_response'] : 'Transaction failed or insufficient funds';
                }

                if ($isSuccess) {
                    DB::beginTransaction();

                    $sub->status = 'expired';
                    $sub->save();

                    $newSub = UserSubscription::create([
                        'user_id' => $sub->user_id,
                        'subscription_plan_id' => $sub->subscription_plan_id,
                        'duration_months' => $sub->duration_months,
                        'amount_paid' => $sub->amount_paid,
                        'discount_percentage' => $sub->discount_percentage,
                        'start_date' => Carbon::today(),
                        'end_date' => Carbon::today()->addMonths($sub->duration_months),
                        'status' => 'active',
                        'payment_reference' => $reference,
                        'auto_renew' => true,
                        'last_renewed_at' => Carbon::now(),
                    ]);

                    // Generate renewal commission for BRM
                    if ($sub->user && $sub->user->brm_id && $newSub->amount_paid > 0) {
                        $commission = \App\Http\Controllers\Brm\BrmCommissionController::generateCommission($newSub);
                        if ($commission) {
                            $commission->update(['commission_type' => 'renewal']);
                        }
                    }

                    DB::commit();

                    if ($sub->user && $sub->user->email) {
                        Mail::to($sub->user->email)
                            ->send(new SubscriptionRenewed($sub->user, $newSub));
                    }

                    $renewed++;
                    $this->line("  âœ” Renewed: {$sub->user->business_name} (user #{$sub->user_id})");
                } else {
                    $sub->status = 'expired';
                    $sub->auto_renew = false;
                    $sub->save();
                    
                    if ($sub->user && $sub->user->email) {
                        Mail::to($sub->user->email)->send(new \App\Mail\AutoRenewalFailed($sub->user, $sub, $failureReason));
                    }
                    
                    $this->warn("  âœ˜ Failed Paystack charge for user #{$sub->user_id}: {$failureReason}");
                }
            } catch (\Exception $e) {
                if (DB::transactionLevel() > 0) {
                    DB::rollBack();
                }
                Log::error("Auto-renewal failed for user #{$sub->user_id}: ".$e->getMessage());
                $this->warn("  âœ˜ Failed for user #{$sub->user_id}: ".$e->getMessage());
            }
        }

        $this->line("  Auto-renewed {$renewed} subscription(s).");
    }

    // â”€â”€â”€ Step 3: send expiry reminders at 7, 3, and 1 day(s) out â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
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
                        $this->line("  ðŸ“§ Reminder ({$days}d): {$sub->user->email}");
                    }
                } catch (\Exception $e) {
                    Log::error("Reminder email failed for user #{$sub->user_id}: ".$e->getMessage());
                }
            }
        }

        $this->line("  Sent {$sent} expiry reminder(s).");
    }
}
