<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\UserSubscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RevenueController extends Controller
{
    public function index(Request $request)
    {
        // Date range defaults: last 30 days
        $from = $request->filled('from')
            ? Carbon::parse($request->from)->startOfDay()
            : Carbon::now()->subDays(29)->startOfDay();

        $to = $request->filled('to')
            ? Carbon::parse($request->to)->endOfDay()
            : Carbon::now()->endOfDay();

        $planId = $request->plan_id;

        // ── Summary stats for the selected period ─────────────────────────
        $baseQuery = UserSubscription::whereBetween('created_at', [$from, $to]);

        if ($planId) {
            $baseQuery->where('subscription_plan_id', $planId);
        }

        $periodRevenue       = (clone $baseQuery)->sum('amount_paid');
        $periodTransactions  = (clone $baseQuery)->count();
        $periodAvg           = $periodTransactions > 0 ? $periodRevenue / $periodTransactions : 0;

        // Previous period for comparison (same length)
        $days       = max(1, $from->diffInDays($to) + 1);
        $prevFrom   = $from->copy()->subDays($days);
        $prevTo     = $from->copy()->subSecond();
        $prevRevenue = UserSubscription::whereBetween('created_at', [$prevFrom, $prevTo])
            ->when($planId, fn($q) => $q->where('subscription_plan_id', $planId))
            ->sum('amount_paid');

        $revenueChange = $prevRevenue > 0
            ? round((($periodRevenue - $prevRevenue) / $prevRevenue) * 100, 1)
            : null;

        // ── Daily revenue for chart ────────────────────────────────────────
        $daily = UserSubscription::selectRaw('DATE(created_at) as date, SUM(amount_paid) as total')
            ->whereBetween('created_at', [$from, $to])
            ->when($planId, fn($q) => $q->where('subscription_plan_id', $planId))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Fill every day in range (including zero-revenue days)
        $chartLabels = [];
        $chartData   = [];
        $cursor = $from->copy()->startOfDay();
        while ($cursor <= $to) {
            $key           = $cursor->toDateString();
            $chartLabels[] = $cursor->format('M d');
            $chartData[]   = (float) ($daily[$key]->total ?? 0);
            $cursor->addDay();
        }

        // ── Revenue by plan ───────────────────────────────────────────────
        $byPlan = UserSubscription::selectRaw('subscription_plan_id, SUM(amount_paid) as total, COUNT(*) as count')
            ->with('subscriptionPlan:id,name')
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('subscription_plan_id')
            ->orderByDesc('total')
            ->get();

        // ── Transaction list (paginated) ──────────────────────────────────
        $transactions = UserSubscription::with(['user:id,first_name,surname,email', 'subscriptionPlan:id,name'])
            ->whereBetween('created_at', [$from, $to])
            ->when($planId, fn($q) => $q->where('subscription_plan_id', $planId))
            ->latest()
            ->paginate(20);

        $plans = SubscriptionPlan::orderBy('name')->get(['id', 'name']);

        return view('superadmin.revenue.index', compact(
            'from', 'to', 'planId', 'plans',
            'periodRevenue', 'periodTransactions', 'periodAvg',
            'prevRevenue', 'revenueChange',
            'chartLabels', 'chartData',
            'byPlan', 'transactions'
        ));
    }
}
