<?php

namespace App\Http\Controllers\Brm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BrmController extends Controller
{
    public function showLogin()
    {
        return view('brm.auth.login');
    }

    /**
     * Handle the login request
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::guard('brms')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->route('brm.dashboard');
        }

        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Handle the logout request
     */
    public function logout(Request $request)
    {
        Auth::guard('brms')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('brm.login');
    }

    /**
     * Show the BRM dashboard
     */
    public function dashboard()
    {
        $brm = Auth::guard('brms')->user();

        // Get customer statistics
        $totalCustomers = $brm->customers()->count();
        $customersThisMonth = $brm->customers()
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Get recent customers (last 5)
        $recentCustomers = $brm->customers()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get customers with active subscriptions
        $activeSubscriptions = $brm->customers()
            ->whereHas('currentSubscription')
            ->count();

        // Get recent activity logs for BRM's customers
        $recentActivity = \App\Models\ActivityLog::whereIn('user_id', $brm->customers()->pluck('id'))
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();

        return view('brm.brmdashboard', compact(
            'brm',
            'totalCustomers',
            'customersThisMonth',
            'recentCustomers',
            'activeSubscriptions',
            'recentActivity'
        ));
    }

    /**
     * Show BRM customers list
     */
    public function customers()
    {
        $brm = Auth::guard('brms')->user();

        // Customer Statistics - ALL customers
        $totalCustomers = $brm->customers()->count();
        $activeCustomers = $brm->customers()->where('status', 1);
        $activeSubscriptions = $activeCustomers->clone()->whereHas('currentSubscription')->count();
        $customersThisMonth = $brm->customers()
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
        $inactiveCustomers = $brm->customers()->where('status', '!=', 1)->count();

        // Get paginated customers (only active) with subscription details
        $customers = $activeCustomers
            ->with(['currentSubscription.subscriptionPlan'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('brm.customers.index', compact(
            'brm',
            'customers',
            'totalCustomers',
            'activeSubscriptions',
            'customersThisMonth',
            'inactiveCustomers'
        ));
    }
}
