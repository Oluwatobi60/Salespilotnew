<?php

namespace App\Http\Controllers\Brm;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Brm;
use App\Models\BrmWalletAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BrmCommissionController extends Controller
{
    /**
     * Show BRM commissions overview
     */
    public function index()
    {
        $brm = Auth::guard('brms')->user();

        // Get or create wallet
        $wallet = $brm->wallet ?? $brm->wallet()->create([
            'balance' => 0,
            'total_earned' => 0,
            'total_withdrawn' => 0,
            'pending_approval' => 0,
        ]);

        // Commission statistics
        $totalCommissions = $brm->commissions()->sum('commission_amount');
        $pendingCommissions = $brm->commissions()->where('status', 'pending')->sum('commission_amount');
        $approvedCommissions = $brm->commissions()->where('status', 'approved')->sum('commission_amount');
        $paidCommissions = $brm->commissions()->where('status', 'paid')->sum('commission_amount');

        // Count of commissions by status
        $pendingCount = $brm->commissions()->where('status', 'pending')->count();
        $approvedCount = $brm->commissions()->where('status', 'approved')->count();
        $paidCount = $brm->commissions()->where('status', 'paid')->count();

        // Commission breakdown by type
        $referralCommissions = $brm->commissions()->where('commission_type', 'referral')->sum('commission_amount');
        $renewalCommissions = $brm->commissions()->where('commission_type', 'renewal')->sum('commission_amount');
        $upgradeCommissions = $brm->commissions()->where('commission_type', 'upgrade')->sum('commission_amount');

        // Recent commissions (last 10)
        $recentCommissions = $brm->commissions()
            ->with(['user', 'userSubscription.subscriptionPlan'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Commissions this month
        $commissionsThisMonth = $brm->commissions()
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('commission_amount');

        // Get wallet accounts
        $walletAccounts = $wallet->accounts()->get();

        // Prepare wallet stats for view
        $walletStats = [
            'balance' => $wallet->balance,
            'accounts' => $walletAccounts,
            'accountCount' => $walletAccounts->count(),
        ];

        return view('brm.commissions.index', compact(
            'brm',
            'wallet',
            'walletStats',
            'totalCommissions',
            'pendingCommissions',
            'approvedCommissions',
            'paidCommissions',
            'pendingCount',
            'approvedCount',
            'paidCount',
            'referralCommissions',
            'renewalCommissions',
            'upgradeCommissions',
            'recentCommissions',
            'commissionsThisMonth'
        ));
    }

    /**
     * Show commission history with pagination and filtering
     */
    public function history(Request $request)
    {
        $brm = Auth::guard('brms')->user();

        $query = $brm->commissions()
            ->with(['user', 'userSubscription.subscriptionPlan'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by commission type
        if ($request->filled('type')) {
            $query->where('commission_type', $request->type);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $commissions = $query->paginate(25);

        // Summary statistics
        $totalCommissions = $brm->commissions()->sum('commission_amount');
        $pendingCommissions = $brm->commissions()->where('status', 'pending')->sum('commission_amount');
        $paidCommissions = $brm->commissions()->where('status', 'paid')->sum('commission_amount');

        return view('brm.commissions.history', compact(
            'brm',
            'commissions',
            'totalCommissions',
            'pendingCommissions',
            'paidCommissions'
        ));
    }

    /**
     * Generate commission from a customer subscription
     * Called when a new subscription is created
     */
    public static function generateCommission($userSubscription)
    {
        $user = $userSubscription->user;

        // Check if BRM exists for this customer
        if (!$user->brm_id) {
            return null;
        }

        $commissionRate = 5; // 5% commission for referrals

        $commissionAmount = $userSubscription->amount_paid * ($commissionRate / 100);

        return Commission::create([
            'brm_id' => $user->brm_id,
            'user_id' => $user->id,
            'user_subscription_id' => $userSubscription->id,
            'subscription_amount' => $userSubscription->amount_paid,
            'commission_rate' => $commissionRate,
            'commission_amount' => $commissionAmount,
            'status' => 'pending',
            'commission_type' => 'referral',
            'notes' => "Commission from " . ($user->business_name ?? ($user->first_name . ' ' . $user->surname)) . " subscription",
        ]);
    }

    /**
     * Calculate and display detailed commission breakdown
     */
    public function breakdown()
    {
        $brm = Auth::guard('brms')->user();

        // Commission by customer
        $commissionsByCustomer = $brm->commissions()
            ->selectRaw('user_id, SUM(commission_amount) as total_commission, COUNT(*) as count')
            ->groupBy('user_id')
            ->with('user')
            ->orderByDesc('total_commission')
            ->get();

        // Commission by month
        $commissionsByMonth = $brm->commissions()
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(commission_amount) as total')
            ->groupBy('month')
            ->orderByDesc('month')
            ->limit(12)
            ->get();

        // Commission by type breakdown
        $byType = [
            'referral' => $brm->commissions()->where('commission_type', 'referral')->sum('commission_amount'),
            'renewal' => $brm->commissions()->where('commission_type', 'renewal')->sum('commission_amount'),
            'upgrade' => $brm->commissions()->where('commission_type', 'upgrade')->sum('commission_amount'),
        ];

        // Commission by status breakdown
        $byStatus = [
            'pending' => $brm->commissions()->where('status', 'pending')->sum('commission_amount'),
            'approved' => $brm->commissions()->where('status', 'approved')->sum('commission_amount'),
            'paid' => $brm->commissions()->where('status', 'paid')->sum('commission_amount'),
            'rejected' => $brm->commissions()->where('status', 'rejected')->sum('commission_amount'),
        ];

        return view('brm.commissions.breakdown', compact(
            'brm',
            'commissionsByCustomer',
            'commissionsByMonth',
            'byType',
            'byStatus'
        ));
    }

    /**
     * Get commission statistics for dashboard widgets
     */
    public static function getQuickStats($brmId)
    {
        return [
            'total' => Commission::where('brm_id', $brmId)->sum('commission_amount'),
            'pending' => Commission::where('brm_id', $brmId)->where('status', 'pending')->sum('commission_amount'),
            'approved' => Commission::where('brm_id', $brmId)->where('status', 'approved')->sum('commission_amount'),
            'paid' => Commission::where('brm_id', $brmId)->where('status', 'paid')->sum('commission_amount'),
            'thisMonth' => Commission::where('brm_id', $brmId)
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->sum('commission_amount'),
        ];
    }

    /**
     * API: Add bank account to wallet
     */
    public function addAccount(Request $request)
    {
        $brm = Auth::guard('brms')->user();
        $wallet = $brm->wallet ?? $brm->wallet()->create([
            'balance' => 0,
            'total_earned' => 0,
            'total_withdrawn' => 0,
            'pending_approval' => 0,
        ]);

        $validated = $request->validate([
            'account_number' => 'required|string|size:10',
            'account_name' => 'required|string|max:100',
            'bank_code' => 'required|string',
            'bank_name' => 'required|string|max:100',
        ]);

        try {
            // Check if account already exists
            $existingAccount = BrmWalletAccount::where('brm_id', $brm->id)
                ->where('account_number', $validated['account_number'])
                ->first();

            if ($existingAccount) {
                return response()->json([
                    'success' => false,
                    'message' => 'This account is already saved.',
                ], 422);
            }

            // Create new account
            $account = BrmWalletAccount::create([
                'brm_wallet_id' => $wallet->id,
                'brm_id' => $brm->id,
                'account_number' => $validated['account_number'],
                'account_name' => $validated['account_name'],
                'bank_code' => $validated['bank_code'],
                'bank_name' => $validated['bank_name'],
                'is_primary' => false,
                'is_verified' => true,
                'verified_at' => now(),
            ]);

            // Set as primary if first account
            if ($wallet->accounts()->count() === 1) {
                $account->update(['is_primary' => true]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Account added successfully!',
                'account' => $account,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error adding account: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API: Get wallet accounts
     */
    public function getAccounts()
    {
        $brm = Auth::guard('brms')->user();

        $accounts = $brm->wallet->accounts()->get();

        return response()->json([
            'success' => true,
            'accounts' => $accounts,
        ]);
    }

    /**
     * API: Process withdrawal request
     */
    /**
     * Request withdrawal - creates a withdrawal request for superadmin approval
     */
    public function requestWithdrawal(Request $request)
    {
        $brm = Auth::guard('brms')->user();
        $wallet = $brm->wallet;

        $validated = $request->validate([
            'amount' => 'required|numeric|min:100',
            'account_id' => 'required|exists:brm_wallet_accounts,id',
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            // Check if account belongs to this BRM
            $account = BrmWalletAccount::where('id', $validated['account_id'])
                ->where('brm_id', $brm->id)
                ->firstOrFail();

            $amount = $validated['amount'];

            // Check balance
            if ($wallet->balance < $amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient balance. Available: ₦' . number_format($wallet->balance, 2),
                ], 422);
            }

            // Create withdrawal request
            $withdrawal = \App\Models\Withdrawal::create([
                'brm_id' => $brm->id,
                'brm_wallet_account_id' => $account->id,
                'amount' => $amount,
                'status' => 'pending',
                'notes' => $validated['reason'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Withdrawal request submitted successfully! Awaiting superadmin approval.',
                'new_balance' => $wallet->balance,
                'withdrawal_id' => $withdrawal->id,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing withdrawal: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get BRM withdrawal requests
     */
    public function getWithdrawals()
    {
        $brm = Auth::guard('brms')->user();

        $withdrawals = $brm->withdrawals()
            ->with('bankAccount')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'withdrawals' => $withdrawals,
        ]);
    }
}

