<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SetPasswordController extends Controller
{
    /**
     * Show the set-password form for a given token.
     */
    public function showForm(string $token)
    {
        $user = User::where('password_setup_token', $token)
            ->where('password_set', false)
            ->first();

        if (! $user) {
            return redirect()->route('login')
                ->with('error', 'This password-setup link is invalid or has already been used.');
        }

        if ($user->password_setup_expires_at && Carbon::now()->greaterThan($user->password_setup_expires_at)) {
            return redirect()->route('login')
                ->with('error', 'This password-setup link has expired. Please contact support to request a new one.');
        }

        return view('auth.set-password', compact('token'));
    }

    /**
     * Process the new password submission.
     */
    public function store(Request $request, string $token)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::where('password_setup_token', $token)
            ->where('password_set', false)
            ->first();

        if (! $user) {
            return redirect()->route('login')
                ->with('error', 'This password-setup link is invalid or has already been used.');
        }

        if ($user->password_setup_expires_at && Carbon::now()->greaterThan($user->password_setup_expires_at)) {
            return redirect()->route('login')
                ->with('error', 'This password-setup link has expired. Please contact support to request a new one.');
        }

        $user->forceFill([
            'password'                  => Hash::make($request->password),
            'password_set'              => true,
            'password_setup_token'      => null,
            'password_setup_expires_at' => null,
        ])->save();

        return redirect()->route('login')
            ->with('success', 'Password created successfully! You can now log in to your SalesPilot account.');
    }
}
