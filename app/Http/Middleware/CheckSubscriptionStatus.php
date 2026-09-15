<?php

namespace App\Http\Middleware;

use App\Models\UserSubscription;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only check for authenticated users
        if (Auth::check()) {
            $user = Auth::user();

            // Get the most recent active subscription (latest end_date first)
            $activeSubscription = UserSubscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->orderByDesc('end_date')
                ->first();

            // If there's a valid active subscription that hasn't expired, allow through
            if ($activeSubscription && $activeSubscription->end_date >= Carbon::today()) {
                return $next($request);
            }

            // If there's an active-status record that has expired, mark it expired
            if ($activeSubscription && $activeSubscription->end_date < Carbon::today()) {
                $activeSubscription->update(['status' => 'expired']);
            }

            // Check if any non-expired active subscription exists (could be the renewed one)
            $validSubscription = UserSubscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->where('end_date', '>=', Carbon::today())
                ->exists();

            if ($validSubscription) {
                return $next($request);
            }

            // Check if they have a pending subscription
            $pendingSubscription = UserSubscription::where('user_id', $user->id)
                ->where('status', 'pending')
                ->exists();

            if ($pendingSubscription) {
                return redirect()->route('plan_pricing')->with('redirect_to_plans', true);
            }

            // For managers created by another user (addby), check creator's subscription
            if ($user->role === 'manager' && $user->addby) {
                $creator = \App\Models\User::where('email', $user->addby)->first();
                if ($creator) {
                    $creatorActive = UserSubscription::where('user_id', $creator->id)
                        ->where('status', 'active')
                        ->where('end_date', '>=', Carbon::today())
                        ->exists();

                    if ($creatorActive) {
                        return $next($request);
                    }
                }
            }

            // If subscription is expired or missing, redirect to pricing page while keeping session active for renewal
            return redirect()->route('plan_pricing')->withErrors([
                'email' => 'Your subscription has expired. Please choose a plan to continue using VigCore.',
            ])->with('redirect_to_plans', true);
        }

        return $next($request);
    }
}
