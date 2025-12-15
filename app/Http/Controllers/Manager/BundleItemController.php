<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\BundleItem;
use App\Models\BundleComponent;
use App\Models\StandardItem;
use App\Models\ProductVariant;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BundleItemController extends Controller
{
    /**
     * Display the add bundle item form
     */
    public function create()
    {
        $suppliers = Supplier::all();
        $units = Unit::all();
        $standardItems = StandardItem::where('current_stock', '>', 0)->get();
        $variantItems = ProductVariant::where('stock_quantity', '>', 0)->get();

        return view('manager.bundleItems.add_item_bundle', compact('suppliers', 'units', 'standardItems', 'variantItems'));
    }

    /**
     * Store a new bundle item
     */
    public function store(Request $request)
    {
        try {
            // Validate the bundle data
            $validatedData = $request->validate([
                'bundle_name' => 'required|string|max:255',
                'bundle_code' => 'nullable|string|max:255|unique:bundle_items',
                'category' => 'required|string|max:255',
                'supplier_id' => 'nullable|exists:suppliers,id',
                'unit' => 'required|string|max:255',
                'barcode' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'bundle_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

                // Pricing
                'assembly_fee' => 'nullable|numeric|min:0',
                'bundle_selling_price' => 'required|numeric|min:0',
                'tax_rate' => 'nullable|numeric|min:0',

                // Stock
                'initial_bundle_stock' => 'required|integer|min:0',
                'low_stock_alert' => 'nullable|integer|min:0',
                'storage_location' => 'nullable|string|max:255',
                'expiry_date' => 'nullable|date',

                // Bundle components
                'bundle_items' => 'required|array|min:2',
                'bundle_items.*' => 'required|string',
                'bundle_quantities' => 'required|array|min:2',
                'bundle_quantities.*' => 'required|integer|min:1',
            ]);

            // Start database transaction
            DB::beginTransaction();

            // Generate bundle code if not provided
            if (empty($validatedData['bundle_code'])) {
                $validatedData['bundle_code'] = 'BND-' . strtoupper(substr($validatedData['bundle_name'], 0, 3)) . '-' . time();
            }

            // Handle file upload
            if ($request->hasFile('bundle_image')) {
                $image = $request->file('bundle_image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('uploads/item_images'), $imageName);
                $validatedData['bundle_image'] = 'uploads/item_images/' . $imageName;
            }

            // Parse bundle items (format: "type:id" e.g., "standard:1" or "variant:5")
            $bundleItems = $request->input('bundle_items');
            $bundleQuantities = $request->input('bundle_quantities');

            // Calculate bundle costs and validate stock
            $totalItemCost = 0;
            $individualTotal = 0;
            $componentData = [];
            $maxPossibleBundles = PHP_INT_MAX;

            foreach ($bundleItems as $index => $itemString) {
                list($type, $id) = explode(':', $itemString);
                $quantity = $bundleQuantities[$index];

                if ($type === 'standard') {
                    $item = StandardItem::findOrFail($id);
                    $unitCost = $item->cost_price;
                    $sellingPrice = $item->selling_price;
                    $availableStock = $item->current_stock;
                    $productName = $item->item_name;
                } elseif ($type === 'variant') {
                    $item = ProductVariant::findOrFail($id);
                    $unitCost = $item->cost_price ?? $item->manual_cost_price ?? $item->margin_cost_price ?? $item->range_cost_price ?? 0;
                    $sellingPrice = $item->selling_price ?? $item->calculated_price ?? 0;
                    $availableStock = $item->stock_quantity;
                    $productName = $item->variant_name;
                } else {
                    throw new \Exception('Invalid product type: ' . $type);
                }

                // Check if sufficient stock is available
                if ($availableStock < $quantity) {
                    throw new \Exception("Insufficient stock for {$productName}. Available: {$availableStock}, Required: {$quantity}");
                }

                // Calculate max possible bundles based on this component
                $possibleFromThisItem = floor($availableStock / $quantity);
                $maxPossibleBundles = min($maxPossibleBundles, $possibleFromThisItem);

                // Calculate costs
                $subtotal = $unitCost * $quantity;
                $totalItemCost += $subtotal;
                $individualTotal += $sellingPrice * $quantity;

                // Store component data for later insertion
                $componentData[] = [
                    'product_type' => $type,
                    'product_id' => $type === 'standard' ? $id : null,
                    'variant_id' => $type === 'variant' ? $id : null,
                    'quantity_in_bundle' => $quantity,
                    'unit_cost' => $unitCost,
                    'subtotal' => $subtotal,
                ];
            }

            // Check if initial stock doesn't exceed max possible bundles
            if ($validatedData['initial_bundle_stock'] > $maxPossibleBundles) {
                throw new \Exception("Cannot create {$validatedData['initial_bundle_stock']} bundles. Maximum possible: {$maxPossibleBundles} based on component stock.");
            }

            // Calculate pricing
            $assemblyFee = $validatedData['assembly_fee'] ?? 0;
            $totalBundleCost = $totalItemCost + $assemblyFee;
            $bundleSellingPrice = $validatedData['bundle_selling_price'];
            $customerSavings = $individualTotal - $bundleSellingPrice;
            $bundleProfit = $bundleSellingPrice - $totalBundleCost;
            $profitMargin = $totalBundleCost > 0 ? (($bundleProfit / $totalBundleCost) * 100) : 0;

            // Create the bundle item
            $bundleItem = BundleItem::create([
                'bundle_name' => $validatedData['bundle_name'],
                'bundle_code' => $validatedData['bundle_code'],
                'category' => $validatedData['category'],
                'supplier_id' => $validatedData['supplier_id'] ?? null,
                'unit' => $validatedData['unit'],
                'barcode' => $validatedData['barcode'] ?? null,
                'description' => $validatedData['description'] ?? null,
                'bundle_image' => $validatedData['bundle_image'] ?? null,
                'total_item_cost' => $totalItemCost,
                'assembly_fee' => $assemblyFee,
                'total_bundle_cost' => $totalBundleCost,
                'bundle_selling_price' => $bundleSellingPrice,
                'individual_total' => $individualTotal,
                'customer_savings' => $customerSavings,
                'profit_margin' => $profitMargin,
                'bundle_profit' => $bundleProfit,
                'tax_rate' => $validatedData['tax_rate'] ?? 0,
                'max_possible_bundles' => $maxPossibleBundles,
                'current_stock' => $validatedData['initial_bundle_stock'],
                'low_stock_threshold' => $validatedData['low_stock_alert'] ?? null,
                'storage_location' => $validatedData['storage_location'] ?? null,
                'expiry_date' => $validatedData['expiry_date'] ?? null,
            ]);

            // Create bundle components
            foreach ($componentData as $component) {
                BundleComponent::create([
                    'bundle_item_id' => $bundleItem->id,
                    'product_id' => $component['product_id'],
                    'variant_id' => $component['variant_id'],
                    'product_type' => $component['product_type'],
                    'quantity_in_bundle' => $component['quantity_in_bundle'],
                    'unit_cost' => $component['unit_cost'],
                    'subtotal' => $component['subtotal'],
                ]);
            }

            // Deduct stock from component items for initial bundle stock
            $initialStock = $validatedData['initial_bundle_stock'];
            if ($initialStock > 0) {
                foreach ($componentData as $component) {
                    $totalToDeduct = $component['quantity_in_bundle'] * $initialStock;

                    if ($component['product_type'] === 'standard') {
                        StandardItem::where('id', $component['product_id'])
                            ->decrement('current_stock', $totalToDeduct);
                    } elseif ($component['product_type'] === 'variant') {
                        ProductVariant::where('id', $component['variant_id'])
                            ->decrement('stock_quantity', $totalToDeduct);
                    }
                }
            }

            // Commit the transaction
            DB::commit();

            // Redirect to dashboard with success message
            return redirect()->route('manager')
                ->with('success', 'Bundle "' . $bundleItem->bundle_name . '" with ' . count($componentData) . ' components created successfully! Initial stock: ' . $initialStock . ' bundles.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error creating bundle item: ' . json_encode($e->errors()));
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors());

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating bundle item: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create bundle item: ' . $e->getMessage()]);
        }
    }
}
