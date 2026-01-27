<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\StandardItem;
use App\Models\VariantItem;
use App\Models\ProductVariant;
use App\Models\Category;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\User;

class SellProductController extends Controller
{
    public function sell_product()
    {
        // Get manager information
        $manager = Auth::user();
        $businessName = $manager->business_name;

        // Fetch all StandardItems with their associated relationships filtered by business_name
        $standard_items = StandardItem::with([
            'supplier',           // Supplier relationship
            'pricingTiers'       // Pricing tiers for bulk/quantity pricing
        ])
        ->where('enable_sale', true)
        ->where('business_name', $businessName)
        ->get();

        // Fetch all VariantItems with their associated relationships filtered by business_name
        $variant_items = VariantItem::with([
            'supplier',          // Supplier relationship
            'unit',              // Unit of measurement
            'variants' => function($query) {
                $query->where('sell_item', true)->with('pricingTiers'); // Only sellable variants with their pricing tiers
            }
        ])
        ->where('business_name', $businessName)
        ->get();

        // Get all unique categories filtered by business_name
        $categories = Category::where('business_name', $businessName)->orderBy('category_name')->get();

        // Merge both collections for a unified item list
        $all_items = collect([]);

        // Add standard items with type identifier
        foreach ($standard_items as $item) {
            $item->item_type = 'standard';
            $all_items->push($item);
        }

        // Add variant items with type identifier
        foreach ($variant_items as $item) {
            $item->item_type = 'variant';
            $all_items->push($item);
        }

        return view('manager.sell.sell_product', compact('all_items', 'standard_items', 'variant_items', 'categories'));
    }

