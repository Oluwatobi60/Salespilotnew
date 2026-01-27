<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Welcome\SignupRequest;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
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
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'other_name' => 'nullable|string|max:255',
            'business_name' => 'required|string|max:255',
            'branch_name' => 'required|string|max:255',
            'business_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'state' => 'required|string|max:255',
            'local_govt' => 'required|string|max:255',
            'address' => 'required|string|max:1000',
            'phone_number' => 'required|string|size:11',
            'referral_code' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|string|in:manager',
        ]);

        // Handle business logo upload
        $businessLogoPath = null;
        if ($request->hasFile('business_logo')) {
            $businessLogoPath = $request->file('business_logo')->store('business_logos', 'public');
        }

        $user = User::create([
            'first_name' => $validated['first_name'],
            'surname' => $validated['surname'],
            'other_name' => $validated['other_name'],
            'business_name' => $validated['business_name'],
            'branch_name' => $validated['branch_name'],
            'business_logo' => $businessLogoPath,
            'state' => $validated['state'],
            'local_govt' => $validated['local_govt'],
            'address' => $validated['address'],
            'phone_number' => $validated['phone_number'],
            'referral_code' => $validated['referral_code'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('plan_pricing')->with('success', 'Registration successful! Please select a plan.');
    }
}
