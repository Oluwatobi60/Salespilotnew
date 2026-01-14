<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CartItem;
use Carbon\Carbon;

class StaffSalesController extends Controller
{
      public function staff_sales(Request $request)
    {
        $query = CartItem::where('status', 'completed');

        // Apply staff filter
        if ($request->filled('staff_id')) {
            $staffId = $request->staff_id;
            $query->where(function($q) use ($staffId) {
                $q->where('staff_id', $staffId)
                  ->orWhere('user_id', $staffId);
            });
        }

        // Apply date range filter
        if ($request->filled('date_range')) {
            $dateRange = $request->date_range;
            $startDate = null;
            $endDate = null;

            switch ($dateRange) {
                case 'today':
                    $startDate = Carbon::today();
                    $endDate = Carbon::today()->endOfDay();
                    break;
                case 'yesterday':
                    $startDate = Carbon::yesterday();
                    $endDate = Carbon::yesterday()->endOfDay();
                    break;
                case 'last7':
                    $startDate = Carbon::today()->subDays(6);
                    $endDate = Carbon::today()->endOfDay();
                    break;
                case 'last30':
                    $startDate = Carbon::today()->subDays(29);
                    $endDate = Carbon::today()->endOfDay();
                    break;
                case 'thisMonth':
                    $startDate = Carbon::now()->startOfMonth();
                    $endDate = Carbon::now()->endOfMonth();
                    break;
                case 'lastMonth':
                    $startDate = Carbon::now()->subMonth()->startOfMonth();
                    $endDate = Carbon::now()->subMonth()->endOfMonth();
                    break;
                case 'custom':
                    if ($request->filled('start_date')) {
                        $startDate = Carbon::parse($request->start_date)->startOfDay();
                    }
                    if ($request->filled('end_date')) {
                        $endDate = Carbon::parse($request->end_date)->endOfDay();
                    }
                    break;
            }

            if ($startDate) {
                $query->where('created_at', '>=', $startDate);
            }
            if ($endDate) {
                $query->where('created_at', '<=', $endDate);
            }
        }

        $salesbystaff = $query
            ->select('staff_id', 'user_id')
            ->selectRaw('SUM(total) as total_sales')
            ->selectRaw('COUNT(DISTINCT receipt_number) as transactions_count')
            ->selectRaw('SUM(quantity) as items_sold')
            ->selectRaw('MAX(created_at) as last_transaction_date')
            ->groupBy('staff_id', 'user_id')
            ->orderBy('total_sales', 'desc')
            ->with(['user', 'staff'])
            ->paginate(15);

        // Calculate totals with same filters
        $totalsQuery = CartItem::where('status', 'completed');

        // Apply same staff filter
        if ($request->filled('staff_id')) {
            $staffId = $request->staff_id;
            $totalsQuery->where(function($q) use ($staffId) {
                $q->where('staff_id', $staffId)
                  ->orWhere('user_id', $staffId);
            });
        }

        // Apply same date range filter
        if ($request->filled('date_range')) {
            $dateRange = $request->date_range;
            $startDate = null;
            $endDate = null;

            switch ($dateRange) {
                case 'today':
                    $startDate = Carbon::today();
                    $endDate = Carbon::today()->endOfDay();
                    break;
                case 'yesterday':
                    $startDate = Carbon::yesterday();
                    $endDate = Carbon::yesterday()->endOfDay();
                    break;
                case 'last7':
                    $startDate = Carbon::today()->subDays(6);
                    $endDate = Carbon::today()->endOfDay();
                    break;
                case 'last30':
                    $startDate = Carbon::today()->subDays(29);
                    $endDate = Carbon::today()->endOfDay();
                    break;
                case 'thisMonth':
                    $startDate = Carbon::now()->startOfMonth();
                    $endDate = Carbon::now()->endOfMonth();
                    break;
                case 'lastMonth':
                    $startDate = Carbon::now()->subMonth()->startOfMonth();
                    $endDate = Carbon::now()->subMonth()->endOfMonth();
                    break;
                case 'custom':
                    if ($request->filled('start_date')) {
                        $startDate = Carbon::parse($request->start_date)->startOfDay();
                    }
                    if ($request->filled('end_date')) {
                        $endDate = Carbon::parse($request->end_date)->endOfDay();
                    }
                    break;
            }

            if ($startDate) {
                $totalsQuery->where('created_at', '>=', $startDate);
            }
            if ($endDate) {
                $totalsQuery->where('created_at', '<=', $endDate);
            }
        }

        $totals = $totalsQuery
            ->selectRaw('SUM(total) as total_sales')
            ->selectRaw('COUNT(DISTINCT receipt_number) as transactions_count')
            ->selectRaw('SUM(quantity) as items_sold')
            ->first();

        return view('manager.reports.sales_by_staff', ['salesbystaff' => $salesbystaff], compact('totals'));
    }
}
