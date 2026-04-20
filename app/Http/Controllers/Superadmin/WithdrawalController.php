<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use App\Models\Brm;
use Illuminate\Http\Request;
use Carbon\Carbon;

class WithdrawalController extends Controller
{
    /**
     * List all withdrawal requests
     */
    public function index(Request $request)
    {
        $query = Withdrawal::with(['brm', 'bankAccount'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by BRM
        if ($request->filled('brm_id')) {
            $query->where('brm_id', $request->brm_id);
        }

        // Search by BRM name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('brm', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Date range filter
        if ($request->filled('from') && $request->filled('to')) {
            $from = Carbon::parse($request->from)->startOfDay();
            $to = Carbon::parse($request->to)->endOfDay();
            $query->whereBetween('created_at', [$from, $to]);
        }

        $withdrawals = $query->paginate(25)->withQueryString();

        // Statistics
        $stats = [
            'total' => Withdrawal::count(),
            'pending' => Withdrawal::where('status', 'pending')->count(),
            'approved' => Withdrawal::where('status', 'approved')->count(),
            'paid' => Withdrawal::where('status', 'paid')->count(),
            'rejected' => Withdrawal::where('status', 'rejected')->count(),
            'pending_amount' => Withdrawal::where('status', 'pending')->sum('amount'),
            'approved_amount' => Withdrawal::where('status', 'approved')->sum('amount'),
            'paid_amount' => Withdrawal::where('status', 'paid')->sum('amount'),
            'rejected_amount' => Withdrawal::where('status', 'rejected')->sum('amount'),
            'total_amount' => Withdrawal::sum('amount'),
        ];

        $brms = Brm::orderBy('name')->get(['id', 'name']);

        return view('superadmin.withdrawals.index', compact('withdrawals', 'stats', 'brms'));
    }

    /**
     * Show detailed withdrawal view
     */
    public function show(Withdrawal $withdrawal)
    {
        $withdrawal->load(['brm', 'bankAccount']);

        return view('superadmin.withdrawals.show', compact('withdrawal'));
    }

    /**
     * Approve withdrawal request (deduct from wallet)
     */
    public function approve(Withdrawal $withdrawal, Request $request)
    {
        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'Only pending withdrawal requests can be approved.');
        }

        $notes = $request->input('notes');
        $withdrawal->approve($notes);

        return back()->with('success', "Withdrawal #" . $withdrawal->id . " (₦" . number_format((float)$withdrawal->amount, 2) . ") has been approved. Amount deducted from wallet. Please make the bank transfer.");
    }

    /**
     * Mark withdrawal as paid (after bank transfer complete)
     */
    public function markAsPaid(Withdrawal $withdrawal, Request $request)
    {
        if ($withdrawal->status !== 'approved') {
            return back()->with('error', 'Only approved withdrawals can be marked as paid.');
        }

        $notes = $request->input('notes');
        $withdrawal->markAsPaid($notes);

        return back()->with('success', "Withdrawal #" . $withdrawal->id . " has been marked as paid. Bank transfer confirmed.");
    }

    /**
     * Reject withdrawal (return amount to wallet balance)
     */
    public function reject(Withdrawal $withdrawal, Request $request)
    {
        if (!in_array($withdrawal->status, ['pending', 'approved'])) {
            return back()->with('error', 'Cannot reject a paid or already rejected withdrawal.');
        }

        $notes = $request->input('notes');
        $withdrawal->reject($notes);

        return back()->with('error', "Withdrawal #" . $withdrawal->id . " has been rejected.");
    }

    /**
     * Bulk approve withdrawals
     */
    public function bulkApprove(Request $request)
    {
        $ids = $request->input('withdrawal_ids', []);

        if (empty($ids)) {
            return back()->with('error', 'Please select withdrawals to approve.');
        }

        $count = 0;
        $totalAmount = 0;

        foreach ($ids as $id) {
            $withdrawal = Withdrawal::find($id);
            if ($withdrawal && $withdrawal->status === 'pending') {
                $withdrawal->approve();
                $count++;
                $totalAmount += $withdrawal->amount;
            }
        }

        return back()->with('success', "$count withdrawal(s) approved. Total: ₦" . number_format($totalAmount, 2) . " deducted from wallets.");
    }

    /**
     * Bulk mark as paid
     */
    public function bulkMarkPaid(Request $request)
    {
        $ids = $request->input('withdrawal_ids', []);

        if (empty($ids)) {
            return back()->with('error', 'Please select withdrawals to mark as paid.');
        }

        $count = 0;
        $totalAmount = 0;

        foreach ($ids as $id) {
            $withdrawal = Withdrawal::find($id);
            if ($withdrawal && $withdrawal->status === 'approved') {
                $withdrawal->markAsPaid();
                $count++;
                $totalAmount += $withdrawal->amount;
            }
        }

        return back()->with('success', "$count withdrawal(s) marked as paid. Total: ₦" . number_format($totalAmount, 2));
    }
}
