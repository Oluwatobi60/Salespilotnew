<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\BranchInventory;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\ProductVariant;
use App\Models\Sale;
use App\Models\Staffs;
use App\Models\StandardItem;
use App\Models\User;
use App\Models\VariantItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SellProductController extends Controller
{
    public function sell_product()
    {
        // Get manager information
        $manager = Auth::user();
        $businessId = $manager->getBusinessId();

        // Check if manager is managing a branch (delegated manager)
        $managedBranch = $manager->managedBranch;

        if ($managedBranch) {
            // Delegated manager - show items allocated to their branch + items they added themselves
            $branchInventory = BranchInventory::where('branch_id', $managedBranch->id)
                ->where('current_quantity', '>', 0)
                ->get();

            // Get standard item IDs allocated to this branch
            $standardItemIds = $branchInventory->where('item_type', 'standard')->pluck('item_id');
            $variantItemIds = $branchInventory->where('item_type', 'variant')->pluck('item_id');

            // Fetch allocated StandardItems + items added by this manager
            $standard_items = StandardItem::with([
                'supplier',
                'unit',
                'pricingTiers',
            ])
                ->where('enable_sale', true)
                ->where('business_id', $businessId)
                ->where(function ($query) use ($standardItemIds, $manager) {
                    $query->whereIn('id', $standardItemIds)
                        ->orWhere('manager_email', $manager->email);
                })
                ->get();

            // Replace stock with branch inventory stock for allocated items
            foreach ($standard_items as $item) {
                $branchStock = $branchInventory->where('item_type', 'standard')
                    ->where('item_id', $item->id)
                    ->first();
                if ($branchStock) {
                    $item->current_stock = $branchStock->current_quantity;
                    $item->branch_inventory_id = $branchStock->id;
                }
                // If item was added by manager but not in branch_inventory yet, keep original stock
                // This shouldn't happen anymore since we auto-create branch_inventory, but kept as fallback
            }

            // Get variant item IDs for items added by this manager
            $managerVariantItemIds = VariantItem::where('business_id', $businessId)
                ->where('manager_email', $manager->email)
                ->pluck('id');

            // Fetch parent VariantItems that have allocated variants + items added by this manager
            $variant_items = VariantItem::with([
                'supplier',
                'unit',
                'variants' => function ($query) use ($variantItemIds, $managerVariantItemIds) {
                    $query->where('sell_item', true)
                        ->where(function ($q) use ($variantItemIds, $managerVariantItemIds) {
                            // Include variants that are in branch inventory OR whose parent was added by manager
                            $q->whereIn('id', $variantItemIds)
                                ->orWhereIn('variant_item_id', $managerVariantItemIds);
                        })
                        ->with('pricingTiers');
                },
            ])
                ->where('business_id', $businessId)
                ->where(function ($query) use ($variantItemIds, $managerVariantItemIds) {
                    $query->whereHas('variants', function ($q) use ($variantItemIds) {
                        $q->whereIn('id', $variantItemIds);
                    })
                        ->orWhereIn('id', $managerVariantItemIds);
                })
                ->get();

            // Replace variant stock with branch inventory stock for allocated items
            foreach ($variant_items as $variantItem) {
                foreach ($variantItem->variants as $variant) {
                    $branchStock = $branchInventory->where('item_type', 'variant')
                        ->where('item_id', $variant->id)
                        ->first();
                    if ($branchStock) {
                        $variant->current_stock = $branchStock->current_quantity;
                        $variant->branch_inventory_id = $branchStock->id;
                    }
                    // If variant was added by manager but not in branch_inventory yet, keep original stock
                    // This shouldn't happen anymore since we auto-create branch_inventory, but kept as fallback
                }
            }
        } else {
            // Business creator - show all items
            $standard_items = StandardItem::with([
                'supplier',
                'unit',
                'pricingTiers',
            ])
                ->where('enable_sale', true)
                ->where('business_id', $businessId)
                ->get();

            $variant_items = VariantItem::with([
                'supplier',
                'unit',
                'variants' => function ($query) {
                    $query->where('sell_item', true)->with('pricingTiers');
                },
            ])
                ->where('business_id', $businessId)
                ->get();
        }

        // Get all unique categories filtered by business_name
        $categories = Category::where('business_id', $businessId)->orderBy('category_name')->get();

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

        // Pass the entire collection without pagination
        $all_items = $all_items;

        return view('manager.sell.sell_product', compact('all_items', 'standard_items', 'variant_items', 'categories', 'manager'));
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
                'note' => 'nullable|string',
            ]);

            $sessionId = Str::uuid();

            // Get manager information
            $manager = Auth::user();
            $managerName = trim(($manager->firstname ?? '').' '.($manager->othername ?? '').' '.($manager->surname ?? ''));

            // Get user's branch information (either managed branch or assigned branch)
            $managedBranch = $manager->managedBranch;
            $branchId = $managedBranch ? $managedBranch->id : null;
            $branchName = $managedBranch ? $managedBranch->branch_name : null;

            foreach ($validated['items'] as $item) {
                $itemType = isset($item['type']) ? $item['type'] : 'standard';
                $itemCode = isset($item['code']) ? $item['code'] : null;
                CartItem::create([
                    'business_name' => $manager->business_name,
                    'business_id'   => $manager->getBusinessId(),
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
                    'user_id' => Auth::id(),
                    'branch_id' => $branchId,
                    'branch_name' => $branchName,
                ]);
                \App\Helpers\ActivityLogger::log('add_to_cart', 'Manager added item to cart: '.($item['name'] ?? ''));
            }

            return response()->json([
                'success' => true,
                'message' => 'Cart saved successfully',
                'session_id' => $sessionId,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save cart: '.$e->getMessage(),
            ], 500);
        }
    }

    public function checkout(Request $request)
    {
        // Process the checkout and create CartItem entries and Sale header with atomic transaction
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
                'items.*.quantity' => 'required|numeric|min:0.01',
                'items.*.note' => 'nullable|string',
                'items.*.img' => 'nullable|string',
                'total' => 'required|numeric',
                'discount' => 'nullable|numeric',
                'discount_id' => 'nullable|integer|exists:add_discounts,id',
                'payment_method' => 'nullable|string',
                'notes' => 'nullable|string',
            ]);

            $manager = Auth::user();
            $managerName = trim(($manager->firstname ?? '').' '.($manager->othername ?? '').' '.($manager->surname ?? ''));
            $managedBranch = $manager->managedBranch;
            $branchId = $managedBranch ? $managedBranch->id : null;
            $branchName = $managedBranch ? $managedBranch->branch_name : null;

            $sessionId = Str::uuid()->toString();
            $receiptNumber = 'RCPT-'.strtoupper(substr($sessionId, 0, 8));
            $discount = $validated['discount'] ?? 0;
            $discountId = $validated['discount_id'] ?? null;

            $cartSubtotal = 0;
            foreach ($validated['items'] as $item) {
                $cartSubtotal += $item['price'] * $item['quantity'];
            }

            // Execute in an atomic database transaction
            DB::transaction(function () use ($validated, $manager, $managerName, $branchId, $branchName, $sessionId, $receiptNumber, $discount, $discountId, $cartSubtotal) {
                foreach ($validated['items'] as $item) {
                    $itemType = isset($item['type']) ? $item['type'] : 'standard';
                    $itemCode = isset($item['code']) ? $item['code'] : null;

                    // Proportional discount calculation
                    $itemSubtotal = $item['price'] * $item['quantity'];
                    $itemDiscount = ($cartSubtotal > 0 && $discount > 0) ? ($discount * ($itemSubtotal / $cartSubtotal)) : 0;
                    $itemTotal = $itemSubtotal - $itemDiscount;

                    // 1. Stock deduction with row-level locking
                    if ($branchId) {
                        $branchInventory = BranchInventory::where('branch_id', $branchId)
                            ->where('item_id', $item['id'])
                            ->where('item_type', $itemType)
                            ->lockForUpdate()
                            ->first();

                        if ($branchInventory) {
                            if (! $branchInventory->hasStock($item['quantity'])) {
                                throw new \Exception("Insufficient stock in branch inventory for item: {$item['name']}");
                            }
                            $branchInventory->deductStock($item['quantity']);
                        } else {
                            if ($itemType === 'standard') {
                                $standardItem = StandardItem::where('id', $item['id'])
                                    ->where('business_id', $manager->getBusinessId())
                                    ->lockForUpdate()
                                    ->first();

                                if (! $standardItem || $standardItem->current_stock < $item['quantity']) {
                                    throw new \Exception("Insufficient stock for item: {$item['name']}");
                                }
                                $standardItem->current_stock = max(0, $standardItem->current_stock - $item['quantity']);
                                $standardItem->save();
                            } elseif ($itemType === 'variant') {
                                $productVariant = ProductVariant::where('id', $item['id'])
                                    ->where('business_id', $manager->getBusinessId())
                                    ->lockForUpdate()
                                    ->first();

                                if (! $productVariant || $productVariant->current_stock < $item['quantity']) {
                                    throw new \Exception("Insufficient stock for variant: {$item['name']}");
                                }
                                $productVariant->current_stock = max(0, $productVariant->current_stock - $item['quantity']);
                                $productVariant->save();
                            }
                        }
                    } else {
                        if ($itemType === 'standard') {
                            $standardItem = StandardItem::where('id', $item['id'])
                                ->where('business_id', $manager->getBusinessId())
                                ->lockForUpdate()
                                ->first();

                            if (! $standardItem || $standardItem->current_stock < $item['quantity']) {
                                throw new \Exception("Insufficient stock for item: {$item['name']}");
                            }
                            $standardItem->current_stock = max(0, $standardItem->current_stock - $item['quantity']);
                            $standardItem->save();
                        } elseif ($itemType === 'variant') {
                            $productVariant = ProductVariant::where('id', $item['id'])
                                ->where('business_id', $manager->getBusinessId())
                                ->lockForUpdate()
                                ->first();

                            if (! $productVariant || $productVariant->current_stock < $item['quantity']) {
                                throw new \Exception("Insufficient stock for variant: {$item['name']}");
                            }
                            $productVariant->current_stock = max(0, $productVariant->current_stock - $item['quantity']);
                            $productVariant->save();
                        }
                    }

                    // 2. Create CartItem line item
                    CartItem::create([
                        'business_name' => $manager->business_name,
                    'business_id'   => $manager->getBusinessId(),
                        'manager_name' => $managerName,
                        'manager_email' => $manager->email,
                        'cart_name' => 'Sale - '.now()->format('Y-m-d H:i'),
                        'customer_id' => $validated['customer_id'] ?? null,
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
                        'user_id' => Auth::id(),
                        'branch_id' => $branchId,
                        'branch_name' => $branchName,
                    ]);
                }

                // 3. Create normalized Sale transaction header
                Sale::create([
                    'receipt_number' => $receiptNumber,
                    'session_id' => $sessionId,
                    'business_name' => $manager->business_name,
                    'business_id'   => $manager->getBusinessId(),
                    'user_id' => Auth::id(),
                    'staff_id' => null,
                    'branch_id' => $branchId,
                    'branch_name' => $branchName,
                    'customer_id' => $validated['customer_id'] ?? null,
                    'customer_name' => $validated['customer_name'] ?? 'Walk-in Customer',
                    'subtotal' => $cartSubtotal,
                    'discount_total' => $discount,
                    'tax_total' => 0,
                    'grand_total' => $validated['total'],
                    'payment_method' => $validated['payment_method'] ?? 'cash',
                    'status' => 'completed',
                    'items_count' => count($validated['items']),
                    'notes' => $validated['notes'] ?? null,
                    'completed_at' => now(),
                ]);
            });

            // Log checkout activity for manager
            $details = [
                'customer_id' => $request->input('customer_id'),
                'customer_name' => $request->input('customer_name'),
                'total' => $request->input('total'),
                'discount' => $request->input('discount'),
                'receipt_number' => $receiptNumber,
                'items_count' => count($validated['items']),
            ];
            \App\Helpers\ActivityLogger::log('Checkout completed', json_encode($details));

            return response()->json([
                'success' => true,
                'message' => 'Sale completed successfully',
                'session_id' => $sessionId,
                'receipt_number' => $receiptNumber,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete sale: '.$e->getMessage(),
            ], 500);
        }
    }

    public function get_saved_carts()
    {
        try {
            // Get manager information
            $manager = Auth::user();
            $businessId = $manager->getBusinessId();

            // Admin view - show all saved carts from all staff members filtered by business_name
            $savedCarts = CartItem::where('status', 'saved')
                ->where('business_id', $businessId)
                ->select('session_id', 'cart_name', 'customer_name', 'customer_id', 'created_at', 'manager_name as user_name')
                ->selectRaw('SUM(total) as total')
                ->selectRaw('COUNT(*) as items_count')
                ->groupBy('session_id', 'cart_name', 'customer_name', 'customer_id', 'created_at', 'manager_name')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'carts' => $savedCarts,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch saved carts: '.$e->getMessage(),
            ], 500);
        }
    }

    public function load_saved_cart($sessionId)
    {
        try {
            $manager = Auth::user();
            $businessId = $manager->getBusinessId();

            $cartItems = CartItem::where('session_id', $sessionId)
                ->where('status', 'saved')
                ->where('business_id', $businessId)
                ->get();

            if ($cartItems->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart not found',
                ], 404);
            }

            // Get cart summary
            $firstItem = $cartItems->first();
            $total = $cartItems->sum('total');
            $userName = 'Unknown User';
            if ($firstItem->user) {
                $userName = trim(($firstItem->user->firstname ?? '').' '.($firstItem->user->othername ?? '').' '.($firstItem->user->surname ?? '')) ?: 'Unknown User';
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
                    'items' => $cartItems,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load cart: '.$e->getMessage(),
            ], 500);
        }
    }

    public function delete_saved_cart($sessionId)
    {
        try {
            $manager = Auth::user();
            $businessId = $manager->getBusinessId();

            CartItem::where('session_id', $sessionId)
                ->where('status', 'saved')
                ->where('business_id', $businessId)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Cart deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete cart: '.$e->getMessage(),
            ], 500);
        }
    }

    public function view_saved_carts()
    {
        // Get manager information
        $manager = Auth::user();
        $businessId = $manager->getBusinessId();

        // Get all saved carts from all staff members and managers filtered by business_name
        $savedCarts = CartItem::where('status', 'saved')
            ->where('business_id', $businessId)
            ->select('session_id', 'cart_name', 'customer_name', 'customer_id', 'created_at', 'user_id', 'staff_id')
            ->selectRaw('SUM(total) as total')
            ->selectRaw('COUNT(*) as items_count')
            ->groupBy('session_id', 'cart_name', 'customer_name', 'customer_id', 'created_at', 'user_id', 'staff_id')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Add user names (manager or staff)
        foreach ($savedCarts as $cart) {
            if ($cart->staff_id) {
                $staff = Staffs::find($cart->staff_id);
                $cart->user_name = $staff ? $staff->fullname : 'Unknown Staff';
            } elseif ($cart->user_id) {
                $user = User::find($cart->user_id);
                $cart->user_name = $user ? trim($user->first_name.' '.$user->surname) : 'Unknown User';
            } else {
                $cart->user_name = 'Unknown';
            }
        }

        return view('manager.sales.saved_carts', compact('savedCarts'));
    }

    public function get_all_staff()
    {
        try {
            $manager = Auth::user();
            $businessId = $manager->getBusinessId();

            $query = \App\Models\Staffs::select('staffs.staffsid', 'staffs.fullname', 'staffs.email', 'staffs.role')
                ->where('staffs.business_name', $businessName)
                ->where('staffs.status', 'active');

            if (! $manager->isBusinessCreator()) {
                $managedBranchIds = \App\Models\Branch\Branch::where('manager_id', $manager->id)->pluck('id');
                if ($managedBranchIds->isNotEmpty()) {
                    $query->whereHas('branches', function ($q) use ($managedBranchIds) {
                        $q->whereIn('branches.id', $managedBranchIds);
                    });
                } else {
                    $query->where('staffs.id', '<', 0);
                }
            }

            $staff = $query->orderBy('staffs.fullname')->get();

            return response()->json([
                'success' => true,
                'staff' => $staff,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch staff: '.$e->getMessage(),
            ], 500);
        }
    }
}
