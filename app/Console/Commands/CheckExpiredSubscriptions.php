<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserSubscription;
use App\Mail\SubscriptionExpiryReminder;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class CheckExpiredSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:check-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and update expired subscriptions and send reminders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired subscriptions and sending reminders...');

        // 1. Check and expire subscriptions
        $expiredSubscriptions = UserSubscription::where('status', 'active')
            ->where('end_date', '<', Carbon::today())
            ->get();

        $expiredCount = $expiredSubscriptions->count();

        foreach ($expiredSubscriptions as $subscription) {
            $subscription->update(['status' => 'expired']);
            $this->line("Expired subscription ID: {$subscription->id} for user: {$subscription->user->email}");
        }

        if ($expiredCount > 0) {
            $this->info("Successfully updated {$expiredCount} expired subscription(s).");
        }

        // 2. Send reminders for subscriptions expiring within 5 days
        $expiringSubscriptions = UserSubscription::with(['user', 'subscriptionPlan'])
            ->where('status', 'active')
            ->whereBetween('end_date', [
                Carbon::today(),
                Carbon::today()->addDays(5)
            ])
            ->get();

        $reminderCount = 0;

        foreach ($expiringSubscriptions as $subscription) {
            $daysRemaining = Carbon::today()->diffInDays($subscription->end_date, false);

            if ($daysRemaining >= 0) {
                // Send email reminder
                try {
                    Mail::to($subscription->user->email)->send(
                        new SubscriptionExpiryReminder($subscription->user, $subscription, $daysRemaining)
                    );

                    $this->line("Reminder sent to {$subscription->user->email} - {$daysRemaining} days remaining");
                    $reminderCount++;
                } catch (\Exception $e) {
                    $this->error("Failed to send reminder to {$subscription->user->email}: {$e->getMessage()}");
                }
            }
        }

        if ($reminderCount > 0) {
            $this->info("Successfully sent {$reminderCount} expiry reminder(s).");
        }

        if ($expiredCount === 0 && $reminderCount === 0) {
            $this->info('No expired subscriptions or reminders to send.');
        }

        return Command::SUCCESS;
    }
}
