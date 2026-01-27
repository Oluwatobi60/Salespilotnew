<?php

namespace App\Http\Controllers\Welcome;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Welcome\SignupRequest;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use App\Mail\SubscriptionActivated;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SignupController extends Controller
{
    public function index()
    {
        return view('sign_up');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:signup_requests,email',
        ]);

        // Generate a unique token
        $token = Str::random(64);

        // Create signup request with token and expiration
        $signupRequest = SignupRequest::create([
            'email' => $validated['email'],
            'token' => $token,
            'token_expires_at' => Carbon::now()->addMinutes(30),
            'is_used' => false,
        ]);

        // Send email with token
        $this->sendTokenEmail($signupRequest);

        return redirect()->back()->with('success', 'A verification token has been sent to your email. Please check your inbox.');
    }

    /**
     * Send token to user's email
     */
    protected function sendTokenEmail($signupRequest)
    {
        $tokenUrl = route('signup.verify', ['token' => $signupRequest->token]);

        Mail::send('emails.signup_token', [
            'token' => $signupRequest->token,
            'tokenUrl' => $tokenUrl,
            'expiresIn' => 30,
        ], function ($message) use ($signupRequest) {
            $message->to($signupRequest->email)
                    ->subject('Your SalesPilot Signup Token');
        });
    }

    /**
     * Verify and use the token
     */
    public function verifyToken($token)
    {
        $signupRequest = SignupRequest::where('token', $token)->first();

        // Check if token exists
        if (!$signupRequest) {
            return redirect()->route('get_started')->with('error', 'Invalid token.');
        }

        // Check if token is already used
        if ($signupRequest->is_used) {
            return redirect()->route('get_started')->with('error', 'This token has already been used.');
        }

        // Check if token has expired
        if (Carbon::now()->greaterThan($signupRequest->token_expires_at)) {
            return redirect()->route('get_started')->with('error', 'This token has expired. Please request a new one.');
        }

        // Mark token as used
        $signupRequest->update(['is_used' => true]);

        // Redirect to registration with verified email
        return redirect()->route('register')->with([
            'success' => 'Token verified! Please complete your registration.',
            'signup_email' => $signupRequest->email
        ]);
    }


    public function plan_pricing()
    {
        $plans = SubscriptionPlan::active()->get();
        return view('plan_pricing', compact('plans'));
    }

    /**
     * Handle plan selection
     */
    public function selectPlan(Request $request)
    {
        $validated = $request->validate([
            'plan' => 'required|string|in:free,basic,standard,premium',
            'duration' => 'required|integer|in:1,3,6,12',
        ]);

        // Get the selected plan
        $plan = SubscriptionPlan::where('name', $validated['plan'])->first();

        if (!$plan) {
            return redirect()->back()->with('error', 'Invalid plan selected.');
        }

        // Calculate pricing
        $pricing = $plan->calculatePrice($validated['duration']);

        // Store in session for payment processing
        session([
            'selected_plan' => $plan->id,
            'selected_duration' => $validated['duration'],
            'pricing' => $pricing,
        ]);

        // If free plan or 7-day trial, activate immediately
        if ($validated['plan'] === 'free' || $pricing['discounted_price'] == 0) {
            return $this->activateFreePlan($plan, $validated['duration']);
        }

        // Redirect to payment page
        return redirect()->route('payment.show');
    }

    /**
     * Show payment page
     */
    public function showPayment()
    {
        if (!session('selected_plan')) {
            return redirect()->route('plan_pricing')->with('error', 'Please select a plan first.');
        }

        $plan = SubscriptionPlan::find(session('selected_plan'));
        $duration = session('selected_duration');
        $pricing = session('pricing');

        return view('payment', compact('plan', 'duration', 'pricing'));
    }

    /**
     * Process payment
     */
    public function processPayment(Request $request)
    {
        $validated = $request->validate([
            'payment_method' => 'required|string',
            'payment_reference' => 'nullable|string',
        ]);

        if (!session('selected_plan')) {
            return redirect()->route('plan_pricing')->with('error', 'Session expired. Please select a plan again.');
        }

        $plan = SubscriptionPlan::find(session('selected_plan'));
        $duration = session('selected_duration');
        $pricing = session('pricing');

        // Create subscription
        $subscription = UserSubscription::create([
            'user_id' => Auth::id(),
            'subscription_plan_id' => $plan->id,
            'duration_months' => $duration,
            'amount_paid' => $pricing['discounted_price'],
            'discount_percentage' => $pricing['discount_percentage'],
            'start_date' => Carbon::today(),
            'end_date' => Carbon::today()->addMonths($duration),
            'status' => 'active',
            'payment_reference' => $validated['payment_reference'] ?? 'FREE-' . Str::random(10),
        ]);

        // Send activation email
        Mail::to(Auth::user()->email)->send(new SubscriptionActivated(Auth::user(), $subscription));

        // Clear session
        session()->forget(['selected_plan', 'selected_duration', 'pricing']);

        return redirect()->route('manager')->with('success', 'Subscription activated successfully! Welcome to SalesPilot.');
    }

    /**
     * Activate free plan immediately
     */
    protected function activateFreePlan($plan, $duration)
    {
        $subscription = UserSubscription::create([
            'user_id' => Auth::id(),
            'subscription_plan_id' => $plan->id,
            'duration_months' => $duration,
            'amount_paid' => 0,
            'discount_percentage' => 0,
            'start_date' => Carbon::today(),
            'end_date' => $plan->trial_days > 0 ? Carbon::today()->addDays($plan->trial_days) : Carbon::today()->addMonths($duration),
            'status' => 'active',
            'payment_reference' => 'FREE-TRIAL-' . Str::random(10),
        ]);

        // Send activation email
        Mail::to(Auth::user()->email)->send(new SubscriptionActivated(Auth::user(), $subscription));

        // Clear session
        session()->forget(['selected_plan', 'selected_duration', 'pricing']);

        return redirect()->route('manager')->with('success', 'Free trial activated! Welcome to SalesPilot.');
    }
}
