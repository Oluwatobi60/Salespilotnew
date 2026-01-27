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

class AllItemsController extends Controller
{
    public function all_items()
    {
        // Get manager information
        $manager = Auth::user();
        $businessName = $manager->business_name;

        // Fetch Standard Items with related data filtered by business_name
        $standardItems = StandardItem::with([
            'supplier',
            'pricingTiers'
        ])
        ->where('business_name', $businessName)
        ->latest()->get();

        // Fetch Variant Items with related data filtered by business_name
        $variantItems = VariantItem::with([
            'supplier',
            'unit',
            'variants.pricingTiers'
        ])
        ->where('business_name', $businessName)
        ->latest()->get();

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
        $suppliers = Supplier::where('business_name', $businessName)->orderBy('name')->get();

        // Combine all items into a single collection
        $allItems = collect();

        // Add standard items with type identifier
        foreach ($standardItems as $item) {
            $allItems->push([
                'id' => $item->id,
                'type' => 'standard',
                'name' => $item->item_name,
                'code' => $item->item_code,
                'barcode' => $item->barcode,
                'category' => $item->category_name,
                'supplier' => $item->supplier,
                'unit' => $item->unit,
                'image' => $item->item_image,
                'cost_price' => $item->cost_price,
                'selling_price' => $item->selling_price,
                'profit_margin' => $item->profit_margin,
                'current_stock' => $item->current_stock,
                'low_stock_threshold' => $item->low_stock_threshold,
                'pricing_tiers' => $item->pricingTiers,
                'created_at' => $item->created_at,
                'data' => $item
            ]);
        }

        // Add variant items with type identifier
        foreach ($variantItems as $item) {
            $allItems->push([
                'id' => $item->id,
                'type' => 'variant',
                'name' => $item->item_name,
                'code' => $item->item_code,
                'barcode' => $item->barcode,
                'category' => $item->category_name,
                'supplier' => $item->supplier,
                'unit' => $item->unit,
                'image' => $item->item_image,
                'variant_sets' => $item->variant_sets,
                'variants' => $item->variants,
                'created_at' => $item->created_at,
                'data' => $item
            ]);
        }

        // Sort by created_at descending
        $allItems = $allItems->sortByDesc('created_at');

        // Paginate the combined collection
        $perPage = 8;
        $currentPage = request()->get('page', 1);
        $allItemsPaginated = new LengthAwarePaginator(
            $allItems->forPage($currentPage, $perPage),
            $allItems->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('manager.inventory.all_items.all_items', compact(
            'allItemsPaginated',
            'standardItems',
            'variantItems',
            'productVariants',
            'categories',
            'suppliers'
        ));
    }


    public function delete_item($type, $id)
    {
        $manager = Auth::user();
        $businessName = $manager->business_name;

        switch ($type) {
            case 'standard':
                $item = StandardItem::where('business_name', $businessName)->findOrFail($id);
                break;
            case 'variant':
                $item = VariantItem::where('business_name', $businessName)->findOrFail($id);
                break;
            default:
                return redirect()->back()->with('error', 'Invalid item type specified.');
        }

        $itemName = $item->item_name ?? ($item->bundle_name ?? '');
        $item->forceDelete();
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
                    $item = StandardItem::with(['supplier', 'pricingTiers'])
                        ->where('business_name', $businessName)
                        ->findOrFail($id);
                    $formattedItem = [
                        'id' => $item->id,
                        'type' => 'standard',
                        'item_name' => $item->item_name,
                        'item_code' => $item->item_code,
                        'barcode' => $item->barcode,
                        'category' => $item->category,
                        'unit' => $item->unit,
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
                    $item = VariantItem::with(['supplier', 'unit', 'variants.pricingTiers'])->findOrFail($id);
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

            $suppliers = Supplier::where('business_name', $businessName)->get();
            $units = Unit::all();
            $itemType = $type; // Define itemType variable

            switch ($type) {
                case 'standard':
                    $item = StandardItem::with(['supplier', 'pricingTiers'])
                        ->where('business_name', $businessName)
                        ->findOrFail($id);
                    break;

                case 'variant':
                    $item = VariantItem::with(['supplier', 'unit', 'variants.pricingTiers'])
                        ->where('business_name', $businessName)
                        ->findOrFail($id);
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
                        'current_stock' => 'nullable|integer|min:0',
                        'low_stock_threshold' => 'nullable|integer|min:0',
                        'item_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
                    ]);

                    // Handle image upload
                    if ($request->hasFile('item_image')) {
                        $image = $request->file('item_image');
                        $imageName = time() . '_' . $image->getClientOriginalName();
                        $imagePath = $image->move(public_path('uploads/item_images'), $imageName);
                        $validatedData['item_image'] = 'uploads/item_images/' . $imageName;
                    }

                    // Calculate profit margin
                    if ($validatedData['cost_price'] > 0) {
                        $validatedData['profit_margin'] = (($validatedData['selling_price'] - $validatedData['cost_price']) / $validatedData['cost_price']) * 100;
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

                    // Handle image upload
                    if ($request->hasFile('item_image')) {
                        $image = $request->file('item_image');
                        $imageName = time() . '_' . $image->getClientOriginalName();
                        $imagePath = $image->move(public_path('uploads/item_images'), $imageName);
                        $validatedData['item_image'] = 'uploads/item_images/' . $imageName;
                    }

                    $item->update($validatedData);
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
