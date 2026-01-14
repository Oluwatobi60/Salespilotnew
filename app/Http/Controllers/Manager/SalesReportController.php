<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CartItem;
use Illuminate\Support\Facades\Log;
use App\Models\StandardItem;
use App\Models\VariantItem;
use App\Models\ProductVariant;
use App\Models\Staffs;
use App\Models\User;
use App\Models\Category;
use Carbon\Carbon;


class SalesReportController extends Controller
{

    // Display completed sales with pagination
    public function completed_sales()
    {
        $completedSales = CartItem::where('status', 'completed')
            ->select('receipt_number', 'customer_name', 'customer_id', 'created_at', 'user_id', 'staff_id')
            ->selectRaw('SUM(total) as total')
            ->selectRaw('SUM(discount) as discount')
            ->selectRaw('COUNT(*) as items_count')
            ->groupBy('receipt_number', 'customer_name', 'customer_id', 'created_at', 'user_id', 'staff_id')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('manager.sales.completed_sales', compact('completedSales'));
    }

    public function sales_summary()
    {
        // Get sales data grouped by date with aggregated calculations
        $salesData = CartItem::where('status', 'completed')
            ->selectRaw('DATE(created_at) as sale_date')
            ->selectRaw('SUM(subtotal) as gross_sales')
            ->selectRaw('SUM(discount) as total_discount')
            ->selectRaw('SUM(total) as net_sales')
            ->selectRaw('COUNT(DISTINCT receipt_number) as transaction_count')
            ->selectRaw('SUM(quantity) as items_sold')
            ->groupBy('sale_date')
            ->orderBy('sale_date', 'desc')
            ->get();

        // Debug: Check total completed sales
        $totalCompletedSales = CartItem::where('status', 'completed')
            ->distinct('receipt_number')
            ->count('receipt_number');

            // Log the total completed sales and sales data count
        Log::info('Total completed sales (by receipt): ' . $totalCompletedSales);
        Log::info('Sales data grouped by date: ' . $salesData->count());

        // Calculate cost of items, gross profit, margin, and taxes for each day
        $salesSummary = $salesData->map(function ($sale) {
            // Get all items sold on this date
            $items = CartItem::where('status', 'completed')
                ->whereDate('created_at', $sale->sale_date)
                ->get();

            // Collect unique seller IDs for this date
            $sellerIds = $items->map(function($item) {
                if ($item->staff_id) {
                    return 'staff_' . $item->staff_id;
                } elseif ($item->user_id) {
                    return 'user_' . $item->user_id;
                }
                return null;
            })->filter()->unique()->values()->toArray();

            $sale->seller_ids = $sellerIds;

            // Calculate cost of items and taxes by looking up cost_price and tax_rate for each item
            $costOfItems = 0;
            $totalTaxes = 0;
            $totalDiscount = 0;

            foreach ($items as $item) {
                $totalDiscount += $item->discount;
                Log::info('Processing cart item:', [
                    'item_name' => $item->item_name,
                    'item_type' => $item->item_type,
                    'item_id' => $item->item_id,
                    'item_code' => $item->item_code ?? null,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->subtotal
                ]);

                if ($item->item_type === 'standard') {
                    $standardItem = null;
                    if (!empty($item->item_code)) {
                        $standardItem = StandardItem::where('item_code', $item->item_code)->first();
                    }
                    if (!$standardItem) {
                        $standardItem = StandardItem::find($item->item_id);
                    }
                    if ($standardItem) {
                        $itemCost = ($standardItem->cost_price ?? 0) * $item->quantity;
                        $costOfItems += $itemCost;
                        Log::info('Standard item found:', [
                            'item_name' => $standardItem->item_name,
                            'cost_price' => $standardItem->cost_price,
                            'quantity' => $item->quantity,
                            'itemCost' => $itemCost
                        ]);
                        $taxRate = ($standardItem->tax_rate ?? 0) / 100;
                        $totalTaxes += $item->subtotal * $taxRate;
                    } else {
                        Log::warning('Standard item not found for item_id: ' . $item->item_id . ' or item_code: ' . ($item->item_code ?? ''));
                    }
                } elseif ($item->item_type === 'variant') {
                    $productVariant = null;
                    if (!empty($item->item_code)) {
                        $productVariant = ProductVariant::where('variant_code', $item->item_code)->first();
                    }
                    if (!$productVariant) {
                        $productVariant = ProductVariant::find($item->item_id);
                    }
                    if ($productVariant) {
                        $itemCost = ($productVariant->cost_price ?? 0) * $item->quantity;
                        $costOfItems += $itemCost;
                        Log::info('Product variant found:', [
                            'variant_name' => $productVariant->variant_name,
                            'cost_price' => $productVariant->cost_price,
                            'quantity' => $item->quantity,
                            'itemCost' => $itemCost
                        ]);
                        $taxRate = ($productVariant->tax_rate ?? 0) / 100;
                        $totalTaxes += $item->subtotal * $taxRate;
                    } else {
                        $variantItem = null;
                        if (!empty($item->item_code)) {
                            $variantItem = VariantItem::where('variant_code', $item->item_code)->first();
                        }
                        if (!$variantItem) {
                            $variantItem = VariantItem::find($item->item_id);
                        }
                        if ($variantItem) {
                            $itemCost = ($variantItem->cost_price ?? 0) * $item->quantity;
                            $costOfItems += $itemCost;
                            Log::info('Variant item found:', [
                                'item_name' => $variantItem->item_name,
                                'cost_price' => $variantItem->cost_price,
                                'quantity' => $item->quantity,
                                'itemCost' => $itemCost
                            ]);
                            $taxRate = ($variantItem->tax_rate ?? 0) / 100;
                            $totalTaxes += $item->subtotal * $taxRate;
                        } else {
                            Log::warning('Product variant and variant item not found for item_id: ' . $item->item_id . ' or item_code: ' . ($item->item_code ?? ''));
                        }
                    }
                }
            }

            Log::info('Total cost calculation:', [
                'sale_date' => $sale->sale_date,
                'total_cost_of_items' => $costOfItems,
                'gross_sales' => $sale->gross_sales,
                'total_discount' => $totalDiscount
            ]);

            // Calculate gross profit: (Gross Sales - Discount) - Cost of Items
            $grossProfit = ($sale->gross_sales - $totalDiscount) - $costOfItems;

            // Calculate margin percentage based on (gross sales - discount)
            $marginBase = $sale->gross_sales - $totalDiscount;
            $margin = $marginBase > 0 ? ($grossProfit / $marginBase) * 100 : 0;

            // Add calculated fields to the sale object
            $sale->cost_of_items = round($costOfItems, 2);
            $sale->gross_profit = round($grossProfit, 2);
            $sale->margin = round($margin, 1);
            $sale->taxes = round($totalTaxes, 2);
            $sale->total_discount = round($totalDiscount, 2);

            return $sale;
        });

        // Convert collection to paginator manually
        $perPage = 15;
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage('page');
        $currentItems = $salesSummary->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $salesSummaryPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $salesSummary->count(),
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        return view('manager.reports.sales_summary', [
            'salesSummary' => $salesSummaryPaginated,
            'allSalesData' => $salesSummary->values()->toArray() // Convert to array for charts
        ]);
    }






