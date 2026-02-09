<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CartItem;
use App\Models\StandardItem;
use App\Models\ProductVariant;
use App\Models\VariantItem;
use App\Models\Category;
use App\Models\User;
use Carbon\Carbon;

class SalesbyItemController extends Controller
{
     public function sales_by_item(Request $request)
    {
        // Get manager information
        $manager = Auth::user();
        $branchName = $manager->branch_name;

        // If user was added by another manager, get the creator's business_name
        if ($manager->addby) {
            $creator = User::where('email', $manager->addby)->first();
            $businessName = $creator ? $creator->business_name : $manager->business_name;
        } else {
            $businessName = $manager->business_name;
        }

        $query = CartItem::where('status', 'completed')
            ->where('business_name', $businessName);

        // If the user was added by another manager, filter by user_id, staff_id, or branch_name
        if ($manager->addby) {
            $query->where(function($q) use ($manager, $branchName) {
                $q->where('user_id', $manager->id)
                  ->orWhereIn('staff_id', function($subQuery) use ($manager) {
                      $subQuery->select('id')
                          ->from('staffs')
                          ->where('manager_email', $manager->email);
                  })
                  ->orWhere('branch_name', $branchName);
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

        // Apply item filter
        if ($request->filled('item_id') && $request->filled('item_type')) {
            $query->where('item_id', $request->item_id)
                  ->where('item_type', $request->item_type);
        }

        $rawItems = $query
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
            $salesbyitem = $rawItems->map(function($item) use ($request) {
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
                    'gross_sales_after_discount' => ($item->gross_sales ?? 0) - ($item->total_discount ?? 0),
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
                $grossSalesAfterDiscount = ($item->gross_sales ?? 0) - ($item->total_discount ?? 0);
                $grossProfit = $grossSalesAfterDiscount - $totalCost;
                $margin = $grossSalesAfterDiscount > 0 ? ($grossProfit / $grossSalesAfterDiscount) * 100 : 0;
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

        // Apply category filter after enrichment
        if ($request->filled('category_id')) {
            $salesbyitem = $salesbyitem->filter(function($item) use ($request) {
                return $item->category == $request->category_id;
            });
        }

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

    public function getCategoriesList()
    {
        // Get manager information
        $manager = Auth::user();
        $branchName = $manager->branch_name;

        // If user was added by another manager, get the creator's business_name
        if ($manager->addby) {
            $creator = User::where('email', $manager->addby)->first();
            $businessName = $creator ? $creator->business_name : $manager->business_name;
        } else {
            $businessName = $manager->business_name;
        }

        // Build base query with business_name filter
        $query = CartItem::where('status', 'completed')
            ->where('business_name', $businessName)
            ->whereNotNull('item_id');

        // If the user was added by another manager, filter by user_id, staff_id, or branch_name
        if ($manager->addby) {
            $query->where(function($q) use ($manager, $branchName) {
                $q->where('user_id', $manager->id)
                  ->orWhereIn('staff_id', function($subQuery) use ($manager) {
                      $subQuery->select('id')
                          ->from('staffs')
                          ->where('manager_email', $manager->email);
                  })
                  ->orWhere('branch_name', $branchName);
            });
        }

        // Get unique categories from items that have been sold
        $categoryIds = $query->get()
            ->map(function($cartItem) {
                if ($cartItem->item_type === 'standard') {
                    $std = StandardItem::find($cartItem->item_id);
                    return $std ? $std->category : null;
                } elseif ($cartItem->item_type === 'variant') {
                    $prodVar = ProductVariant::find($cartItem->item_id);
                    if ($prodVar && $prodVar->variantItem) {
                        return $prodVar->variantItem->category;
                    }
                    $var = VariantItem::find($cartItem->item_id);
                    return $var ? $var->category : null;
                }
                return null;
            })
            ->filter(function($catId) {
                return !empty($catId) && is_numeric($catId);
            })
            ->unique()
            ->values();

        // Get category names
        $categories = Category::whereIn('id', $categoryIds)
            ->select('id', 'category_name')
            ->orderBy('category_name')
            ->get()
            ->map(function($cat) {
                return [
                    'id' => $cat->id,
                    'name' => $cat->category_name
                ];
            });

        return response()->json($categories);
    }

    public function getItemsList()
    {
        // Get manager information
        $manager = Auth::user();
        $branchName = $manager->branch_name;

        // If user was added by another manager, get the creator's business_name
        if ($manager->addby) {
            $creator = User::where('email', $manager->addby)->first();
            $businessName = $creator ? $creator->business_name : $manager->business_name;
        } else {
            $businessName = $manager->business_name;
        }

        // Build base query with business_name filter
        $query = CartItem::where('status', 'completed')
            ->where('business_name', $businessName);

        // If the user was added by another manager, filter by user_id, staff_id, or branch_name
        if ($manager->addby) {
            $query->where(function($q) use ($manager, $branchName) {
                $q->where('user_id', $manager->id)
                  ->orWhereIn('staff_id', function($subQuery) use ($manager) {
                      $subQuery->select('id')
                          ->from('staffs')
                          ->where('manager_email', $manager->email);
                  })
                  ->orWhere('branch_name', $branchName);
            });
        }

        // Get unique items from completed sales
        $items = $query->select('item_id', 'item_type', 'item_name')
            ->groupBy('item_id', 'item_type', 'item_name')
            ->orderBy('item_name')
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->item_id,
                    'type' => $item->item_type,
                    'name' => $item->item_name
                ];
            });

        return response()->json($items);
    }

}
