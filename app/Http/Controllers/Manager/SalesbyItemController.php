<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\StandardItem;
use App\Models\ProductVariant;
use App\Models\VariantItem;
use App\Models\Category;

class SalesbyItemController extends Controller
{
     public function sales_by_item()
    {

        $rawItems = CartItem::where('status', 'completed')
            ->select('item_id', 'item_type', 'item_name')
            ->selectRaw('SUM(quantity) as total_quantity_sold')
            ->selectRaw('SUM(subtotal) as gross_sales')
            ->selectRaw('SUM(discount) as total_discount')
            ->selectRaw('SUM(total) as total_sales')
            ->selectRaw('COUNT(DISTINCT receipt_number) as transactions_count')
            ->groupBy('item_id', 'item_type', 'item_name')
            ->orderBy('gross_sales', 'desc')
            ->get();

        // Enrich with cost price, gross profit, margin, category, sku
            $salesbyitem = $rawItems->map(function($item) {
                $costPrice = 0;
                $category = '';
                $category_name = '';
                $sku = '';
                // Ensure all required fields have defaults
                $base = [
                    'item_name' => $item->item_name ?? '',
                    'sku' => '',
                    'category' => '',
                    'category_name' => '',
                    'total_quantity_sold' => $item->total_quantity_sold ?? 0,
                    'gross_sales' => $item->gross_sales ?? 0,
                    'total_discount' => $item->total_discount ?? 0,
                    'total_sales' => $item->total_sales ?? 0,
                    'transactions_count' => $item->transactions_count ?? 0,
                ];
                if ($item->item_type === 'standard') {
                    $std = StandardItem::find($item->item_id);
                    if ($std) {
                        $costPrice = $std->cost_price ?? 0;
                        // If category is an ID, fetch the name
                        if (!empty($std->category) && is_numeric($std->category)) {
                            $catModel = Category::find($std->category);
                            $category = $std->category;
                            $category_name = $catModel ? $catModel->category_name : $std->category;
                        } else {
                            $category = $std->category ?? '';
                            $category_name = $std->category ?? '';
                        }
                        $sku = $std->item_code ?? '';
                    }

                } elseif ($item->item_type === 'variant') {
                    // Prefer cost_price and sku from ProductVariant
                    $prodVar = ProductVariant::find($item->item_id);
                    if ($prodVar) {
                        $costPrice = $prodVar->cost_price ?? 0;
                        // Get category from related VariantItem
                        $variantItem = $prodVar->variantItem;
                        if ($variantItem && !empty($variantItem->category) && is_numeric($variantItem->category)) {
                            $catModel = Category::find($variantItem->category);
                            $category = $variantItem->category;
                            $category_name = $catModel ? $catModel->category_name : $variantItem->category;
                        } else if ($variantItem) {
                            $category = $variantItem->category ?? '';
                            $category_name = $variantItem->category ?? '';
                        } else {
                            $category = '';
                            $category_name = '';
                        }
                        $sku = $prodVar->sku ?? $prodVar->variant_code ?? '';
                    } else {
                        $var = VariantItem::find($item->item_id);
                        if ($var) {
                            $costPrice = $var->cost_price ?? 0;
                            if (!empty($var->category) && is_numeric($var->category)) {
                                $catModel = Category::find($var->category);
                                $category = $var->category;
                                $category_name = $catModel ? $catModel->category_name : $var->category;
                            } else {
                                $category = $var->category ?? '';
                                $category_name = $var->category ?? '';
                            }
                            $sku = $var->variant_code ?? '';
                        }
                    }
                }
                // Calculate total cost, gross profit, and margin
                $totalCost = $costPrice * $item->total_quantity_sold;
                $grossProfit = $item->gross_sales - $totalCost;
                $margin = $item->gross_sales > 0 ? ($grossProfit / $item->gross_sales) * 100 : 0;
                return (object) array_merge($base, [
                    'cost_price' => round($costPrice, 2),
                    'total_cost' => round($totalCost, 2),
                    'gross_profit' => round($grossProfit, 2),
                    'profit_margin' => round($margin, 1),
                    'category' => $category,
                    'category_name' => $category_name,
                    'sku' => $sku
                ]);
            });

        // Calculate totals
        $totals = [
            'gross_sales' => $salesbyitem->sum('gross_sales'),
            'cost_price' => $salesbyitem->sum(function($item) { return $item->cost_price * $item->total_quantity_sold; }),
            'gross_profit' => $salesbyitem->sum('gross_profit'),
            'total_discount' => $salesbyitem->sum('total_discount'),
        ];

        // Paginate manually
        $perPage = 15;
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage('page');
        $currentItems = $salesbyitem->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $salesbyitemPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $salesbyitem->count(),
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );
        return view('manager.reports.sales_by_item', [
            'salesbyitem' => $salesbyitemPaginated,
            'totals' => $totals
        ]);
    }

}
