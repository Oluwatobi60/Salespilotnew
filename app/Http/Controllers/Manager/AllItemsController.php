<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\StandardItem;
use App\Models\VariantItem;
use App\Models\ProductVariant;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\BranchInventory;
use App\Models\Branch\Branch;

class AllItemsController extends Controller
{
    public function all_items()
    {
        // Get manager information
        /** @var \App\Models\User $manager */
        $manager = Auth::user();
        $businessName = $manager->business_name;

        // Determine plan scope for the manager
        $activeSubscription = $manager->currentSubscription()->with('subscriptionPlan')->first();
        $planName = strtolower(trim($activeSubscription->subscriptionPlan->name ?? ''));
        $isBasicOrFree = in_array($planName, ['basic', 'free']);

        // Base query for Standard Items
        $standardQuery = StandardItem::with([
            'supplier',
            'unit',
            'pricingTiers'
        ])->where('business_name', $businessName);

        // Base query for Variant Items
        $variantQuery = VariantItem::with([
            'supplier',
            'unit',
            'variants.pricingTiers'
        ])->where('business_name', $businessName);

        // If user is an added manager (has addby), filter by their own email
        if ($manager->addby) {
            // Get branches where this manager is the branch manager
            $managedBranchIds = Branch::where('manager_id', $manager->id)->pluck('id');
            // Only show items allocated to the branches this manager manages
            $standardQuery->whereHas('branchInventory', function($q) use ($managedBranchIds) {
                $q->whereIn('branch_id', $managedBranchIds);
            });
            // For variant items, branch_inventory entries reference product variant IDs, not the parent VariantItem.
            // Collect variant (product_variant) IDs allocated to the managed branches and filter by those.
            $variantIds = BranchInventory::where('item_type', 'variant')
                ->whereIn('branch_id', $managedBranchIds)
                ->where('business_name', $businessName)
                ->pluck('item_id')
                ->unique();
            if ($variantIds->count() > 0) {
                $variantQuery->whereHas('variants', function($q) use ($variantIds) {
                    $q->whereIn('id', $variantIds);
                });
            } else {
                // No variant allocations for these branches — return empty
                $variantQuery->whereRaw('1 = 0');
            }
        }

        // Fetch Standard Items with related data
        $standardItems = $standardQuery->latest()->get();

        // Fetch Variant Items with related data
        $variantItems = $variantQuery->latest()->get();

        // Fetch all Product Variants with related data
        $productVariants = ProductVariant::with([
            'variantItem.supplier',
            'variantItem.unit',
            'pricingTiers'
        ])->latest()->get();

        // Get unique categories from all item types
        $categories = collect();
        $categories = $categories->merge($standardItems->pluck('category_name'))
                                 ->merge($variantItems->pluck('category_name'))
                                 ->filter()
                                 ->unique()
                                 ->sort()
                                 ->values();

        // Get all suppliers filtered by business_name
        $suppliersQuery = Supplier::where('business_name', $businessName);

        // If user is an added manager, filter by their own email
        if ($manager->addby) {
            $suppliersQuery->where('manager_email', $manager->email);
        }

        $suppliers = $suppliersQuery->orderBy('name')->get();


        // Get all branches for this business
        $branches = Branch::where('business_name', $businessName)->get();

        // Combine all items into a single collection
        $allItems = collect();

        // Add standard items with type identifier
        foreach ($standardItems as $item) {
            $branchInventoriesQuery = BranchInventory::where('item_id', $item->id)
                ->where('item_type', 'standard')
                ->where('business_name', $businessName)
                ->with('branch');
            if ($manager->addby) {
                $branchInventoriesQuery->whereIn('branch_id', $managedBranchIds);
            }
            $branchInventories = $branchInventoriesQuery->get();
            $branch_inventory_list = $branchInventories->map(function($inv) {
                $branchName = ($inv->branch && $inv->branch->branch_name) ? $inv->branch->branch_name : ('Branch ID ' . $inv->branch_id);
                return $branchName . ': Allocated ' . $inv->allocated_quantity . ', Current ' . $inv->current_quantity;
            })->values();
            $totalAllocated = $branchInventories->sum('allocated_quantity');
            $branchCurrent = $branchInventories->sum('current_quantity');
// Determine opening stock, current stock, and general left based on plan and manager type
                    if ($isBasicOrFree) {
                        $openingStock = ($item->opening_stock ?? 0) > 0
                            ? $item->opening_stock
                            : (($item->current_stock ?? 0) + $totalAllocated);
                        $currentStock = $branchInventories->count() > 0
                            ? $branchCurrent
                            : ($item->current_stock ?? 0);
                        $generalLeft = $currentStock;
                    } else {
                        if ($manager->addby) {
                            $openingStock = $totalAllocated;
                            $currentStock = $branchCurrent;
                            $generalLeft = $branchCurrent;
                        } else {
                            $openingStock = ($item->current_stock ?? 0) + $totalAllocated;
                            $currentStock = $openingStock;
                            $generalLeft = $item->current_stock ?? 0;
                        }
                    }
// Only include items that are either not an added manager (full access) or have inventory in the branches they manage
            if (!$manager->addby || $branchInventories->count() > 0) {
                $unit = $item->relationLoaded('unit') ? $item->getRelation('unit') : (Unit::find($item->getAttribute('unit')) ?? null);
                $allItems->push([
                    'id' => $item->id,
                    'type' => 'standard',
                    'name' => $item->item_name,
                    'code' => $item->item_code,
                    'barcode' => $item->barcode,
                    'category' => $item->category_name,
                    'supplier' => $item->supplier,
                    'unit' => $unit,
                    'unit_abbreviation' => $unit?->abbreviation ?? $item->unit ?? null,
                    'image' => $item->item_image,
                    'cost_price' => $item->cost_price,
                    'selling_price' => $item->selling_price,
                    'profit_margin' => $item->profit_margin,
                    'current_stock' => $currentStock,
                    'general_left' => $generalLeft,
                    'opening_stock' => $openingStock,
                    'stock_added' => (int) ($item->stock_added ?? 0),
                    'actual_current_stock' => $branchInventories->count() > 0 ? $branchCurrent : ($item->current_stock ?? 0),
                    'low_stock_threshold' => $item->low_stock_threshold,
                    'pricing_tiers' => $item->pricingTiers,
                    'created_at' => $item->created_at,
                    'data' => $item,
                    'branch_inventory_list' => $branch_inventory_list,
                ]);
            }
        }

        // Add variant items - each product variant as a separate row
        foreach ($variantItems as $item) {
            if ($item->variants && $item->variants->count() > 0) {
                foreach ($item->variants as $variant) {
                    $branchInventoriesQuery = BranchInventory::where('item_id', $variant->id)
                        ->where('item_type', 'variant')
                        ->where('business_name', $businessName)
                        ->with('branch');
                    if ($manager->addby) {
                        $branchInventoriesQuery->whereIn('branch_id', $managedBranchIds);
                    }
                    $branchInventories = $branchInventoriesQuery->get();
                    $branch_inventory_list = $branchInventories->map(function($inv) {
                        $branchName = ($inv->branch && $inv->branch->branch_name) ? $inv->branch->branch_name : ('Branch ID ' . $inv->branch_id);
                        return $branchName . ': Allocated ' . $inv->allocated_quantity . ', Current ' . $inv->current_quantity;
                    })->values();
                    $totalAllocated = $branchInventories->sum('allocated_quantity');
                    $branchCurrent = $branchInventories->sum('current_quantity');

                    if ($isBasicOrFree) {
                        $openingStock = ($variant->opening_stock ?? 0) > 0
                            ? $variant->opening_stock
                            : (($variant->current_stock ?? 0) + $totalAllocated);
                        $currentStock = $branchInventories->count() > 0
                            ? $branchCurrent
                            : ($variant->current_stock ?? 0);
                        $generalLeft = $currentStock;
                    } else {
                        if ($manager->addby) {
                            $openingStock = $totalAllocated;
                            $currentStock = $branchCurrent;
                            $generalLeft = $branchCurrent;
                        } else {
                            $openingStock = ($variant->current_stock ?? 0) + $totalAllocated;
                            $currentStock = $variant->current_stock ?? 0;
                            $generalLeft = $variant->current_stock ?? 0;
                        }
                    }
                    if (!$manager->addby || $branchInventories->count() > 0) {
                        $unit = $item->relationLoaded('unit') ? $item->getRelation('unit') : (Unit::find($item->getAttribute('unit')) ?? null);
                        $allItems->push([
                            'id' => $variant->id,
                            'type' => 'product_variant',
                            'parent_id' => $item->id,
                            'name' => $item->item_name . ' - ' . $variant->variant_name,
                            'code' => $variant->sku ?? $item->item_code,
                            'barcode' => $variant->barcode ?? $item->barcode,
                            'category' => $item->category_name,
                            'supplier' => $item->supplier,
                            'unit' => $unit,
                            'unit_abbreviation' => $unit?->abbreviation ?? $item->unit ?? null,
                            'image' => $item->item_image,
                            'cost_price' => $variant->cost_price ?? $variant->manual_cost_price ?? $variant->margin_cost_price ?? $variant->range_cost_price,
                            'selling_price' => $variant->selling_price ?? $variant->calculated_price ?? $variant->final_price,
                            'profit_margin' => $variant->profit_margin ?? $variant->target_margin,
                            'current_stock' => $currentStock,
                            'general_left' => $generalLeft,
                            'opening_stock' => $openingStock,
                            'stock_added' => (int) ($variant->stock_added ?? 0),
                            'actual_current_stock' => $currentStock,
                            'low_stock_threshold' => $variant->low_stock_threshold,
                            'variant_name' => $variant->variant_name,
                            'variant_options' => $variant->variant_options,
                            'pricing_tiers' => $variant->pricingTiers,
                            'created_at' => $variant->created_at ?? $item->created_at,
                            'data' => $variant,
                            'parent_data' => $item,
                            'branch_inventory_list' => $branch_inventory_list,
                        ]);
                    }
                }
            } else {
                if (!$manager->addby) {
                    $unit = $item->relationLoaded('unit') ? $item->getRelation('unit') : (Unit::find($item->getAttribute('unit')) ?? null);
                    $allItems->push([
                        'id' => $item->id,
                        'type' => 'variant',
                        'name' => $item->item_name,
                        'code' => $item->item_code,
                        'barcode' => $item->barcode,
                        'category' => $item->category_name,
                        'supplier' => $item->supplier,
                        'unit' => $unit,
                        'unit_abbreviation' => $unit?->abbreviation ?? $item->unit ?? null,
                        'image' => $item->item_image,
                        'variant_sets' => $item->variant_sets,
                        'variants' => $item->variants,
                        'current_stock' => $item->variants->sum(function ($variant) {
                            return $variant->current_stock ?? $variant->opening_stock ?? $variant->current_stock ?? 0;
                        }),
                        'general_left' => $item->variants->sum(function ($variant) {
                            return $variant->current_stock ?? $variant->opening_stock ?? $variant->current_stock ?? 0;
                        }),
                        'created_at' => $item->created_at,
                        'data' => $item,
                        'branch_inventory_list' => collect(),
                    ]);
                }
            }
        }

        // Sort by created_at descending
        $allItems = $allItems->sortByDesc('created_at');

        // Paginate the combined collection
        $perPage = 10;
        $currentPage = request()->get('page', 1);
        $allItemsPaginated = new LengthAwarePaginator(
            $allItems->forPage($currentPage, $perPage),
            $allItems->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // Get active subscription
        $activeSubscription = $manager->currentSubscription()->with('subscriptionPlan')->first();

        return view('manager.inventory.all_items.all_items', compact(
            'allItemsPaginated',
            'standardItems',
            'variantItems',
            'productVariants',
            'categories',
            'suppliers',
            'activeSubscription'
        ));
    }

    /**
     * Determine whether the current manager can edit or delete items.
     */
    private function canEditItems()
    {
        $manager = Auth::user();
        if (!$manager) {
            return false;
        }

        // Business creator / owner has full access
        if (empty($manager->addby)) {
            return true;
        }

        return user_has_feature('manager_edit_items_features', $manager);
    }


    public function delete_item($type, $id)
    {
        /** @var \App\Models\User $manager */
        $manager = Auth::user();
        $businessName = $manager->business_name;

        if (!$this->canEditItems()) {
            return redirect()->route('all_items')->with('error', 'You do not have permission to delete items. This must be enabled by your business creator.');
        }

        switch ($type) {
            case 'standard':
                $item = StandardItem::where('business_name', $businessName)->findOrFail($id);
                $itemName = $item->item_name;
                $item->forceDelete();
                break;
            case 'variant':
                $item = VariantItem::where('business_name', $businessName)->findOrFail($id);
                $itemName = $item->item_name;
                $item->forceDelete();
                break;
            case 'product_variant':
                $variant = ProductVariant::with('variantItem')->findOrFail($id);
                // Check business_name through parent
                if ($variant->variantItem->business_name !== $businessName) {
                    return redirect()->back()->with('error', 'Unauthorized access.');
                }
                $itemName = $variant->variantItem->item_name . ' - ' . $variant->variant_name;
                $variant->forceDelete();
                break;
            default:
                return redirect()->back()->with('error', 'Invalid item type specified.');
        }

        \App\Helpers\ActivityLogger::log('Delete item', json_encode(['type' => $type, 'id' => $id, 'name' => $itemName]));
        return redirect()->back()->with('success', ucfirst($type) . ' item permanently deleted successfully.');
    }


    public function show_item_details($type, $id)
    {
        try {
            $manager = Auth::user();
            $businessName = $manager->business_name;

            switch ($type) {
                case 'standard':
                    $item = StandardItem::with(['supplier', 'unit', 'pricingTiers'])
                        ->where('business_name', $businessName)
                        ->findOrFail($id);

                    // Get the unit object - the 'unit' field contains the unit ID
                    $unitId = $item->getAttribute('unit');
                    $unitObj = $unitId ? Unit::find($unitId) : null;

                    $formattedItem = [
                        'id' => $item->id,
                        'type' => 'standard',
                        'item_name' => $item->item_name,
                        'item_code' => $item->item_code,
                        'barcode' => $item->barcode,
                        'category' => $item->category,
                        'unit' => $unitObj,
                        'item_image' => $item->item_image,
                        'cost_price' => $item->cost_price,
                        'selling_price' => $item->selling_price,
                        'profit_margin' => $item->profit_margin,
                        'current_stock' => $item->current_stock,
                        'low_stock_threshold' => $item->low_stock_threshold,
                        'supplier' => $item->supplier,
                        'pricing_tiers' => $item->pricingTiers,
                        'description' => $item->description,
                        'updated_at' => $item->updated_at,
                        'created_at' => $item->created_at
                    ];
                    break;

                case 'variant':
                    // ✅ SECURITY: Verify variant item belongs to manager's business
                    $item = VariantItem::with(['supplier', 'unit', 'variants.pricingTiers'])
                        ->where('business_name', $businessName)
                        ->findOrFail($id);
                    $formattedItem = [
                        'id' => $item->id,
                        'type' => 'variant',
                        'item_name' => $item->item_name,
                        'item_code' => $item->item_code,
                        'barcode' => $item->barcode,
                        'category' => $item->category,
                        'unit' => $item->unit,
                        'item_image' => $item->item_image,
                        'variant_sets' => $item->variant_sets,
                        'variants' => $item->variants,
                        'current_stock' => $item->variants->sum('stock_quantity'),
                        'supplier' => $item->supplier,
                        'description' => $item->description,
                        'updated_at' => $item->updated_at,
                        'created_at' => $item->created_at
                    ];
                    break;

                case 'product_variant':
                    $variant = ProductVariant::with(['variantItem.supplier', 'variantItem.unit', 'pricingTiers'])
                        ->findOrFail($id);

                    // Check business_name through parent variantItem
                    if ($variant->variantItem->business_name !== $businessName) {
                        return response()->json(['error' => 'Unauthorized access.'], 403);
                    }

                    $formattedItem = [
                        'id' => $variant->id,
                        'type' => 'product_variant',
                        'parent_id' => $variant->variant_item_id,
                        'item_name' => $variant->variantItem->item_name . ' - ' . $variant->variant_name,
                        'variant_name' => $variant->variant_name,
                        'sku' => $variant->sku,
                        'barcode' => $variant->barcode,
                        'category' => $variant->variantItem->category,
                        'unit' => $variant->variantItem->unit,
                        'item_image' => $variant->variantItem->item_image,
                        'variant_options' => $variant->variant_options,
                        'cost_price' => $variant->cost_price ?? $variant->manual_cost_price ?? $variant->margin_cost_price ?? $variant->range_cost_price,
                        'selling_price' => $variant->selling_price ?? $variant->calculated_price ?? $variant->final_price,
                        'profit_margin' => $variant->profit_margin ?? $variant->target_margin,
                        'current_stock' => $variant->current_stock ?? $variant->opening_stock ?? $variant->current_stock ?? 0,
                        'low_stock_threshold' => $variant->low_stock_threshold,
                        'supplier' => $variant->variantItem->supplier,
                        'pricing_tiers' => $variant->pricingTiers,
                        'pricing_type' => $variant->pricing_type,
                        'description' => $variant->variantItem->description,
                        'expiry_date' => $variant->expiry_date,
                        'location' => $variant->location,
                        'updated_at' => $variant->updated_at,
                        'created_at' => $variant->created_at
                    ];
                    break;

                default:
                    return response()->json(['error' => 'Invalid item type specified.'], 400);
            }

            return response()->json(['item' => $formattedItem, 'success' => true]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Item not found or error occurred.',
                'message' => $e->getMessage()
            ], 404);
        }
    }

