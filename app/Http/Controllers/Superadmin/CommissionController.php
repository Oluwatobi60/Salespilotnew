<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Brm;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CommissionController extends Controller
{
    /**
     * List all commissions with filtering and approval
     */
    public function index(Request $request)
    {
        $query = Commission::with(['brm', 'user', 'userSubscription.subscriptionPlan'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by commission type
        if ($request->filled('type')) {
            $query->where('commission_type', $request->type);
        }

        // Filter by BRM
        if ($request->filled('brm_id')) {
            $query->where('brm_id', $request->brm_id);
        }

        // Search by BRM name or customer name/email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('brm', function ($brmQ) use ($search) {
                    $brmQ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas('user', function ($userQ) use ($search) {
                    $userQ->where('business_name', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('surname', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            });
        }

        // Date range filter
        if ($request->filled('from') && $request->filled('to')) {
            $from = Carbon::parse($request->from)->startOfDay();
            $to = Carbon::parse($request->to)->endOfDay();
            $query->whereBetween('created_at', [$from, $to]);
        }

        $commissions = $query->paginate(25)->withQueryString();

        // Statistics
        $stats = [
            'total' => Commission::count(),
            'pending' => Commission::where('status', 'pending')->count(),
            'approved' => Commission::where('status', 'approved')->count(),
            'paid' => Commission::where('status', 'paid')->count(),
            'rejected' => Commission::where('status', 'rejected')->count(),
            'pending_amount' => Commission::where('status', 'pending')->sum('commission_amount'),
            'total_amount' => Commission::sum('commission_amount'),
        ];

        $brms = Brm::orderBy('name')->get(['id', 'name']);

        return view('superadmin.commissions.index', compact('commissions', 'stats', 'brms'));
    }

    /**
     * Show detailed view of a commission
     */
    public function show(Commission $commission)
    {
        $commission->load(['brm', 'user', 'userSubscription.subscriptionPlan']);

        return view('superadmin.commissions.show', compact('commission'));
    }

    /**
     * Approve a commission
     */
    public function approve(Commission $commission)
    {
        if ($commission->status !== 'pending') {
            return back()->with('error', 'Only pending commissions can be approved.');
        }

        $commission->approve();

        return back()->with('success', "Commission #" . $commission->id . " has been approved. Amount: ₦" . number_format($commission->commission_amount, 2));
    }

    /**
     * Mark a commission as paid
     */
    public function markAsPaid(Commission $commission)
    {
        if (!in_array($commission->status, ['approved', 'paid'])) {
            return back()->with('error', 'Only approved commissions can be marked as paid.');
        }

        $commission->markAsPaid();

        return back()->with('success', "Commission #" . $commission->id . " has been marked as paid. Amount: ₦" . number_format($commission->commission_amount, 2));
    }

    /**
     * Reject a commission
     */
    public function reject(Commission $commission)
    {
        if ($commission->status !== 'pending') {
            return back()->with('error', 'Only pending commissions can be rejected.');
        }

        $commission->reject();

        return back()->with('error', "Commission #" . $commission->id . " has been rejected.");
    }

    /**
     * Bulk approve commissions
     */
    public function bulkApprove(Request $request)
    {
        $ids = $request->input('commission_ids', []);

        if (empty($ids)) {
            return back()->with('error', 'Please select commissions to approve.');
        }

        $count = Commission::whereIn('id', $ids)
            ->where('status', 'pending')
            ->each(function ($commission) {
                $commission->approve();
            })
            ->count();

        $totalAmount = Commission::whereIn('id', $ids)
            ->where('status', 'approved')
            ->sum('commission_amount');

        return back()->with('success', "$count commission(s) approved. Total: ₦" . number_format($totalAmount, 2));
    }

    /**
     * Bulk reject commissions
     */
    public function bulkReject(Request $request)
    {
        $ids = $request->input('commission_ids', []);

        if (empty($ids)) {
            return back()->with('error', 'Please select commissions to reject.');
        }

        Commission::whereIn('id', $ids)
            ->where('status', 'pending')
            ->each(function ($commission) {
                $commission->reject();
            });

        return back()->with('error', count($ids) . ' commission(s) rejected.');
    }

    /**
     * Get BRM commission summary
     */
    public function brmSummary(Brm $brm)
    {
        $commissions = $brm->commissions()
            ->with('user', 'userSubscription.subscriptionPlan')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total' => $brm->commissions()->sum('commission_amount'),
            'pending' => $brm->commissions()->where('status', 'pending')->sum('commission_amount'),
            'approved' => $brm->commissions()->where('status', 'approved')->sum('commission_amount'),
            'paid' => $brm->commissions()->where('status', 'paid')->sum('commission_amount'),
        ];

        return view('superadmin.commissions.brm-summary', compact('brm', 'commissions', 'stats'));
    }
}
