<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\VariantItem;
use App\Models\ProductVariant;
use App\Models\VariantPricingTier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class VariantItemController extends Controller
{
    public function createvariant(Request $request)
    {
        try {
            // Validate the base item data
            $validatedData = $request->validate([
                'item_name' => 'required|string|max:255',
                'item_code' => 'nullable|string|max:255|unique:variant_items',
                'barcode' => 'nullable|string|max:255',
                'category' => 'required|string|max:255',
                'supplier_id' => 'nullable|exists:suppliers,id',
                'unit_id' => 'nullable|exists:units,id',
                'brand' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'item_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'variants' => 'required|array|min:1',
                'variants.*.name' => 'required|string|max:255',
                'variants.*.sku' => 'required|string|max:255',
                'variants.*.barcode' => 'nullable|string|max:255',
                'variants.*.is_sellable' => 'nullable|boolean',
                'variants.*.cost_price' => 'required|numeric|min:0',
                'variants.*.selling_price' => 'nullable|numeric|min:0',
                'variants.*.pricing_method' => 'nullable|string|in:fixed,manual,margin,range',
                'variants.*.stock_quantity' => 'nullable|integer|min:0',
                'variants.*.low_stock_threshold' => 'nullable|integer|min:0',
                'variants.*.primary_value' => 'nullable|string',
                'variants.*.secondary_value' => 'nullable|string',
                'variants.*.tertiary_value' => 'nullable|string',
                // Fixed pricing fields
                'variants.*.profit_margin' => 'nullable|numeric',
                'variants.*.potential_profit' => 'nullable|numeric',
                'variants.*.tax_rate' => 'nullable|numeric|min:0',
               /*  'variants.*.discount' => 'nullable|numeric|min:0', */
                'variants.*.final_price' => 'nullable|numeric|min:0',
                // Manual pricing fields
                'variants.*.manual_cost_price' => 'nullable|numeric|min:0',
                // Margin pricing fields
                'variants.*.margin_cost_price' => 'nullable|numeric|min:0',
                'variants.*.target_margin' => 'nullable|numeric',
                'variants.*.calculated_price' => 'nullable|numeric|min:0',
                'variants.*.margin_profit' => 'nullable|numeric',
                // Range pricing fields
                'variants.*.range_cost_price' => 'nullable|numeric|min:0',
                'variants.*.min_price' => 'nullable|numeric|min:0',
                'variants.*.max_price' => 'nullable|numeric|min:0',
                'variants.*.range_potential_profit' => 'nullable|numeric',
                // Stock management fields
                'variants.*.expiry_date' => 'nullable|date',
                'variants.*.location' => 'nullable|string|max:255',
            ]);

            // Start database transaction
            DB::beginTransaction();

            // Generate item code if not provided
            if (empty($validatedData['item_code'])) {
                $validatedData['item_code'] = 'VAR-' . strtoupper(substr($validatedData['item_name'], 0, 3)) . '-' . time();
            }

            // Handle file upload
            if ($request->hasFile('item_image')) {
                $image = $request->file('item_image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('uploads/item_images'), $imageName);
                $validatedData['item_image'] = 'uploads/item_images/' . $imageName;
            }

            // Extract variant sets configuration from the first variant
            $variantSets = $this->extractVariantSets($request->input('variants'));
            $validatedData['variant_sets'] = $variantSets;

            // Get manager information
            $manager = Auth::user();
            $managerName = trim(($manager->firstname ?? '') . ' ' . ($manager->othername ?? '') . ' ' . ($manager->surname ?? ''));

            // Add manager info to validated data
            $validatedData['business_name'] = $manager->business_name;
            $validatedData['manager_name'] = $managerName;
            $validatedData['manager_email'] = $manager->email;

            // Create the variant item (base item)
            $variantItem = VariantItem::create($validatedData);

            // Process each variant
            $variantsData = $request->input('variants');
            foreach ($variantsData as $index => $variantData) {
                // Build variant options array
                $variantOptions = [];
                if (!empty($variantData['primary_value'])) {
                    $variantOptions['set1'] = $variantData['primary_value'];
                }
                if (!empty($variantData['secondary_value'])) {
                    $variantOptions['set2'] = $variantData['secondary_value'];
                }
                if (!empty($variantData['tertiary_value'])) {
                    $variantOptions['set3'] = $variantData['tertiary_value'];
                }

                // Determine pricing type
                $pricingType = $variantData['pricing_method'] ?? 'fixed';

                // Prepare variant data
                $productVariantData = [
                    'variant_item_id' => $variantItem->id,
                    'variant_name' => $variantData['name'],
                    'sku' => $variantData['sku'],
                    'barcode' => $variantData['barcode'] ?? null,
                    'variant_options' => $variantOptions,
                    'primary_value' => $variantData['primary_value'] ?? null,
                    'secondary_value' => $variantData['secondary_value'] ?? null,
                    'tertiary_value' => $variantData['tertiary_value'] ?? null,
                    'sell_item' => isset($variantData['is_sellable']) && $variantData['is_sellable'] ? true : false,
                    'pricing_type' => $pricingType,
                    'stock_quantity' => $variantData['stock_quantity'] ?? 0,
                ];

                // Add pricing fields based on pricing type
                switch ($pricingType) {
                    case 'fixed':
                        $productVariantData['cost_price'] = $variantData['cost_price'] ?? 0;
                        $productVariantData['selling_price'] = $variantData['selling_price'] ?? 0;
                        $productVariantData['profit_margin'] = !empty($variantData['profit_margin']) ? $variantData['profit_margin'] : null;
                        $productVariantData['potential_profit'] = !empty($variantData['potential_profit']) ? $variantData['potential_profit'] : null;
                        $productVariantData['tax_rate'] = !empty($variantData['tax_rate']) ? $variantData['tax_rate'] : 0;
                        /* $productVariantData['discount'] = !empty($variantData['discount']) ? $variantData['discount'] : 0; */
                        $productVariantData['final_price'] = !empty($variantData['final_price']) ? $variantData['final_price'] : ($variantData['selling_price'] ?? 0);
                        break;

                    case 'manual':
                        $productVariantData['manual_cost_price'] = !empty($variantData['manual_cost_price']) ? $variantData['manual_cost_price'] : ($variantData['cost_price'] ?? 0);
                        break;

                    case 'margin':
                        $productVariantData['margin_cost_price'] = !empty($variantData['margin_cost_price']) ? $variantData['margin_cost_price'] : ($variantData['cost_price'] ?? 0);
                        $productVariantData['target_margin'] = !empty($variantData['target_margin']) ? $variantData['target_margin'] : null;
                        $productVariantData['calculated_price'] = !empty($variantData['calculated_price']) ? $variantData['calculated_price'] : null;
                        $productVariantData['margin_profit'] = !empty($variantData['margin_profit']) ? $variantData['margin_profit'] : null;
                        $productVariantData['tax_rate'] = !empty($variantData['tax_rate']) ? $variantData['tax_rate'] : 0;
                       /*  $productVariantData['discount'] = !empty($variantData['discount']) ? $variantData['discount'] : 0; */
                        $productVariantData['final_price'] = !empty($variantData['final_price']) ? $variantData['final_price'] : ($variantData['calculated_price'] ?? 0);
                        break;

                    case 'range':
                        $productVariantData['range_cost_price'] = !empty($variantData['range_cost_price']) ? $variantData['range_cost_price'] : ($variantData['cost_price'] ?? 0);
                        $productVariantData['min_price'] = !empty($variantData['min_price']) ? $variantData['min_price'] : null;
                        $productVariantData['max_price'] = !empty($variantData['max_price']) ? $variantData['max_price'] : null;
                        $productVariantData['range_potential_profit'] = !empty($variantData['range_potential_profit']) ? $variantData['range_potential_profit'] : null;
                        $productVariantData['tax_rate'] = !empty($variantData['tax_rate']) ? $variantData['tax_rate'] : 0;
                       /*  $productVariantData['discount'] = !empty($variantData['discount']) ? $variantData['discount'] : 0; */
                        $productVariantData['final_price'] = !empty($variantData['final_price']) ? $variantData['final_price'] : null;
                        break;
                }

                // Add stock management fields if provided
                $productVariantData['low_stock_threshold'] = !empty($variantData['low_stock_threshold']) ? $variantData['low_stock_threshold'] : null;
                $productVariantData['expiry_date'] = !empty($variantData['expiry_date']) ? $variantData['expiry_date'] : null;
                $productVariantData['location'] = !empty($variantData['location']) ? $variantData['location'] : null;

                // Create the product variant
                $productVariant = ProductVariant::create($productVariantData);

                // Handle pricing tiers for range pricing
                if ($pricingType === 'range' && isset($variantData['pricing_tiers']) && is_array($variantData['pricing_tiers'])) {
                    foreach ($variantData['pricing_tiers'] as $tier) {
                        if (!empty($tier['min_quantity']) && !empty($tier['price_per_unit'])) {
                            VariantPricingTier::create([
                                'product_variant_id' => $productVariant->id,
                                'min_quantity' => $tier['min_quantity'],
                                'max_quantity' => $tier['max_quantity'] ?? null,
                                'price_per_unit' => $tier['price_per_unit'],
                            ]);
                        }
                    }
                }
            }

            // Commit the transaction
            DB::commit();

            // Redirect to dashboard with success message
            return redirect()->route('manager')
                ->with('success', 'Variant item "' . $variantItem->item_name . '" with ' . count($variantsData) . ' variants created successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error creating variant item: ' . json_encode($e->errors()));
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors());

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating variant item: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create variant item: ' . $e->getMessage()]);
        }
    }

    /**
     * Extract variant sets configuration from variants data
     */
    private function extractVariantSets(array $variants)
    {
        $variantSets = [];

        // Get the first variant to extract set names
        $firstVariant = reset($variants);

        if (!empty($firstVariant['primary_value'])) {
            $variantSets['set1'] = 'Primary Set'; // You might want to pass this from the form
        }

        if (!empty($firstVariant['secondary_value'])) {
            $variantSets['set2'] = 'Secondary Set';
        }

        if (!empty($firstVariant['tertiary_value'])) {
            $variantSets['set3'] = 'Tertiary Set';
        }

        return $variantSets;
    }
}