    public function save_cart(Request $request)
    {
        try {
            $validated = $request->validate([
                'cart_name' => 'required|string|max:255',
                'customer_id' => 'nullable|integer',
                'customer_name' => 'nullable|string',
                'items' => 'required|array',
                'items.*.id' => 'required',
                'items.*.code' => 'nullable|string',
                'items.*.type' => 'required|string',
                'items.*.name' => 'required|string',
                'items.*.price' => 'required|numeric',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.note' => 'nullable|string',
                'items.*.img' => 'nullable|string',
                'total' => 'required|numeric',
                'note' => 'nullable|string'
            ]);

            $sessionId = Str::uuid();

            // Get manager information
            $manager = Auth::user();
            $managerName = trim(($manager->firstname ?? '') . ' ' . ($manager->othername ?? '') . ' ' . ($manager->surname ?? ''));

            foreach ($validated['items'] as $item) {
                $itemType = isset($item['type']) ? $item['type'] : 'standard';
                $itemCode = isset($item['code']) ? $item['code'] : null;
                CartItem::create([
                    'business_name' => $manager->business_name,
                    'manager_name' => $managerName,
                    'manager_email' => $manager->email,
                    'cart_name' => $validated['cart_name'],
                    'customer_id' => $validated['customer_id'],
                    'customer_name' => $validated['customer_name'] ?? 'Walk-in Customer',
                    'item_id' => $item['id'],
                    'item_code' => $itemCode,
                    'item_type' => $itemType,
                    'item_name' => $item['name'],
                    'item_price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'note' => $item['note'] ?? null,
                    'item_image' => $item['img'] ?? null,
                    'subtotal' => $item['price'] * $item['quantity'],
                    'discount' => 0,
                    'total' => $item['price'] * $item['quantity'],
                    'status' => 'saved',
                    'session_id' => $sessionId,
                    'user_id' => Auth::id()
                ]);
                \App\Helpers\ActivityLogger::log('add_to_cart', 'Manager added item to cart: ' . ($item['name'] ?? ''));
            }

            return response()->json([
                'success' => true,
                'message' => 'Cart saved successfully',
                'session_id' => $sessionId
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save cart: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkout(Request $request)
    {
        // Process the checkout and create CartItem entries with 'completed' status
        try {
            $validated = $request->validate([
                'customer_id' => 'nullable|integer',
                'customer_name' => 'nullable|string',
                'items' => 'required|array',
                'items.*.id' => 'required',
                'items.*.code' => 'nullable|string',
                'items.*.type' => 'required|string',
                'items.*.name' => 'required|string',
                'items.*.price' => 'required|numeric',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.note' => 'nullable|string',
                'items.*.img' => 'nullable|string',
                'total' => 'required|numeric',
                'discount' => 'nullable|numeric',
                'discount_id' => 'nullable|integer|exists:add_discounts,id'
            ]);

            // Log checkout activity for manager
            $details = [
                'customer_id' => $request->input('customer_id'),
                'customer_name' => $request->input('customer_name'),
                'total' => $request->input('total'),
                'discount' => $request->input('discount'),
                'items_count' => is_array($request->input('items')) ? count($request->input('items')) : 0,
            ];
            \App\Helpers\ActivityLogger::log('Checkout completed', json_encode($details));


            $sessionId = Str::uuid();
            $receiptNumber = 'RCPT-' . strtoupper(substr($sessionId, 0, 8));
            $discount = $validated['discount'] ?? 0;
            $discountId = $validated['discount_id'] ?? null;

            // Get manager information
            $manager = Auth::user();
            $managerName = trim(($manager->firstname ?? '') . ' ' . ($manager->othername ?? '') . ' ' . ($manager->surname ?? ''));

            $cartSubtotal = 0;
            foreach ($validated['items'] as $item) {
                $cartSubtotal += $item['price'] * $item['quantity'];
            }

            foreach ($validated['items'] as $item) {
                $itemSubtotal = $item['price'] * $item['quantity'];
                // Avoid division by zero, and use subtotal for proportional discount
                $itemDiscount = ($cartSubtotal > 0 && $discount > 0) ? ($discount * ($itemSubtotal / $cartSubtotal)) : 0;
                $itemTotal = $itemSubtotal - $itemDiscount;

                $itemType = isset($item['type']) ? $item['type'] : 'standard';
                $itemCode = isset($item['code']) ? $item['code'] : null;
                CartItem::create([
                    'business_name' => $manager->business_name,
                    'manager_name' => $managerName,
                    'manager_email' => $manager->email,
                    'cart_name' => 'Sale - ' . now()->format('Y-m-d H:i'),
                    'customer_id' => $validated['customer_id'],
                    'customer_name' => $validated['customer_name'] ?? 'Walk-in Customer',
                    'item_id' => $item['id'],
                    'item_code' => $itemCode,
                    'item_type' => $itemType,
                    'item_name' => $item['name'],
                    'item_price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'note' => $item['note'] ?? null,
                    'item_image' => $item['img'] ?? null,
                    'subtotal' => $itemSubtotal,
                    'discount' => $itemDiscount,
                    'discount_id' => $discountId,
                    'total' => $itemTotal,
                    'status' => 'completed',
                    'session_id' => $sessionId,
                    'receipt_number' => $receiptNumber,
                    'user_id' => Auth::id()
                ]);

                // Update stock for standard and variant items
                if ($itemType === 'standard') {
                    $standardItem = StandardItem::find($item['id']);
                    if ($standardItem) {
                        $standardItem->current_stock -= $item['quantity'];
                        if ($standardItem->current_stock < 0) {
                            $standardItem->current_stock = 0;
                        }
                        $standardItem->save();
                    }
                } elseif ($itemType === 'variant') {
                    $productVariant = ProductVariant::find($item['id']);
                    if ($productVariant) {
                        $productVariant->stock_quantity -= $item['quantity'];
                        if ($productVariant->stock_quantity < 0) {
                            $productVariant->stock_quantity = 0;
                        }
                        $productVariant->save();
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Sale completed successfully',
                'session_id' => $sessionId,
                'receipt_number' => 'RCPT-' . strtoupper(substr($sessionId, 0, 8))
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete sale: ' . $e->getMessage()
            ], 500);
        }
    }

    public function get_saved_carts()
    {
        try {
            // Get manager information
            $manager = Auth::user();
            $businessName = $manager->business_name;

            // Admin view - show all saved carts from all staff members filtered by business_name
            $savedCarts = CartItem::where('status', 'saved')
                ->where('business_name', $businessName)
                ->select('session_id', 'cart_name', 'customer_name', 'customer_id', 'created_at', 'manager_name as user_name')
                ->selectRaw('SUM(total) as total')
                ->selectRaw('COUNT(*) as items_count')
                ->groupBy('session_id', 'cart_name', 'customer_name', 'customer_id', 'created_at', 'manager_name')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'carts' => $savedCarts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch saved carts: ' . $e->getMessage()
            ], 500);
        }
    }

    public function load_saved_cart($sessionId)
    {
        try {
            $manager = Auth::user();
            $businessName = $manager->business_name;

            $cartItems = CartItem::where('session_id', $sessionId)
                ->where('status', 'saved')
                ->where('business_name', $businessName)
                ->get();

            if ($cartItems->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart not found'
                ], 404);
            }

            // Get cart summary
            $firstItem = $cartItems->first();
            $total = $cartItems->sum('total');
            $userName = 'Unknown User';
            if ($firstItem->user) {
                $userName = trim(($firstItem->user->firstname ?? '') . ' ' . ($firstItem->user->othername ?? '') . ' ' . ($firstItem->user->surname ?? '')) ?: 'Unknown User';
            }

            return response()->json([
                'success' => true,
                'cart' => [
                    'cart_name' => $firstItem->cart_name,
                    'customer_id' => $firstItem->customer_id,
                    'customer_name' => $firstItem->customer_name,
                    'user_name' => $userName,
                    'total' => $total,
                    'created_at' => $firstItem->created_at,
                    'items' => $cartItems
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load cart: ' . $e->getMessage()
            ], 500);
        }
    }

    public function delete_saved_cart($sessionId)
    {
        try {
            $manager = Auth::user();
            $businessName = $manager->business_name;

            CartItem::where('session_id', $sessionId)
                ->where('status', 'saved')
                ->where('business_name', $businessName)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Cart deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete cart: ' . $e->getMessage()
            ], 500);
        }
    }


    public function view_saved_carts()
    {
        // Get manager information
        $manager = Auth::user();
        $businessName = $manager->business_name;

        // Admin view - show all saved carts from all staff members filtered by business_name
        $savedCarts = CartItem::where('status', 'saved')
            ->where('business_name', $businessName)
            ->select('session_id', 'cart_name', 'customer_name', 'customer_id', 'created_at', 'user_id', 'manager_name as user_name')
            ->selectRaw('SUM(total) as total')
            ->selectRaw('COUNT(*) as items_count')
            ->groupBy('session_id', 'cart_name', 'customer_name', 'customer_id', 'created_at', 'user_id', 'manager_name')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('manager.sales.saved_carts', compact('savedCarts'));
    }

    public function get_all_staff()
    {
        try {
            $manager = Auth::user();
            $businessName = $manager->business_name;

            $staff = \App\Models\Staffs::select('staffsid', 'fullname', 'email', 'role')
                ->where('business_name', $businessName)
                ->where('status', 'active')
                ->orderBy('fullname')
                ->get();

            return response()->json([
                'success' => true,
                'staff' => $staff
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch staff: ' . $e->getMessage()
            ], 500);
        }
    }




}

