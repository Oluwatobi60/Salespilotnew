<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\SuperAdmin;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\ActivityLog;
use App\Models\Brm;
use App\Mail\SubscriptionExpiryReminder;
use App\Mail\BrmCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SuperAdminController extends Controller
{
    public function showSignup()
    {
        return view('superadmin.auth.signup');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:superadmins,email',
            'phone'    => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        SuperAdmin::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'phone'    => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('superadmin.login')
            ->with('success', 'Account created successfully. Please log in.');
    }

    public function showLogin()
    {
        return view('superadmin.auth.login');
    }

    // Handle the login request
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::guard('superadmin')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->route('superadmin');
        }

        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function dashboard()
    {
        $superadmin = Auth::guard('superadmin')->user();

        // Recent new business registrations
        $recentUsers = User::whereNull('addby')
            ->latest()
            ->take(5)
            ->get(['id', 'first_name', 'surname', 'email', 'business_name', 'created_at']);

        // Recent subscription payments
        $recentSubscriptions = UserSubscription::with('user:id,first_name,surname,email')
            ->whereIn('status', ['active', 'expired'])
            ->latest()
            ->take(5)
            ->get(['id', 'user_id', 'status', 'amount_paid', 'created_at']);

        // Recent meaningful activity logs
        $recentActivity = ActivityLog::with('user:id,first_name,surname,email')
            ->whereIn('action', ['Checkout completed', 'login', 'create_branch', 'create_category'])
            ->latest()
            ->take(8)
            ->get();

        // BRM stats
        $totalBrms  = Brm::count();
        $activeBrms = Brm::where('status', 1)->count();

        return view('superadmin.superadmin', compact(
            'superadmin', 'recentUsers', 'recentSubscriptions', 'recentActivity',
            'totalBrms', 'activeBrms'
        ));
    }

    public function logout(Request $request)
    {
        Auth::guard('superadmin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('superadmin.login');
    }

    public function customers(Request $request)
    {
        $search = $request->get('search');

        $customers = User::whereNull('addby')
            ->with(['currentSubscription.subscriptionPlan', 'brm'])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q2) use ($search) {
                    $q2->where('first_name', 'like', "%{$search}%")
                       ->orWhere('surname', 'like', "%{$search}%")
                       ->orWhere('business_name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(20);

        $activeBrms = Brm::where('status', 1)->orderBy('name')->get(['id', 'name']);

        return view('superadmin.customers', compact('customers', 'search', 'activeBrms'));
    }

    public function toggleCustomerStatus(Request $request, User $user)
    {
        $user->status = $user->status ? 0 : 1;
        $user->save();

        $label = $user->status ? 'activated' : 'deactivated';
        return back()->with('success', "Customer account has been {$label}.");
    }

    public function sendSubscriptionReminder(Request $request, User $user)
    {
        $subscription = $user->currentSubscription()->with('subscriptionPlan')->first();

        // Also check for most recent expired subscription if no active one
        if (!$subscription) {
            $subscription = UserSubscription::where('user_id', $user->id)
                ->with('subscriptionPlan')
                ->latest()
                ->first();
        }

        if (!$subscription) {
            return back()->with('error', "{$user->first_name} has no subscription record to reminder about.");
        }

        $daysLeft = $subscription->end_date
            ? (int) now()->startOfDay()->diffInDays($subscription->end_date, false)
            : 0;

        try {
            Mail::to($user->email)->send(new SubscriptionExpiryReminder($user, $subscription, max($daysLeft, 0)));
            return back()->with('success', "Subscription reminder sent to {$user->email}.");
        } catch (\Exception $e) {
            return back()->with('error', "Failed to send email: " . $e->getMessage());
        }
    }

    // ─── BRM ───────────────────────────────────────────────────────────────────

    public function brms(Request $request)
    {
        $search = $request->get('search');

        $brms = Brm::withCount('customers')
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('region', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(20);

        return view('superadmin.brms.index', compact('brms', 'search'));
    }

    public function createBrm()
    {
        return view('superadmin.brms.create');
    }

    public function storeBrm(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:brms,email',
            'phone'    => 'nullable|string|max:20',
            'address'  => 'nullable|string|max:255',
            'region'   => 'nullable|string|max:100',
            'referral_code' => 'nullable|string|max:6|unique:brms,referral_code',
            'notes'    => 'nullable|string|max:1000',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Store the plain password before it gets hashed
        $plainPassword = $validated['password'];

        // Generate unique referral code if not provided
        if (empty($validated['referral_code'])) {
            $validated['referral_code'] = $this->generateUniqueBrmCode();
        }

        // Create the BRM
        $brm = Brm::create($validated + ['status' => 1]);

        // Send welcome email with credentials
        try {
            Mail::to($brm->email)->send(new BrmCreated($brm, $plainPassword));
        } catch (\Exception $e) {
            // Log the error but don't block BRM creation
            Log::error('Failed to send BRM creation email: ' . $e->getMessage());
        }

        return redirect()->route('superadmin.brms')->with('success', 'BRM registered successfully. Welcome email sent to ' . $brm->email);
    }

    /**
     * Generate a unique 6-character alphanumeric code for BRM
     */
    private function generateUniqueBrmCode()
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code = '';

        do {
            $code = '';
            for ($i = 0; $i < 6; $i++) {
                $code .= $characters[rand(0, strlen($characters) - 1)];
            }
        } while (Brm::where('referral_code', $code)->exists());

        return $code;
    }

    public function editBrm(Brm $brm)
    {
        return view('superadmin.brms.edit', compact('brm'));
    }

    public function updateBrm(Request $request, Brm $brm)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:brms,email,' . $brm->id,
            'phone'    => 'nullable|string|max:20',
            'address'  => 'nullable|string|max:255',
            'region'   => 'nullable|string|max:100',
            'notes'    => 'nullable|string|max:1000',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = collect($validated)->except('password')->toArray();
        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $brm->update($data);

        return redirect()->route('superadmin.brms')->with('success', 'BRM updated successfully.');
    }

    public function toggleBrmStatus(Brm $brm)
    {
        $brm->status = $brm->status ? 0 : 1;
        $brm->save();
        $label = $brm->status ? 'activated' : 'deactivated';
        return back()->with('success', "BRM {$brm->name} has been {$label}.");
    }

    public function assignBrm(Request $request, User $user)
    {
        $request->validate([
            'brm_id' => 'nullable|exists:brms,id',
        ]);

        $user->brm_id = $request->brm_id ?: null;
        $user->save();

        return back()->with('success', 'BRM assignment updated.');
    }

    public function showUser(User $user)
    {
        $user->load(['currentSubscription.subscriptionPlan', 'brm']);
        $subscriptions = UserSubscription::where('user_id', $user->id)
            ->with('subscriptionPlan')
            ->latest()
            ->get();
        $activeBrms = Brm::where('status', 1)->orderBy('name')->get(['id', 'name', 'region']);
        return view('superadmin.users.show', compact('user', 'subscriptions', 'activeBrms'));
    }
}
