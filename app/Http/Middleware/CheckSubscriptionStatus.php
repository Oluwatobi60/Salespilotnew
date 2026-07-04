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

            // No valid subscription — check if they have ever had one (not brand-new signups)
            $hasAnySubscription = UserSubscription::where('user_id', $user->id)->exists();

            if ($hasAnySubscription) {
                Auth::logout();
                return redirect()->route('login')->withErrors([
                    'email' => 'Your subscription has expired. Please renew to continue using SalesPilot.',
                ])->with('redirect_to_plans', true);
            }
        }

        return $next($request);
    }
}
