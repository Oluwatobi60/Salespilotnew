<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\StandardItem;
use App\Models\ProductVariant;
use App\Models\Category;
use App\Models\BranchInventory;
use App\Models\Branch\Branch;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class ValuationReportController extends Controller
{
      public function valuation_report(Request $request)
    {
        // Get manager information
        $manager = Auth::user();
        $businessName = $manager->business_name;

        // Check if user is a business creator or branch manager
        $isBranchManager = !empty($manager->addby);
        $managerBranchName = $manager->branch_name;
        $branchIds = [];

        if ($isBranchManager && !empty($managerBranchName)) {
            // Get branch IDs for this manager's branch
            $branchIds = Branch::where('business_name', $businessName)
                ->where('branch_name', $managerBranchName)
                ->pluck('id')
                ->toArray();
        }

        // Fetch items based on user role
        $items = [];
        $totalInventoryValue = 0;
        $totalSellingValue = 0;
        $totalPotentialProfit = 0;
        $totalMargin = 0;
        $marginDenominator = 0;

        if ($isBranchManager && !empty($branchIds)) {
            // Branch manager: Get items from branch_inventory
            $branchInventories = BranchInventory::whereIn('branch_id', $branchIds)
                ->where('business_name', $businessName)
                ->get();

            $categories = Category::where('business_name', $businessName)->pluck('category_name', 'id');

            foreach ($branchInventories as $branchItem) {
                $quantity = $branchItem->current_quantity ?? 0;
                $itemName = '';
                $cost = 0;
                $selling = 0;
                $categoryName = 'N/A';
                $categoryId = null;
                $branchName = $branchItem->branch ? $branchItem->branch->branch_name : 'N/A';

                if ($branchItem->item_type === 'standard') {
                    $standardItem = StandardItem::find($branchItem->item_id);
                    if ($standardItem) {
                        $itemName = $standardItem->item_name;
                        $cost = $standardItem->cost_price ?? 0;
                        $selling = $standardItem->selling_price ?? 0;
                        $categoryName = $categories[$standardItem->category] ?? 'N/A';
                        $categoryId = $standardItem->category;
                    }
                } elseif ($branchItem->item_type === 'variant') {
                    $variant = ProductVariant::find($branchItem->item_id);
                    if ($variant) {
                        $itemName = $variant->variant_name;
                        $cost = $variant->cost_price ?? 0;
                        $selling = $variant->selling_price ?? 0;
                        if ($variant->variantItem && isset($variant->variantItem->category)) {
                            $catId = $variant->variantItem->category;
                            $categoryName = $categories[$catId] ?? 'N/A';
                            $categoryId = $catId;
                        }
                    }
                }

                if (!empty($itemName)) {
                    $inventoryValue = $quantity * $cost;
                    $sellingValue = $quantity * $selling;
                    $potentialProfit = $sellingValue - $inventoryValue;
                    $margin = $sellingValue > 0 ? ($potentialProfit / $sellingValue) * 100 : 0;

                    $items[] = [
                        'item_name' => $itemName,
                        'category_name' => $categoryName,
                        'category_id' => $categoryId,
                        'quantity' => $quantity,
                        'cost_price' => $cost,
                        'inventory_value' => $inventoryValue,
                        'total_selling_value' => $sellingValue,
                        'potential_profit' => $potentialProfit,
                        'margin' => $margin,
                        'branch_name' => $branchName,
                    ];

                    $totalInventoryValue += $inventoryValue;
                    $totalSellingValue += $sellingValue;
                    $totalPotentialProfit += $potentialProfit;
                    $marginDenominator += $sellingValue;
                }
            }
        } else {
            // Business creator: Show all items across all branches
            $standardItems = StandardItem::where('business_name', $businessName)->get();
            $productVariants = ProductVariant::where('business_name', $businessName)->get();
            $categories = Category::where('business_name', $businessName)->pluck('category_name', 'id');

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

                // Get branch info from branch_inventory
                $branchInfo = BranchInventory::where('item_id', $item->id)
                    ->where('item_type', 'standard')
                    ->with('branch')
                    ->first();
                $branchName = $branchInfo && $branchInfo->branch ? $branchInfo->branch->branch_name : 'Main Stock';

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
                    'branch_name' => $branchName,
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

                // Get branch info from branch_inventory
                $branchInfo = BranchInventory::where('item_id', $variant->id)
                    ->where('item_type', 'variant')
                    ->with('branch')
                    ->first();
                $branchName = $branchInfo && $branchInfo->branch ? $branchInfo->branch->branch_name : 'Main Stock';

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
                    'branch_name' => $branchName,
                ];
                $totalInventoryValue += $inventoryValue;
                $totalSellingValue += $sellingValue;
                $totalPotentialProfit += $potentialProfit;
                $marginDenominator += $sellingValue;
            }
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
