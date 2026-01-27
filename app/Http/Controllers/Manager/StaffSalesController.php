<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CartItem;
use App\Models\Staffs;
use Carbon\Carbon;

class StaffSalesController extends Controller
{
      public function staff_sales(Request $request)
    {
        // Get manager information
        $manager = Auth::user();
        $businessName = $manager->business_name;

        $query = CartItem::where('cart_items.status', 'completed')
            ->where('cart_items.business_name', $businessName);

        // Apply staff filter
        if ($request->filled('staff_id')) {
            $staffId = $request->staff_id;
            $query->where(function($q) use ($staffId) {
                $q->where('cart_items.staff_id', $staffId)
                  ->orWhere('cart_items.user_id', $staffId);
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
                $query->where('cart_items.created_at', '>=', $startDate);
            }
            if ($endDate) {
                $query->where('cart_items.created_at', '<=', $endDate);
            }
        }

        $salesbystaff = $query
            ->leftJoin('staffs', 'cart_items.staff_id', '=', 'staffs.id')
            ->selectRaw("
                CASE
                    WHEN cart_items.staff_id IS NULL THEN CONCAT('Manager: ', cart_items.manager_name)
                    ELSE cart_items.manager_name
                END as seller_name
            ")
            ->selectRaw("
                CASE
                    WHEN cart_items.staff_id IS NULL THEN 'Manager'
                    ELSE staffs.role
                END as seller_role
            ")
            ->select(
                'cart_items.staff_id',
                'cart_items.user_id',
                'cart_items.manager_name',
                'staffs.fullname as staff_name',
                'staffs.email as staff_email'
            )
            ->selectRaw('SUM(cart_items.total) as total_sales')
            ->selectRaw('COUNT(DISTINCT cart_items.receipt_number) as transactions_count')
            ->selectRaw('SUM(cart_items.quantity) as items_sold')
            ->selectRaw('MAX(cart_items.created_at) as last_transaction_date')
            ->groupBy('cart_items.manager_name', 'cart_items.staff_id', 'cart_items.user_id', 'staffs.fullname', 'staffs.email', 'staffs.role')
            ->orderBy('total_sales', 'desc')
            ->paginate(15);

        // Calculate totals with same filters
        $totalsQuery = CartItem::where('cart_items.status', 'completed')
            ->where('cart_items.business_name', $businessName);

        // Apply same staff filter
        if ($request->filled('staff_id')) {
            $staffId = $request->staff_id;
            $totalsQuery->where(function($q) use ($staffId) {
                $q->where('cart_items.staff_id', $staffId)
                  ->orWhere('cart_items.user_id', $staffId);
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
                $totalsQuery->where('cart_items.created_at', '>=', $startDate);
            }
            if ($endDate) {
                $totalsQuery->where('cart_items.created_at', '<=', $endDate);
            }
        }

        $totals = $totalsQuery
            ->selectRaw('SUM(total) as total_sales')
            ->selectRaw('COUNT(DISTINCT receipt_number) as transactions_count')
            ->selectRaw('SUM(quantity) as items_sold')
            ->first();

        // Get all staff members from manager's business for the filter dropdown
        $staffList = Staffs::where('business_name', $businessName)
            ->select('id', 'fullname', 'staffsid')
            ->orderBy('fullname')
            ->get();

        return view('manager.reports.sales_by_staff', compact('salesbystaff', 'totals', 'staffList'));
    }
}