    public function sales_by_category(Request $request)
    {
        // Get all completed sales items
        $query = CartItem::where('status', 'completed');

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

        $cartItems = $query->get();

        // Aggregate by category
        $categoryData = [];
        foreach ($cartItems as $item) {
            $categoryId = null;
            $categoryName = '';
            $costPrice = 0;
            $taxRate = 0;
            if ($item->item_type === 'standard') {
                $std = StandardItem::find($item->item_id);
                if ($std) {
                    $categoryId = $std->category;
                    $catModel = Category::find($categoryId);
                    $categoryName = $catModel ? $catModel->category_name : ($categoryId ?? 'Unknown');
                    $costPrice = $std->cost_price ?? 0;
                    $taxRate = $std->tax_rate ?? 0;
                }
            } elseif ($item->item_type === 'variant') {
                $prodVar = ProductVariant::find($item->item_id);
                $variantItem = $prodVar ? $prodVar->variantItem : null;
                if ($prodVar) {
                    $costPrice = $prodVar->cost_price ?? 0;
                    $taxRate = $prodVar->tax_rate ?? 0;
                }
                if ($variantItem) {
                    $categoryId = $variantItem->category;
                    $catModel = Category::find($categoryId);
                    $categoryName = $catModel ? $catModel->category_name : ($categoryId ?? 'Unknown');
                } else {
                    $var = VariantItem::find($item->item_id);
                    if ($var) {
                        $categoryId = $var->category;
                        $catModel = Category::find($categoryId);
                        $categoryName = $catModel ? $catModel->category_name : ($categoryId ?? 'Unknown');
                    }
                }
            }
            if ($categoryId === null) {
                $categoryId = 'uncategorized';
                $categoryName = 'Uncategorized';
            }
            if (!isset($categoryData[$categoryId])) {
                $categoryData[$categoryId] = [
                    'category_id' => $categoryId,
                    'category_name' => $categoryName,
                    'total_quantity_sold' => 0,
                    'gross_sales' => 0,
                    'total_discount' => 0,
                    'total_sales' => 0,
                    'transactions_count' => 0,
                    'total_cost' => 0,
                    'gross_profit' => 0,
                    'tax' => 0,
                ];
            }
            // Calculate item cost and tax
            $itemCost = $costPrice * $item->quantity;
            // Calculate item tax based on subtotal and tax rate
            $itemTax = ($taxRate / 100) * $item->subtotal;
            // Aggregate data
            $categoryData[$categoryId]['total_quantity_sold'] += $item->quantity;
            $categoryData[$categoryId]['gross_sales'] += $item->subtotal;
            $categoryData[$categoryId]['total_discount'] += $item->discount;
            $categoryData[$categoryId]['total_sales'] += $item->total;
            $categoryData[$categoryId]['transactions_count'] += 1; // Each cart item is a transaction line
            $categoryData[$categoryId]['total_cost'] += $itemCost;
            $categoryData[$categoryId]['tax'] += $itemTax;
        }

        // Calculate gross profit and margin for each category
        foreach ($categoryData as &$cat) {
            // Deduct discount from gross sales before gross profit
            $grossSalesAfterDiscount = $cat['gross_sales'] - $cat['total_discount'];
            $cat['gross_profit'] = $grossSalesAfterDiscount - $cat['total_cost'];
            $cat['margin'] = $grossSalesAfterDiscount > 0 ? ($cat['gross_profit'] / $grossSalesAfterDiscount) * 100 : 0;
        }
        unset($cat);

        // Convert to collection and sort by gross_sales desc
        $salesByCategory = collect($categoryData)->sortByDesc('gross_sales')->values();

        // Apply category filter after aggregation
        if ($request->filled('category_id')) {
            $salesByCategory = $salesByCategory->filter(function($category) use ($request) {
                return $category['category_id'] == $request->category_id;
            })->values();
        }

        // Calculate totals for all categories (or filtered categories)
        $totals = [
            'gross_sales' => $salesByCategory->sum('gross_sales'),
            'total_discount' => $salesByCategory->sum('total_discount'),
            'net_sales' => $salesByCategory->sum('total_sales'),
            'items_cost' => $salesByCategory->sum('total_cost'),
            'gross_profit' => $salesByCategory->sum('gross_profit'),
            'tax' => $salesByCategory->sum('tax'),
        ];

        return view('manager.reports.sales_by_category', [
            'salesByCategory' => $salesByCategory,
            'totals' => $totals
        ]);
    }