    public function edit_item($type, $id)
    {
        try {
            $manager = Auth::user();
            $businessName = $manager->business_name;

            if (!$this->canEditItems()) {
                return redirect()->route('all_items')->with('error', 'You do not have permission to edit items. This must be enabled by your business creator.');
            }

            $suppliers = Supplier::where('business_name', $businessName)->get();
            $units = Unit::all();
            $itemType = $type; // Define itemType variable

            switch ($type) {
                case 'standard':
                    $item = StandardItem::with(['supplier', 'unit', 'pricingTiers'])
                        ->where('business_name', $businessName)
                        ->findOrFail($id);
                    break;

                case 'variant':
                    $item = VariantItem::with(['supplier', 'unit', 'variants.pricingTiers'])
                        ->where('business_name', $businessName)
                        ->findOrFail($id);
                    break;

                case 'product_variant':
                    $item = ProductVariant::with(['variantItem.supplier', 'variantItem.unit', 'pricingTiers'])
                        ->findOrFail($id);
                    // Check business_name through parent
                    if ($item->variantItem->business_name !== $businessName) {
                        return redirect()->route('all_items')->with('error', 'Unauthorized access.');
                    }
                    break;

                default:
                    return redirect()->route('all_items')->with('error', 'Invalid item type specified.');
            }

            return view('manager.inventory.all_items.edit_item', compact('item', 'itemType', 'suppliers', 'units'));

        } catch (\Exception $e) {
            return redirect()->route('all_items')->with('error', 'Item not found: ' . $e->getMessage());
        }
    }

