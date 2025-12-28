<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('staff.auth.login');
    }

    // Handle the login request
    public function login(Request $request)
    {

        // Validate the login form data
        $credentials = $request->only('email', 'password');
        if (Auth::guard('staff')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/staff/dashboard');
        }
        return back()->withErrors([
            'email' => 'Invalid credentials or inactive account.',
        ])->withInput($request->only('email', 'remember'));
    }

    public function logout(Request $request)
    {
        Auth::guard('staff')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/staff/login');
    }
}