    // Get sale items by receipt number
    public function get_sale_items($receiptNumber)
    {
        try {
            $items = CartItem::where('receipt_number', $receiptNumber)
                ->where('status', 'completed')
                ->get();

            if ($items->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No items found for this receipt'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'items' => $items
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load sale items: ' . $e->getMessage()
            ], 500);
        }
    }



    public function getStaffUserList()
    {
        // Get unique staff IDs from completed sales in cart_items
        $staffIds = CartItem::where('status', 'completed')
            ->whereNotNull('staff_id')
            ->distinct()
            ->pluck('staff_id');

        // Get unique user IDs from completed sales in cart_items
        $userIds = CartItem::where('status', 'completed')
            ->whereNotNull('user_id')
            ->distinct()
            ->pluck('user_id');

        // Get staff members who have made sales
        $staffUsers = collect();
        if ($staffIds->isNotEmpty()) {
            $staffUsers = Staffs::whereIn('id', $staffIds)
                ->select('id', 'fullname')
                ->get()
                ->map(function ($staff) {
                    return [
                        'id' => 'staff_' . $staff->id,
                        'name' => $staff->fullname,
                        'type' => 'staff'
                    ];
                });
        }

        // Get users who have made sales
        $users = collect();
        if ($userIds->isNotEmpty()) {
            $users = User::whereIn('id', $userIds)
                ->select('id', 'name')
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => 'user_' . $user->id,
                        'name' => $user->name,
                        'type' => 'user'
                    ];
                });
        }

        // Merge and sort by name
        $userList = $staffUsers->merge($users)->sortBy('name')->values();

        Log::info('Seller filter data', [
            'staff_count' => $staffUsers->count(),
            'user_count' => $users->count(),
            'total' => $userList->count()
        ]);

        return response()->json([
            'success' => true,
            'staffUsers' => $userList
        ]);
    }


        // Print receipt for a completed sale
    public function print_receipt($receiptNumber)
    {
        // Get all items for this receipt
        $items = CartItem::where('receipt_number', $receiptNumber)
            ->where('status', 'completed')
            ->get();

        if ($items->isEmpty()) {
            abort(404, 'Sale not found');
        }

        // Get sale summary (customer, date, total, discount, etc.)
        $sale = $items->first();
        $total = $items->sum('total');
        $discount = $items->sum('discount');
        $subtotal = $items->sum('subtotal');

        return view('manager.sales.print_receipt', [
            'items' => $items,
            'sale' => $sale,
            'total' => $total,
            'discount' => $discount,
            'subtotal' => $subtotal,
        ]);
    }
}
