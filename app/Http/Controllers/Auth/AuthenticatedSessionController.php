<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Welcome\SignupRequest;
use App\Models\UserSubscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\User;


class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Get the authenticated user
        $user = Auth::user();

        // Check if email was verified via signup_request
        $signupRequest = SignupRequest::where('email', $user->email)
            ->where('is_used', true)
            ->first();

        // Allow login for managers created by another user (addby) if creator is verified and has active subscription
        if (!$signupRequest && $user->role === 'manager' && $user->addby) {
            $creator = User::where('email', $user->addby)->first();
            $creatorSignup = $creator ? SignupRequest::where('email', $creator->email)->where('is_used', true)->first() : null;
            $creatorActiveSub = $creator ? UserSubscription::where('user_id', $creator->id)
                ->where('status', 'active')
                ->where('end_date', '>=', now())
                ->first() : null;
            if ($creator && $creatorSignup && $creatorActiveSub) {
                // Allow login, skip this check
            } else {
                Auth::logout();
                return redirect()->route('login')->withErrors([
                    'email' => 'Your email has not been verified. Please complete the signup process first.',
                ]);
            }
        } elseif (!$signupRequest) {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Your email has not been verified. Please complete the signup process first.',
            ]);
        }

        // Check if user status is 0 (disabled) - only for managers created by another user
        if ($user->role === 'manager' && $user->addby && $user->status == 0) {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Your account has been disabled. Contact your administrator.'
            ]);
        }

        // If user is a manager created by another user, check addby and creator's subscription
        if ($user->role === 'manager' && $user->addby) {
            $creator = User::where('email', $user->addby)->first();
            if (!$creator) {
                Auth::logout();
                return redirect()->route('login')->withErrors([
                    'email' => 'Your account cannot be verified. Contact your administrator.'
                ]);
            }
            // Check creator's subscription
            $creatorActiveSub = UserSubscription::where('user_id', $creator->id)
                ->where('status', 'active')
                ->where('end_date', '>=', now())
                ->first();
            if (!$creatorActiveSub) {
                Auth::logout();
                return redirect()->route('login')->withErrors([
                    'email' => 'Your account cannot be used because your creator does not have an active subscription.'
                ]);
            }
        }

        // Check if user has an active subscription
        $activeSubscription = UserSubscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->first();

        // For managers created by another user, allow login if creator has active subscription
        if (!$activeSubscription && $user->role === 'manager' && $user->addby) {
            $creator = User::where('email', $user->addby)->first();
            $creatorActiveSub = $creator ? UserSubscription::where('user_id', $creator->id)
                ->where('status', 'active')
                ->where('end_date', '>=', now())
                ->first() : null;
            if ($creatorActiveSub) {
                // Allow login, skip this check
            } else {
                Auth::logout();
                return redirect()->route('plan_pricing')->withErrors([
                    'email' => 'You do not have an active subscription. Please subscribe to a plan to continue.',
                ])->with('redirect_to_plans', true);
            }
        } elseif (!$activeSubscription) {
            Auth::logout();
            return redirect()->route('plan_pricing')->withErrors([
                'email' => 'You do not have an active subscription. Please subscribe to a plan to continue.',
            ])->with('redirect_to_plans', true);
        }

        // Log user login activity
        \App\Helpers\ActivityLogger::log('login', 'User logged in via AuthenticatedSessionController');

        // Get the authenticated user's role
        $authUserRole = $user->role;

        if($authUserRole === 'superadmin'){
            return redirect()->intended(route('superadmin', absolute: false));
        } elseif($authUserRole === 'manager'){
            return redirect()->intended(route('manager', absolute: false));
        } elseif($authUserRole === 'businessowner'){
            return redirect()->intended(route('businessdashboard', absolute: false));
        } elseif($authUserRole === 'staff'){
            return redirect()->intended(route('dashboard', absolute: false));
        }

        return redirect()->intended('/');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $shouldRedirectToPricing = false;

        // Check subscription status before logout
        if ($user) {
            $activeSubscription = UserSubscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->where('end_date', '>=', now())
                ->first();
            
            // If no active subscription, check if email is in signup_requests
            if (!$activeSubscription) {
                $signupRequest = SignupRequest::where('email', $user->email)
                    ->where('is_used', true)
                    ->first();
                
                // Only redirect to pricing if email exists in signup_requests
                $shouldRedirectToPricing = (bool) $signupRequest;
            }
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // Redirect based on subscription status and signup verification
        if ($shouldRedirectToPricing) {
            return redirect()->route('plan_pricing')
                ->with('info', 'Your subscription has expired. Please choose a plan to continue.');
        }

        return redirect('/');
    }
}
