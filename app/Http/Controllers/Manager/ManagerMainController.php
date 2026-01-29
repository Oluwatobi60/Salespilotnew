<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\StandardItem;
use App\Models\Category;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CartItem;
use Carbon\Carbon;

class ManagerMainController extends Controller
{

    // Dashboard view with sales metrics
    public function index()
    {
        // Default: show stats for all time, or filter by date if provided
        $startDate = request('start_date');
        $endDate = request('end_date');
        $query = CartItem::where('status', 'completed');
        if ($startDate && $endDate) {
            $query->whereDate('created_at', '>=', $startDate)
                  ->whereDate('created_at', '<=', $endDate);
        }

        // Calculate key metrics
        $totalItemsSold = (clone $query)->sum('quantity');
        $numberOfSales = (clone $query)->distinct('receipt_number')->count('receipt_number');

        $grossSales = (clone $query)->sum('subtotal');
        $totalDiscount = (clone $query)->sum('discount');
        $grossSalesAfterDiscount = $grossSales - $totalDiscount;

        // Calculate gross profit: gross sales - cost of items
        $cartItems = (clone $query)->get();
        $totalCost = 0;
        foreach ($cartItems as $item) {
            $cost = 0;
            if ($item->item_type === 'standard') {
                $std = StandardItem::find($item->item_id);
                $cost = $std ? ($std->cost_price ?? 0) * $item->quantity : 0;
            } elseif ($item->item_type === 'variant') {
                $prodVar = ProductVariant::find($item->item_id);
                $cost = $prodVar ? ($prodVar->cost_price ?? 0) * $item->quantity : 0;
            }
            $totalCost += $cost;
        }
        $grossProfit = $grossSalesAfterDiscount - $totalCost;

        // Recent sales activity with pagination
        $recentSales = (clone $query)
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'recent_sales_page');

        // Sales Overview: group by day
        $salesOverview = (clone $query)
            ->selectRaw('DATE(created_at) as date, SUM(subtotal) as gross_sales')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function($row) {
                return [
                    'date' => $row->date,
                    'gross_sales' => $row->gross_sales,
                ];
            });

        // Top Products: group by item and sum quantity
        $topProducts = (clone $query)
            ->selectRaw('item_id, item_type, SUM(quantity) as units_sold')
            ->groupBy('item_id', 'item_type')
            ->orderByDesc('units_sold')
            ->limit(5)
            ->get()
            ->map(function($row) {
                // Get correct item name for chart
                if ($row->item_type === 'standard') {
                    $item = StandardItem::find($row->item_id);
                    $name = $item ? ($item->item_name ?? $item->item_code ?? ('ID: ' . $row->item_id)) : 'Unknown';
                } else {
                    $item = ProductVariant::find($row->item_id);
                    $name = $item ? ($item->variant_name ?? $item->sku ?? ('ID: ' . $row->item_id)) : 'Unknown';
                }
                return [
                    'name' => $name,
                    'units_sold' => $row->units_sold,
                ];
            });

        // Check subscription expiry status for notifications
        $user = Auth::user();
        $subscription = UserSubscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->orderBy('end_date', 'desc')
            ->first();

        $subscriptionAlert = null;
        $daysRemaining = null;

        if ($subscription) {
            $daysRemaining = Carbon::today()->diffInDays($subscription->end_date, false);

            // Show alert if expiring within 5 days or already expired
            if ($daysRemaining <= 5) {
                $subscriptionAlert = [
                    'days_remaining' => max(0, $daysRemaining),
                    'end_date' => $subscription->end_date->format('F j, Y'),
                    'plan_name' => ($subscription->subscriptionPlan && isset($subscription->subscriptionPlan->name)) ? $subscription->subscriptionPlan->name : 'N/A',
                    'is_expired' => $daysRemaining < 0,
                    'is_urgent' => $daysRemaining <= 2,
                ];
            }
        }

        return view('manager', [
            'totalItemsSold' => $totalItemsSold,
            'numberOfSales' => $numberOfSales,
            'grossSales' => $grossSales,
            'totalDiscount' => $totalDiscount,
            'grossSalesAfterDiscount' => $grossSalesAfterDiscount,
            'grossProfit' => $grossProfit,
            'recentSales' => $recentSales,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'salesOverview' => $salesOverview->toArray(),
            'topProducts' => $topProducts->toArray(),
            'subscriptionAlert' => $subscriptionAlert,
        ]);
    }


    public function add_item_standard()
    {
        $suppliers = Supplier::all();
        $categories = Category::all();
        return view('manager.standardItems.add_item_standard', compact('suppliers', 'categories'));
    }

    public function add_item_variant()
    {
        $suppliers = Supplier::all();
        $units = Unit::all();
        $categories = Category::all();
        return view('manager.variantItems.add_item_variant', compact('suppliers', 'units', 'categories'));
    }




}