    public function update_item(Request $request, $type, $id)
    {
        try {
            $manager = Auth::user();
            $businessName = $manager->business_name;

            if (!$this->canEditItems()) {
                return redirect()->route('all_items')->with('error', 'You do not have permission to edit items. This must be enabled by your business creator.');
            }

            switch ($type) {
                case 'standard':
                    $item = StandardItem::where('business_name', $businessName)->findOrFail($id);

                    $validatedData = $request->validate([
                        'item_name' => 'required|string|max:255',
                        'item_code' => 'required|string|max:255',
                        'barcode' => 'nullable|string|max:255',
                        'category' => 'required|string|max:255',
                        'supplier_id' => 'nullable|exists:suppliers,id',
                        'unit' => 'nullable|string|max:255',
                        'description' => 'nullable|string',
                        'cost_price' => 'required|numeric|min:0',
                        'selling_price' => 'required|numeric|min:0',
                        'add_stock' => 'nullable|integer|min:0',
                        'low_stock_threshold' => 'nullable|integer|min:0',
                        'item_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
                    ]);

                    // Handle image upload - SECURE: Uses Laravel's storage
                    if ($request->hasFile('item_image')) {
                        $path = $request->file('item_image')->store('item_images', 'public');
                        $validatedData['item_image'] = $path;
                    }

                    // Calculate profit margin
                    if ($validatedData['cost_price'] > 0) {
                        $validatedData['profit_margin'] = (($validatedData['selling_price'] - $validatedData['cost_price']) / $validatedData['cost_price']) * 100;
                    }

                    if ($request->has('add_stock')) {
                        $stockIncrease = (int) ($validatedData['add_stock'] ?? 0);

                        if ($stockIncrease > 0) {
                            $validatedData['current_stock'] = (int) $item->current_stock + $stockIncrease;
                            $validatedData['stock_added'] = $stockIncrease;

                            foreach ($item->branchInventory as $branchInventory) {
                                $branchInventory->allocated_quantity += $stockIncrease;
                                $branchInventory->current_quantity += $stockIncrease;
                                $branchInventory->save();
                            }
                        }
                    }

                    $item->update($validatedData);
                    $itemName = $item->item_name ?? ($item->bundle_name ?? '');
                    \App\Helpers\ActivityLogger::log('Update item', json_encode(['type' => $type, 'id' => $id, 'name' => $itemName]));
                    break;

                case 'variant':
                    $item = VariantItem::where('business_name', $businessName)->findOrFail($id);

                    $validatedData = $request->validate([
                        'item_name' => 'required|string|max:255',
                        'item_code' => 'required|string|max:255',
                        'barcode' => 'nullable|string|max:255',
                        'category' => 'required|string|max:255',
                        'supplier_id' => 'nullable|exists:suppliers,id',
                        'unit_id' => 'nullable|exists:units,id',
                        'brand' => 'nullable|string|max:255',
                        'description' => 'nullable|string',
                        'item_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
                    ]);

                    // Handle image upload - SECURE: Uses Laravel's storage
                    if ($request->hasFile('item_image')) {
                        $path = $request->file('item_image')->store('item_images', 'public');
                        $validatedData['item_image'] = $path;
                    }

                    $item->update($validatedData);
                    break;

                case 'product_variant':
                    $item = ProductVariant::with('variantItem')->findOrFail($id);

                    // Check business_name through parent
                    if ($item->variantItem->business_name !== $businessName) {
                        return redirect()->route('all_items')->with('error', 'Unauthorized access.');
                    }

                    $validatedData = $request->validate([
                        'variant_name' => 'required|string|max:255',
                        'sku' => 'nullable|string|max:255',
                        'barcode' => 'nullable|string|max:255',
                        'cost_price' => 'nullable|numeric|min:0',
                        'selling_price' => 'nullable|numeric|min:0',
                        'add_stock' => 'nullable|integer|min:0',
                        'low_stock_threshold' => 'nullable|integer|min:0',
                        'variant_options' => 'nullable|string',
                    ]);

                    // Calculate profit margin if cost and selling prices are provided
                    if (isset($validatedData['cost_price']) && isset($validatedData['selling_price']) && $validatedData['cost_price'] > 0) {
                        $validatedData['profit_margin'] = (($validatedData['selling_price'] - $validatedData['cost_price']) / $validatedData['cost_price']) * 100;
                    }

                    if ($request->has('add_stock')) {
                        $stockIncrease = (int) ($validatedData['add_stock'] ?? 0);

                        if ($stockIncrease > 0) {
                            $validatedData['current_stock'] = (int) ($item->current_stock ?? 0) + $stockIncrease;
                            $validatedData['stock_added'] = $stockIncrease;

                            foreach (BranchInventory::where('item_id', $item->id)
                                ->where('item_type', 'variant')
                                ->get() as $branchInventory) {
                                $branchInventory->allocated_quantity += $stockIncrease;
                                $branchInventory->current_quantity += $stockIncrease;
                                $branchInventory->save();
                            }
                        }
                    }

                    $item->update($validatedData);
                    $itemName = $item->variantItem->item_name . ' - ' . $item->variant_name;
                    \App\Helpers\ActivityLogger::log('Update item', json_encode(['type' => $type, 'id' => $id, 'name' => $itemName]));
                    break;

                default:
                    return redirect()->route('all_items')->with('error', 'Invalid item type specified.');
            }

            return redirect()->route('all_items')->with('success', ucfirst($type) . ' item updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error updating item: ' . $e->getMessage())->withInput();
        }
    }

