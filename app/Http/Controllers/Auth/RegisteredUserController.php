<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Brm;
use App\Models\Welcome\SignupRequest;
use App\Mail\BrmCustomerRegistration;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        // Get the verified email from session or from the latest used signup request
        $signupEmail = session('signup_email');

        // If no session email, try to get from the most recent used signup request
        if (!$signupEmail) {
            $latestSignup = SignupRequest::where('is_used', true)
                ->orderBy('updated_at', 'desc')
                ->first();

            if ($latestSignup) {
                $signupEmail = $latestSignup->email;
            }
        }

        return view('auth.register', ['signup_email' => $signupEmail]);
    }

    /**
     * Verify referral code via AJAX
     */
    public function verifyReferralCode(Request $request)
    {
        $request->validate([
            'referral_code' => 'required|string|max:255',
        ]);

        $code = trim($request->referral_code);

        // Find the BRM by referral code
        $brm = Brm::where('referral_code', $code)
            ->where('status', 1) // Only active BRMs
            ->first();

            // Return JSON response indicating validity and BRM details if valid
        if ($brm) {
            return response()->json([
                'valid' => true,
                'message' => "Valid referral code for {$brm->name}",
                'brm_id' => $brm->id,
                'brm_name' => $brm->name,
            ]);
        }

        return response()->json([
            'valid' => false,
            'message' => 'Referral code not found or BRM is inactive',
        ], 422);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name'   => 'required|string|max:255',
            'surname'      => 'required|string|max:255',
            'other_name'   => 'nullable|string|max:255',
            'business_name'=> 'required|string|max:255',
            'branch_name'  => 'required|string|max:255',
            'business_logo'=> 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'state'        => 'required|string|max:255',
            'local_govt'   => 'required|string|max:255',
            'address'      => 'required|string|max:1000',
            'phone_number' => 'required|string|size:11',
            'referral_code'=> 'nullable|string|max:255|exists:brms,referral_code',
            'email'        => 'required|string|email|max:255|unique:users',
            'role'         => 'required|string|in:manager',
        ]);

        // Handle business logo upload
        $businessLogoPath = null;
        if ($request->hasFile('business_logo')) {
            $businessLogoPath = $request->file('business_logo')->store('business_logos', 'public');
        }

        // Find BRM by referral code if provided
        $brmId = null;
        if (!empty($validated['referral_code'])) {
            $brm = Brm::where('referral_code', $validated['referral_code'])
                ->where('status', 1) // Only active BRMs
                ->first();

            if ($brm) {
                $brmId = $brm->id;
            }
        }

        $user = User::create([
            'first_name'    => $validated['first_name'],
            'surname'       => $validated['surname'],
            'other_name'    => $validated['other_name'],
            'business_name' => $validated['business_name'],
            'branch_name'   => $validated['branch_name'],
            'business_logo' => $businessLogoPath,
            'state'         => $validated['state'],
            'local_govt'    => $validated['local_govt'],
            'address'       => $validated['address'],
            'phone_number'  => $validated['phone_number'],
            'referral_code' => $validated['referral_code'],
            'brm_id'        => $brmId, // Assign the BRM ID if valid referral code was provided
            'email'         => $validated['email'],
            'password'      => Hash::make(Str::random(40)), // temporary — user sets via email link
            'role'          => $validated['role'],
            'status'        => 1, // Automatically activate upon registration
            'password_set'  => false,
        ]);

        // Send notification email to BRM if customer was referred by them
        if ($brmId) {
            $brm = Brm::find($brmId);
            if ($brm && $brm->email) {
                try {
                    Mail::to($brm->email)->send(new BrmCustomerRegistration($user, $brm));
                } catch (\Exception $e) {
                    // Log the error but don't block registration
                    Log::error('Failed to send BRM notification email', [
                        'brm_id' => $brmId,
                        'customer_email' => $user->email,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('plan_pricing')->with('success', 'Registration successful! Please select a plan.');
    }
}
