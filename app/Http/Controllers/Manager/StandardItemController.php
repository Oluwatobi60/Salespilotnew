<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\StandardItem;
use App\Models\PricingTier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class StandardItemController extends Controller
{
    public function createstandard(Request $request)
    {
        try {
            // Clean profit_margin BEFORE validation
            if ($request->has('profit_margin') && !empty($request->profit_margin)) {
                // Remove percentage symbol and spaces from profit_margin
                $cleanedMargin = str_replace(['%', ' '], '', $request->profit_margin);
                // Convert to float if numeric, otherwise set to null
                $request->merge(['profit_margin' => is_numeric($cleanedMargin) ? (float)$cleanedMargin : null]);
            }

            // Validate the incoming request data
            $validatedData = $request->validate([
                'item_name' => 'required|string|max:255',
                'item_code' => 'nullable|string|max:255|unique:standard_items',
                'category' => 'required|string|max:255',
                'supplier_id' => 'nullable|exists:suppliers,id',
                'unit' => 'required|string|max:50',
                'barcode' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'item_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'enable_sale' => 'nullable|boolean',
                'pricing_type' => 'required|in:fixed,manual,margin,range',
                'cost_price' => 'required|numeric|min:0',
                'selling_price' => 'nullable|numeric|min:0',
                'profit_margin' => 'nullable|numeric',
                'potential_profit' => 'nullable|numeric',
                'target_margin' => 'nullable|numeric',
                'calculated_price' => 'nullable|numeric|min:0',
                'margin_profit' => 'nullable|numeric',
                'min_price' => 'nullable|numeric|min:0',
                'max_price' => 'nullable|numeric|min:0',
                'range_potential_profit' => 'nullable|numeric',
                'tax_rate' => 'nullable|numeric|min:0|max:100',
               /*  'discount' => 'nullable|numeric|min:0|max:100', */
                'final_price' => 'nullable|numeric|min:0',
                'track_stock' => 'nullable|boolean',
                'opening_stock' => 'nullable|integer|min:0',
                'low_stock_threshold' => 'nullable|integer|min:0',
                'expiry_date' => 'nullable|date',
                'location' => 'nullable|string|max:255',
            ]);

            // Set defaults
            $validatedData['enable_sale'] = $request->has('enable_sale') ? 1 : 0;
            $validatedData['track_stock'] = $request->has('track_stock') ? 1 : 0;
            $validatedData['current_stock'] = $validatedData['opening_stock'] ?? 0;

            // Handle file upload
            if ($request->hasFile('item_image')) {
                $image = $request->file('item_image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('uploads/item_images'), $imageName);
                $validatedData['item_image'] = 'uploads/item_images/' . $imageName;
            }

            // Generate item code if not provided
            if (empty($validatedData['item_code'])) {
                $validatedData['item_code'] = 'STD-' . strtoupper(substr($validatedData['item_name'], 0, 3)) . '-' . time();
            }

            // Create the standard item
            $standardItem = StandardItem::create($validatedData);

            // Handle pricing tiers for range pricing
            if ($request->pricing_type === 'range' && $request->has('pricing_tiers')) {
                $tiers = $request->input('pricing_tiers');
                foreach ($tiers as $tier) {
                    if (!empty($tier['min_quantity']) && !empty($tier['max_quantity']) && !empty($tier['price_per_unit'])) {
                        PricingTier::create([
                            'standard_item_id' => $standardItem->id,
                            'min_quantity' => $tier['min_quantity'],
                            'max_quantity' => $tier['max_quantity'],
                            'price_per_unit' => $tier['price_per_unit'],
                        ]);
                    }
                }
            }

            // Redirect to dashboard with success message
            return redirect()->route('manager')->with('success', 'Standard item "' . $standardItem->item_name . '" added successfully!');

        } catch (\Exception $e) {
            // Log the error
            Log::error('Error creating standard item: ' . $e->getMessage());

            // Redirect back with error
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create item: ' . $e->getMessage()]);
        }
    }
}