    public function delete_multiple(Request $request)
    {
        try {
            $manager = Auth::user();
            $businessName = $manager->business_name;

            if (!$this->canEditItems()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to delete items. This must be enabled by your business creator.',
                ], 403);
            }

            // Validate input
            $items = $request->input('items');

            // Check if items array is provided and is an array
            if (!$items || !is_array($items)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No items selected for deletion.'
                ], 400);
            }

            // Initialize counters
            $deletedCount = 0;
            $errors = [];

            // Loop through each item and delete based on type
            foreach ($items as $itemData) {
                $type = $itemData['type'] ?? null;
                $id = $itemData['id'] ?? null;

                if (!$type || !$id) {
                    continue;
                }

                try {
                    $itemName = '';
                    switch ($type) {
                        case 'standard':
                            $item = StandardItem::where('business_name', $businessName)->find($id);
                            if ($item) {
                                $itemName = $item->item_name;
                                $item->forceDelete();
                                $deletedCount++;
                                \App\Helpers\ActivityLogger::log('Delete item (multiple)', json_encode(['type' => $type, 'id' => $id, 'name' => $itemName]));
                            }
                            break;

                        case 'variant':
                            $item = VariantItem::where('business_name', $businessName)->find($id);
                            if ($item) {
                                $itemName = $item->item_name;
                                $item->forceDelete();
                                $deletedCount++;
                                \App\Helpers\ActivityLogger::log('Delete item (multiple)', json_encode(['type' => $type, 'id' => $id, 'name' => $itemName]));
                            }
                            break;

                        default:
                            $errors[] = "Invalid item type: {$type} for ID: {$id}";
                    }
                } catch (\Exception $e) {
                    $errors[] = "Error deleting {$type} item ID {$id}: " . $e->getMessage();
                }
            }

            if ($deletedCount > 0) {
                $message = "Successfully deleted {$deletedCount} item" . ($deletedCount > 1 ? 's' : '');
                if (!empty($errors)) {
                    $message .= " (with some errors)";
                }

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'deleted_count' => $deletedCount,
                    'errors' => $errors
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No items were deleted.',
                    'errors' => $errors
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting items: ' . $e->getMessage()
            ], 500);
        }
    }
}
