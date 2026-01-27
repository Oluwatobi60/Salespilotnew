<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\StandardItem;
use App\Models\ProductVariant;
use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;

class ValuationReportController extends Controller
{
      public function valuation_report(Request $request)
    {
        // Get manager information
        $manager = Auth::user();
        $businessName = $manager->business_name;

        // Fetch all standard items and product variants filtered by business_name
        $standardItems = StandardItem::where('business_name', $businessName)->get();
        $productVariants = ProductVariant::where('business_name', $businessName)->get();
        $categories = Category::where('business_name', $businessName)->pluck('category_name', 'id');

        $items = [];
        $totalInventoryValue = 0;
        $totalSellingValue = 0;
        $totalPotentialProfit = 0;
        $totalMargin = 0;
        $marginDenominator = 0;
        // Standard Items
        foreach ($standardItems as $item) {
            $quantity = $item->current_stock ?? 0;
            $cost = $item->cost_price ?? 0;
            $selling = $item->selling_price ?? 0;
            $inventoryValue = $quantity * $cost;
            $sellingValue = $quantity * $selling;
            $potentialProfit = $sellingValue - $inventoryValue;
            $margin = $sellingValue > 0 ? ($potentialProfit / $sellingValue) * 100 : 0;
            $categoryName = $categories[$item->category] ?? 'N/A';
            $items[] = [
                'item_name' => $item->item_name,
                'category_name' => $categoryName,
                'category_id' => $item->category,
                'quantity' => $quantity,
                'cost_price' => $cost,
                'inventory_value' => $inventoryValue,
                'total_selling_value' => $sellingValue,
                'potential_profit' => $potentialProfit,
                'margin' => $margin,
            ];
            $totalInventoryValue += $inventoryValue;
            $totalSellingValue += $sellingValue;
            $totalPotentialProfit += $potentialProfit;
            $marginDenominator += $sellingValue;
        }
        // Product Variants
        foreach ($productVariants as $variant) {
            $quantity = $variant->stock_quantity ?? 0;
            $cost = $variant->cost_price ?? 0;
            $selling = $variant->selling_price ?? 0;
            $inventoryValue = $quantity * $cost;
            $sellingValue = $quantity * $selling;
            $potentialProfit = $sellingValue - $inventoryValue;
            $margin = $sellingValue > 0 ? ($potentialProfit / $sellingValue) * 100 : 0;
            // Get category name via related VariantItem
            $categoryName = 'N/A';
            $categoryId = null;
            if ($variant->variantItem) {
                $variantItem = $variant->variantItem;
                if (isset($variantItem->category)) {
                    $catId = $variantItem->category;
                    $categoryName = $categories[$catId] ?? 'N/A';
                    $categoryId = $catId;
                }
            }
            $items[] = [
                'item_name' => $variant->variant_name,
                'category_name' => $categoryName,
                'category_id' => $categoryId,
                'quantity' => $quantity,
                'cost_price' => $cost,
                'inventory_value' => $inventoryValue,
                'total_selling_value' => $sellingValue,
                'potential_profit' => $potentialProfit,
                'margin' => $margin,
            ];
            $totalInventoryValue += $inventoryValue;
            $totalSellingValue += $sellingValue;
            $totalPotentialProfit += $potentialProfit;
            $marginDenominator += $sellingValue;
        }

        // Apply search filter
        $itemsCollection = collect($items);
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $itemsCollection = $itemsCollection->filter(function($item) use ($search) {
                return str_contains(strtolower($item['item_name']), $search) ||
                       str_contains(strtolower($item['category_name']), $search);
            });
        }

        // Apply category filter
        if ($request->filled('category')) {
            $selectedCategory = $request->category;
            $itemsCollection = $itemsCollection->filter(function($item) use ($selectedCategory) {
                return $item['category_name'] === $selectedCategory;
            });
        }

        $overallMargin = $marginDenominator > 0 ? ($totalPotentialProfit / $marginDenominator) * 100 : 0;
        // Paginate items (15 per page)
        $perPage = 10;
        $page = request()->get('page', 1);
        $paginatedItems = new LengthAwarePaginator(
            $itemsCollection->forPage($page, $perPage),
            $itemsCollection->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // Get all unique categories from items filtered by business_name
        $allCategories = Category::where('business_name', $businessName)->orderBy('category_name')->get();

        return view('manager.reports.inventory_valuation', compact('paginatedItems', 'totalInventoryValue', 'totalSellingValue', 'totalPotentialProfit', 'overallMargin', 'allCategories'));
    }
}
