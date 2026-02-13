<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Welcome\SignupRequest;
use App\Models\UserSubscription;
use App\Models\User;
use App\Models\Branch\Branch;
use Carbon\Carbon;

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
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginField = $request->input('login');
        $password = $request->input('password');

        // Determine if login is email or staffsid
        $fieldType = filter_var($loginField, FILTER_VALIDATE_EMAIL) ? 'email' : 'staffsid';

        $credentials = [
            $fieldType => $loginField,
            'password' => $password,
        ];

        if (Auth::guard('staff')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            // Get the authenticated staff
            $staff = Auth::guard('staff')->user();

            // Prevent login if status is 0 or not active
            if (!$staff->status || $staff->status == 0 || $staff->status === 'Inactive') {
                Auth::guard('staff')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors([
                    'login' => 'Your account is disabled. Please contact your manager.',
                ])->withInput($request->only('login'));
            }

            // Check if staff is assigned to any active branch
            $activeBranches = $staff->branches()->where('status', 1)->count();
            if ($activeBranches === 0) {
                Auth::guard('staff')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors([
                    'login' => 'Your assigned branch is currently inactive. Please contact your manager.',
                ])->withInput($request->only('login'));
            }

            // Verify manager's subscription status
            if ($staff->manager_email) {
                // Find the user by manager email
                $manager = User::where('email', $staff->manager_email)->first();

                if ($manager) {
                    // Check if manager has an active subscription
                    $subscription = UserSubscription::where('user_id', $manager->id)
                        ->where('status', 'active')
                        ->where('end_date', '>=', Carbon::today())
                        ->first();

                    if (!$subscription) {
                        // Manager's subscription is expired or inactive
                        Auth::guard('staff')->logout();
                        $request->session()->invalidate();
                        $request->session()->regenerateToken();

                        return back()->withErrors([
                            'login' => 'Access denied. Your manager\'s subscription has expired. Please contact your manager to renew the subscription.',
                        ])->withInput($request->only('login'));
                    }
                } else {
                    // Manager not found
                    Auth::guard('staff')->logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return back()->withErrors([
                        'login' => 'Access denied. Manager account not found. Please contact support.',
                    ])->withInput($request->only('login'));
                }
            }

            // Log staff login activity
            $details = [
                'staff_id' => $staff ? $staff->staffsid : null,
                'email' => $staff ? $staff->email : $loginField,
                'device' => $request->header('User-Agent'),
                'ip_address' => $request->ip(),
            ];
            \App\Helpers\ActivityLogger::log('login', json_encode($details));
            return redirect()->intended('/staff/dashboard');
        }
        return back()->withErrors([
            'login' => 'Invalid credentials or inactive account.',
        ])->withInput($request->only('login', 'remember'));
    }

    public function logout(Request $request)
    {
        Auth::guard('staff')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/staff/login');
    }
}
