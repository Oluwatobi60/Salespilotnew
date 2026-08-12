<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Mail\SubscriptionRenewed;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class SubscriptionRenewalController extends Controller
{
    /**
     * List all subscriptions with renewal status.
     */
    public function index(Request $request)
    {
        // Mirror User::currentSubscription() logic:
        // For each user, pick their active subscription with the LATEST end_date.
        // Ties broken by highest id. Falls back to most recent non-active sub.

        // Active: per-user latest end_date (then highest id on ties)
        $bestActiveDates = DB::table('user_subscriptions')
            ->selectRaw('user_id, MAX(end_date) as best_end_date')
            ->where('status', 'active')
            ->where('end_date', '>=', Carbon::today())
            ->groupBy('user_id');

        $activeIds = DB::table('user_subscriptions as us')
            ->joinSub($bestActiveDates, 'ba', fn($j) =>
                $j->on('us.user_id', '=', 'ba.user_id')
                  ->on('us.end_date', '=', 'ba.best_end_date'))
            ->where('us.status', 'active')
            ->where('us.end_date', '>=', Carbon::today())
            ->selectRaw('MAX(us.id) as id')
            ->groupBy('us.user_id')
            ->pluck('id');

        // Fallback for users with no active subscription
        $usersWithActive = DB::table('user_subscriptions')
            ->where('status', 'active')
            ->where('end_date', '>=', Carbon::today())
            ->distinct()
            ->pluck('user_id');

        $fallbackIds = DB::table('user_subscriptions')
            ->whereNotIn('user_id', $usersWithActive)
            ->selectRaw('MAX(id) as id')
            ->groupBy('user_id')
            ->pluck('id');

        $pendingIds = DB::table('user_subscriptions')
            ->where('status', 'pending')
            ->pluck('id');

        $currentIds = $activeIds->merge($fallbackIds)->merge($pendingIds)->unique();

        $query = UserSubscription::with(['user', 'subscriptionPlan'])
            ->whereIn('id', $currentIds)
            ->orderByRaw("FIELD(status, 'pending', 'active', 'expired', 'cancelled')")
            ->orderBy('end_date', 'asc');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('user', function ($q) use ($s) {
                $q->where('first_name', 'like', "%{$s}%")
                  ->orWhere('surname', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('business_name', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('status', 'active')
                      ->where('end_date', '>=', Carbon::today());
            } elseif ($request->status === 'expired') {
                $query->where(function ($q) {
                    $q->where('status', 'expired')
                      ->orWhere(function ($q2) {
                          $q2->where('status', 'active')
                             ->where('end_date', '<', Carbon::today());
                      });
                });
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('auto_renew')) {
            $query->where('auto_renew', $request->auto_renew === '1');
        }

        $subscriptions = $query->paginate(20)->withQueryString();

        $stats = [
            'total'       => $currentIds->count(),
            'pending'     => UserSubscription::whereIn('id', $currentIds)
                                ->where('status', 'pending')
                                ->count(),
            'active'      => UserSubscription::whereIn('id', $currentIds)
                                ->where('status', 'active')
                                ->where('end_date', '>=', Carbon::today())
                                ->count(),
            'auto_renew'  => UserSubscription::whereIn('id', $currentIds)
                                ->where('auto_renew', true)
                                ->where('status', 'active')
                                ->where('end_date', '>=', Carbon::today())
                                ->count(),
            'expiring_7d' => UserSubscription::whereIn('id', $currentIds)
                                ->where('status', 'active')
                                ->where('end_date', '>=', Carbon::today())
                                ->whereBetween('end_date', [Carbon::today(), Carbon::today()->addDays(7)])
                                ->count(),
        ];

        return view('superadmin.subscriptions.index', compact('subscriptions', 'stats'));
    }

    /**
     * Toggle auto-renewal for a single subscription.
     */
    public function toggle(UserSubscription $subscription)
    {
        $subscription->auto_renew = ! $subscription->auto_renew;
        $subscription->save();

        $state = $subscription->auto_renew ? 'enabled' : 'disabled';

        return back()->with('success', "Auto-renewal {$state} for {$subscription->user->business_name}.");
    }

    /**
     * Bulk-toggle auto-renewal for multiple subscriptions.
     */
    public function bulkToggle(Request $request)
    {
        $validated = $request->validate([
            'ids'        => 'required|array',
            'ids.*'      => 'integer|exists:user_subscriptions,id',
            'auto_renew' => 'required|boolean',
        ]);

        UserSubscription::whereIn('id', $validated['ids'])
            ->update(['auto_renew' => $validated['auto_renew']]);

        $count  = count($validated['ids']);
        $state  = $validated['auto_renew'] ? 'enabled' : 'disabled';

        return back()->with('success', "Auto-renewal {$state} for {$count} subscription(s).");
    }

    /**
     * Manually send renewal-reminder emails to subscribers expiring within N days.
     */
    public function sendReminders(Request $request)
    {
        $days = (int) $request->input('days', 7);
        $days = max(1, min($days, 30));

        $expiring = UserSubscription::with(['user', 'subscriptionPlan'])
            ->where('status', 'active')
            ->whereBetween('end_date', [Carbon::today(), Carbon::today()->addDays($days)])
            ->get();

        $sent = 0;
        foreach ($expiring as $sub) {
            if ($sub->user && $sub->user->email) {
                $remaining = Carbon::today()->diffInDays($sub->end_date);
                Mail::to($sub->user->email)
                    ->send(new \App\Mail\SubscriptionExpiryReminder($sub->user, $sub, $remaining));
                $sent++;
            }
        }

        return back()->with('success', "Sent renewal reminders to {$sent} subscriber(s) expiring within {$days} day(s).");
    }

    /**
     * Manually trigger the auto-renewal processor.
     */
    public function processRenewals()
    {
        $renewed = 0;
        $errors  = [];

        // Find active subscriptions that ended today or earlier with auto_renew on
        $due = UserSubscription::with(['user', 'subscriptionPlan'])
            ->where('status', 'active')
            ->where('auto_renew', true)
            ->where('end_date', '<=', Carbon::today())
            ->get();

        foreach ($due as $sub) {
            try {
                DB::beginTransaction();

                // Mark old subscription as expired
                $sub->status = 'expired';
                $sub->save();

                // Create renewed subscription
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

                // Generate renewal commission for BRM
                if ($sub->user && $sub->user->brm_id && $newSub->amount_paid > 0) {
                    $commission = \App\Http\Controllers\Brm\BrmCommissionController::generateCommission($newSub);
                    if ($commission) {
                        $commission->update(['commission_type' => 'renewal']);
                    }
                }

                DB::commit();

                // Send renewal confirmation email
                if ($sub->user && $sub->user->email) {
                    Mail::to($sub->user->email)
                        ->send(new SubscriptionRenewed($sub->user, $newSub));
                }

                $renewed++;
            } catch (\Exception $e) {
                DB::rollBack();
                $errors[] = "User #{$sub->user_id}: " . $e->getMessage();
            }
        }

        $msg = "Processed {$renewed} auto-renewal(s).";
        if (count($errors)) {
            $msg .= ' Errors: ' . implode('; ', $errors);
        }

        return back()->with($errors ? 'error' : 'success', $msg);
    }

    /**
     * Approve a pending bank transfer subscription.
     */
    public function approvePending(UserSubscription $subscription)
    {
        if ($subscription->status !== 'pending') {
            return back()->with('error', 'Only pending subscriptions can be approved.');
        }

        $subscription->status = 'active';
        // Adjust start date to today, since approval might happen days after submission
        $subscription->start_date = Carbon::today();
        if ($subscription->duration_months > 0) {
            $subscription->end_date = Carbon::today()->addMonths($subscription->duration_months);
        } elseif ($subscription->subscriptionPlan && $subscription->subscriptionPlan->trial_days > 0) {
            $subscription->end_date = Carbon::today()->addDays($subscription->subscriptionPlan->trial_days);
        }
        $subscription->save();

        if ($subscription->user && $subscription->user->email) {
            try {
                Mail::to($subscription->user->email)->send(new \App\Mail\SubscriptionActivated($subscription->user, $subscription));
            } catch (\Exception $e) {
                // Email failure shouldn't rollback approval, just log it
                \Illuminate\Support\Facades\Log::error("Failed to send activation email: " . $e->getMessage());
            }
        }

        return back()->with('success', "Subscription approved and activated for {$subscription->user->business_name}.");
    }

    /**
     * Reject a pending bank transfer subscription.
     */
    public function rejectPending(UserSubscription $subscription)
    {
        if ($subscription->status !== 'pending') {
            return back()->with('error', 'Only pending subscriptions can be rejected.');
        }

        $subscription->status = 'cancelled';
        $subscription->cancellation_reason = 'Bank transfer rejected by admin.';
        $subscription->cancelled_at = Carbon::now();
        $subscription->save();

        return back()->with('success', "Subscription rejected for {$subscription->user->business_name}.");
    }
}
