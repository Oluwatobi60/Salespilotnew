<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\UserSubscription;
use Carbon\Carbon;

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

            // Get user's active subscription
            $subscription = UserSubscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->first();

            // If subscription exists and has expired, update status
            if ($subscription && $subscription->end_date < Carbon::today()) {
                $subscription->update(['status' => 'expired']);

                // Logout user and redirect to login with message
                Auth::logout();
                return redirect()->route('login')->withErrors([
                    'email' => 'Your subscription has expired. Please renew to continue using SalesPilot.',
                ])->with('redirect_to_plans', true);
            }

            // If no active subscription exists (already expired or cancelled)
            if (!$subscription) {
                $hasAnySubscription = UserSubscription::where('user_id', $user->id)->exists();

                if ($hasAnySubscription) {
                    Auth::logout();
                    return redirect()->route('login')->withErrors([
                        'email' => 'Your subscription has expired. Please renew to continue using SalesPilot.',
                    ])->with('redirect_to_plans', true);
                }
            }
        }

        return $next($request);
    }
}
