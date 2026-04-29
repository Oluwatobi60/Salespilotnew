<?php

namespace App\Http\Controllers\Welcome;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Welcome\SignupRequest;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use App\Mail\SubscriptionActivated;
use App\Mail\SetupPassword;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Carbon\Carbon;

class SignupController extends Controller
{
    public function index()
    {
        return view('sign_up');
    }

    /**
     * Check if email has been verified
     */
    public function checkVerification(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $signupRequest = SignupRequest::where('email', $validated['email'])
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$signupRequest) {
            return response()->json(['verified' => false]);
        }

        return response()->json([
            'verified' => $signupRequest->is_used == 1,
            'email' => $signupRequest->email
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        // Check if email is already registered as a user
        if (User::where('email', $validated['email'])->exists()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This email is already registered. Please login instead.',
                ], 400);
            }
            return redirect()->back()->with('error', 'This email is already registered. Please login instead.');
        }

        // Check for existing signup request
        $existingRequest = SignupRequest::where('email', $validated['email'])
            ->orderBy('created_at', 'desc')
            ->first();

        // If exists and not used and not expired, return error
        if ($existingRequest && !$existingRequest->is_used && Carbon::now()->lessThan($existingRequest->token_expires_at)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'A verification link has already been sent to this email. Please check your inbox or wait for it to expire.',
                    'expires_at' => $existingRequest->token_expires_at->toIso8601String(),
                ], 400);
            }
            return redirect()->back()->with('error', 'A verification link has already been sent to this email. Please check your inbox.');
        }

        // Generate a unique token
        $token = Str::random(64);

        // Create or update signup request with token and expiration
        if ($existingRequest) {
            // Update existing request
            $existingRequest->update([
                'token' => $token,
                'token_expires_at' => Carbon::now()->addMinutes(30),
                'is_used' => false,
            ]);
            $signupRequest = $existingRequest;
        } else {
            // Create new signup request
            $signupRequest = SignupRequest::create([
                'email' => $validated['email'],
                'token' => $token,
                'token_expires_at' => Carbon::now()->addMinutes(30),
                'is_used' => false,
            ]);
        }

        // Send email with token
        $this->sendTokenEmail($signupRequest);

        // Return JSON response if requested via AJAX
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'A verification token has been sent to your email. Please check your inbox.',
                'expires_at' => $signupRequest->token_expires_at->toIso8601String(),
            ]);
        }

        return redirect()->back()->with('success', 'A verification token has been sent to your email. Please check your inbox.');
    }

    /**
     * Resend verification token
     */
    public function resend(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        // Check if email is already registered
        if (User::where('email', $validated['email'])->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'This email is already registered. Please login instead.',
            ], 400);
        }

        // Find the most recent signup request for this email
        $signupRequest = SignupRequest::where('email', $validated['email'])
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$signupRequest) {
            return response()->json([
                'success' => false,
                'message' => 'No verification request found for this email. Please start over.',
            ], 404);
        }

        // Check if already used
        if ($signupRequest->is_used) {
            return response()->json([
                'success' => false,
                'message' => 'This email has already been verified. Please complete your registration.',
            ], 400);
        }

        // Generate new token and update expiration
        $newToken = Str::random(64);
        $signupRequest->update([
            'token' => $newToken,
            'token_expires_at' => Carbon::now()->addMinutes(30),
        ]);

        // Send new email
        $this->sendTokenEmail($signupRequest);

        return response()->json([
            'success' => true,
            'message' => 'A new verification link has been sent to your email.',
            'expires_at' => $signupRequest->token_expires_at->toIso8601String(),
        ]);
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

        // Clear any previous session data and set verified status
        session()->forget('verificationToken');

        // Redirect to registration with verified email
        return redirect()->route('register')->with([
            'success' => 'Email verified! Please complete your registration.',
            'email_verified' => true,
            'signup_email' => $signupRequest->email
        ]);
    }


    public function plan_pricing()
    {
        $plans = SubscriptionPlan::active()->get();

        // Check if user has an active subscription
        $activeSubscription = null;
        if (Auth::check()) {
            $activeSubscription = UserSubscription::where('user_id', Auth::id())
                ->where('status', 'active')
                ->where('end_date', '>=', now())
                ->with('subscriptionPlan')
                ->first();
        }

        return view('plan_pricing', compact('plans', 'activeSubscription'));
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
        $paystackPublicKey = config('services.paystack.public_key');

        return view('payment', compact('plan', 'duration', 'pricing', 'paystackPublicKey'));
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

        // Cancel any existing active subscriptions before creating new one
        $this->cancelExistingSubscriptions(Auth::id());

        $plan = SubscriptionPlan::find(session('selected_plan'));
        $duration = (int) session('selected_duration');
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

        // Generate commission for BRM if customer has one and paid amount is > 0
        if (Auth::user()->brm_id && $subscription->amount_paid > 0) {
            \App\Http\Controllers\Brm\BrmCommissionController::generateCommission($subscription);
        }

        // Send activation email
        Mail::to(Auth::user()->email)->send(new SubscriptionActivated(Auth::user(), $subscription));

        // Send set-password email
        $this->sendPasswordSetupEmail(Auth::user());

        // Log the user out — they must set password before logging in
        $userEmail = Auth::user()->email;
        Auth::logout();
        session()->forget(['selected_plan', 'selected_duration', 'pricing']);

        return redirect()->route('signup.account.created')->with('setup_email', $userEmail);
    }

    /**
     * Activate free plan immediately
     */
    protected function activateFreePlan($plan, $duration)
    {
        // Cancel any existing active subscriptions before creating new one
        $this->cancelExistingSubscriptions(Auth::id());

        $duration = (int) $duration;

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

        // Note: No commission generated for free trials (amount_paid = 0)

        // Send activation email
        Mail::to(Auth::user()->email)->send(new SubscriptionActivated(Auth::user(), $subscription));

        // Send set-password email
        $this->sendPasswordSetupEmail(Auth::user());

        // Log the user out — they must set password before logging in
        $userEmail = Auth::user()->email;
        Auth::logout();
        session()->forget(['selected_plan', 'selected_duration', 'pricing']);

        return redirect()->route('signup.account.created')->with('setup_email', $userEmail);
    }

    /**
     * Show the "account created" confirmation page after payment.
     */
    public function accountCreated()
    {
        $email = session('setup_email');
        return view('auth.account-created', compact('email'));
    }

    /**
     * Get user dashboard route based on role
     */
    protected function getUserDashboardRoute()
    {
        $user = Auth::user();

        switch ($user->role) {
            case 'superadmin':
                return route('superadmin');
            case 'manager':
                return route('manager');
            case 'businessowner':
                return route('businessdashboard');
            case 'staff':
                return route('dashboard');
            default:
                return route('manager');
        }
    }

    /**
     * Generate a password-setup token for the user and send the email
     */
    protected function sendPasswordSetupEmail(User $user): void
    {
        $token = Str::random(64);

        $user->forceFill([
            'password_setup_token'      => $token,
            'password_setup_expires_at' => Carbon::now()->addHours(48),
            'password_set'              => false,
        ])->save();

        Mail::to($user->email)->send(new SetupPassword($user, $token));
    }

    /**
     * Verify Paystack payment
     */
    public function verifyPaystackPayment(Request $request)
    {
        $reference = $request->query('reference');

        if (!$reference) {
            return redirect()->route('payment.show')->with('error', 'Invalid payment reference.');
        }

        // Verify payment with Paystack
        $url = "https://api.paystack.co/transaction/verify/{$reference}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . config('services.paystack.secret_key')
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response);

        if (!$result->status || $result->data->status !== 'success') {
            return redirect()->route('payment.show')->with('error', 'Payment verification failed. Please try again.');
        }

        if (!session('selected_plan')) {
            return redirect()->route('plan_pricing')->with('error', 'Session expired. Please select a plan again.');
        }

        // Cancel any existing active subscriptions before creating new one
        $this->cancelExistingSubscriptions(Auth::id());

        $plan = SubscriptionPlan::find(session('selected_plan'));
        $duration = (int) session('selected_duration');
        $pricing = session('pricing');

        // Verify amount matches
        $expectedAmount = $pricing['discounted_price'] * 100; // Paystack uses kobo
        if ($result->data->amount != $expectedAmount) {
            return redirect()->route('payment.show')
                ->with('error', 'Payment amount mismatch. Please contact support.');
        }

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
            'payment_reference' => $reference,
        ]);

        // Generate commission for BRM if customer has one and paid amount is > 0
        if (Auth::user()->brm_id && $subscription->amount_paid > 0) {
            \App\Http\Controllers\Brm\BrmCommissionController::generateCommission($subscription);
        }

        // Send activation email
        Mail::to(Auth::user()->email)->send(new SubscriptionActivated(Auth::user(), $subscription));

        // Send set-password email
        $this->sendPasswordSetupEmail(Auth::user());

        // Log the user out — they must set password before logging in
        $userEmail = Auth::user()->email;
        Auth::logout();
        session()->forget(['selected_plan', 'selected_duration', 'pricing']);

        return redirect()->route('signup.account.created')->with('setup_email', $userEmail);
    }

    /**
     * Cancel existing active subscriptions for a user (for upgrades)
     */
    protected function cancelExistingSubscriptions($userId)
    {
        $activeSubscriptions = UserSubscription::where('user_id', $userId)
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->get();

        foreach ($activeSubscriptions as $subscription) {
            // Mark as cancelled and set end date to now
            $subscription->update([
                'status' => 'cancelled',
                'end_date' => now(),
                'cancellation_reason' => 'Upgraded to a new plan',
                'cancelled_at' => now(),
            ]);

            // Log the cancellation
            Log::info('Subscription cancelled for upgrade', [
                'user_id' => $userId,
                'subscription_id' => $subscription->id,
                'old_plan' => $subscription->subscriptionPlan->name ?? 'N/A',
                'cancelled_at' => now(),
            ]);
        }

        return $activeSubscriptions->count();
    }
}
