<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CartItem;
use Illuminate\Support\Facades\Log;
use App\Models\StandardItem;
use App\Models\VariantItem;
use App\Models\ProductVariant;


class SalesReportController extends Controller
{

    // Display completed sales with pagination
   public function completed_sales()
    {
        $completedSales = CartItem::where('status', 'completed')
            ->select('receipt_number', 'customer_name', 'customer_id', 'created_at', 'user_id')
            ->selectRaw('SUM(total) as total')
            ->selectRaw('COUNT(*) as items_count')
            ->with('user')
            ->groupBy('receipt_number', 'customer_name', 'customer_id', 'created_at', 'user_id')
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

            // Calculate cost of items and taxes by looking up cost_price and tax_rate for each item
            $costOfItems = 0;
            $totalTaxes = 0;

            foreach ($items as $item) {
                Log::info('Processing cart item:', [
                    'item_name' => $item->item_name,
                    'item_type' => $item->item_type,
                    'item_id' => $item->item_id,
                    'item_code' => $item->item_code ?? null,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->subtotal
                ]);

                if ($item->item_type === 'standard') {
                    // Prefer lookup by item_code if present, else by item_id
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
                    // Prefer lookup by item_code if present, else by item_id
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
                        // If not found, try VariantItem
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
                'gross_sales' => $sale->gross_sales
            ]);

            // Calculate gross profit: Gross Sales - Cost of Items
            // Gross profit is based on sales before discount
            $grossProfit = $sale->gross_sales - $costOfItems;

            // Calculate margin percentage based on gross sales
            $margin = $sale->gross_sales > 0 ? ($grossProfit / $sale->gross_sales) * 100 : 0;

            // Add calculated fields to the sale object
            $sale->cost_of_items = round($costOfItems, 2);
            $sale->gross_profit = round($grossProfit, 2);
            $sale->margin = round($margin, 1);
            $sale->taxes = round($totalTaxes, 2);

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
            'allSalesData' => $salesSummary // For charts
        ]);
    }

    public function staff_sales()
    {
        return view('manager.reports.sales_by_staff');
    }


    public function sales_by_item()
    {
        return view('manager.reports.sales_by_item');
    }


    public function sales_by_category()
    {
        return view('manager.reports.sale_by_category');
    }

    public function valuation_report()
    {
        return view('manager.reports.inventory_valuation');
    }

    public function taxes()
    {
        return view('manager.reports.taxes');
    }

    public function discount_report()
    {
        return view('manager.reports.discount_report');
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
}
