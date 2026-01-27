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

        if (!$signupRequest) {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Your email has not been verified. Please complete the signup process first.',
            ]);
        }

        // Check if user has an active subscription
        $activeSubscription = UserSubscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->first();

        if (!$activeSubscription) {
            Auth::logout();
            return redirect()->route('login')->withErrors([
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
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
