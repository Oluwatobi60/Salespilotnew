<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StandardItem;
use App\Models\VariantItem;
use App\Models\Category;
use App\Models\CartItem;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\staffs;
use App\Models\ProductVariant;
use App\Models\AddCustomer;
use App\Models\BranchInventory;
use App\Models\User;
use App\Models\Branch\Branch;

class StaffsMainController extends Controller
{
    public function index()
    {
       $staff = Auth::guard('staff')->user();
        $businessName = $staff->business_name;

        // Get staff's branch
        $staffBranch = $staff->branches->first();
        $branchId = $staffBranch ? $staffBranch->id : null;

        $standardBranchItemIds = [];
        $variantBranchItemIds = [];

        // Get item IDs from branch_inventory for staff's branch
        if ($branchId) {
            $branchInventory = BranchInventory::where('branch_id', $branchId)
                ->where('business_name', $businessName)
                ->where('current_quantity', '>', 0)
                ->get();


            foreach ($branchInventory as $inventory) {
                if ($inventory->item_type === 'standard') {
                    $standardBranchItemIds[] = $inventory->item_id;
                } elseif ($inventory->item_type === 'variant') {
                    $variantBranchItemIds[] = $inventory->item_id;
                }
            }
        }

        // Fetch StandardItems - only items in the staff's branch inventory
        $standard_items = StandardItem::with([
            'supplier',
            'pricingTiers'
        ])
        ->where('business_name', $businessName)
        ->where('enable_sale', true);

        // Filter by branch inventory if staff has a branch
        if ($branchId && !empty($standardBranchItemIds)) {
            $standard_items->whereIn('id', $standardBranchItemIds);
        } elseif ($branchId) {
            // If staff has branch but no items, return empty
            $standard_items->whereRaw('1 = 0');
        }

        $standard_items = $standard_items->get();

        // Fetch VariantItems - only items in the staff's branch inventory
        $variant_items = VariantItem::with([
            'supplier',
            'unit',
            'variants' => function($query) use ($variantBranchItemIds, $branchId) {
                $query->where('sell_item', true);
                if ($branchId && !empty($variantBranchItemIds)) {
                    $query->whereIn('id', $variantBranchItemIds);
                } elseif ($branchId) {
                    $query->whereRaw('1 = 0');
                }
                $query->with('pricingTiers');
            }
        ])
        ->where('business_name', $businessName);

        // Filter variant items by branch inventory
        if ($branchId && !empty($variantBranchItemIds)) {
            $variant_items->whereHas('variants', function($q) use ($variantBranchItemIds) {
                $q->whereIn('id', $variantBranchItemIds);
            });
        } elseif ($branchId) {
            // If staff has branch but no items, return empty
            $variant_items->whereRaw('1 = 0');
        }

        $variant_items = $variant_items->get();

        // Replace stock quantities with branch inventory quantities
        if ($branchId) {
            $branchInventory = BranchInventory::where('branch_id', $branchId)
                ->where('business_name', $businessName)
                ->get();

            // Replace standard item stock with branch inventory stock
            foreach ($standard_items as $item) {
                $branchStock = $branchInventory->where('item_type', 'standard')
                    ->where('item_id', $item->id)
                    ->first();
                if ($branchStock) {
                    $item->current_stock = $branchStock->current_quantity;
                    $item->branch_inventory_id = $branchStock->id;
                }
            }

            // Replace variant stock with branch inventory stock
            foreach ($variant_items as $variantItem) {
                foreach ($variantItem->variants as $variant) {
                    $branchStock = $branchInventory->where('item_type', 'variant')
                        ->where('item_id', $variant->id)
                        ->first();
                    if ($branchStock) {
                        $variant->stock_quantity = $branchStock->current_quantity;
                        $variant->branch_inventory_id = $branchStock->id;
                    }
                }
            }
        }

        // Get all unique categories
        $categories = Category::where('business_name', $businessName)
            ->orderBy('category_name')
            ->get();

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


        return view('staff.dashboard', compact('categories', 'all_items', 'standard_items', 'variant_items'));
    }


    public function sell_product()
    {
        $staff = Auth::guard('staff')->user();
        $businessName = $staff->business_name;

        // Get staff's branch
        $staffBranch = $staff->branches->first();
        $branchId = $staffBranch ? $staffBranch->id : null;

        $standardBranchItemIds = [];
        $variantBranchItemIds = [];

        // Get item IDs from branch_inventory for staff's branch
        if ($branchId) {
            $branchInventory = BranchInventory::where('branch_id', $branchId)
                ->where('business_name', $businessName)
                ->where('current_quantity', '>', 0)
                ->get();


            foreach ($branchInventory as $inventory) {
                if ($inventory->item_type === 'standard') {
                    $standardBranchItemIds[] = $inventory->item_id;
                } elseif ($inventory->item_type === 'variant') {
                    $variantBranchItemIds[] = $inventory->item_id;
                }
            }
        }

        // Fetch StandardItems - only items in the staff's branch inventory
        $standard_items = StandardItem::with([
            'supplier',
            'pricingTiers'
        ])
        ->where('business_name', $businessName)
        ->where('enable_sale', true);

        // Filter by branch inventory if staff has a branch
        if ($branchId && !empty($standardBranchItemIds)) {
            $standard_items->whereIn('id', $standardBranchItemIds);
        } elseif ($branchId) {
            // If staff has branch but no items, return empty
            $standard_items->whereRaw('1 = 0');
        }

        $standard_items = $standard_items->get();

        // Fetch VariantItems - only items in the staff's branch inventory
        $variant_items = VariantItem::with([
            'supplier',
            'unit',
            'variants' => function($query) use ($variantBranchItemIds, $branchId) {
                $query->where('sell_item', true);
                if ($branchId && !empty($variantBranchItemIds)) {
                    $query->whereIn('id', $variantBranchItemIds);
                } elseif ($branchId) {
                    $query->whereRaw('1 = 0');
                }
                $query->with('pricingTiers');
            }
        ])
        ->where('business_name', $businessName);

        // Filter variant items by branch inventory
        if ($branchId && !empty($variantBranchItemIds)) {
            $variant_items->whereHas('variants', function($q) use ($variantBranchItemIds) {
                $q->whereIn('id', $variantBranchItemIds);
            });
        } elseif ($branchId) {
            // If staff has branch but no items, return empty
            $variant_items->whereRaw('1 = 0');
        }

        $variant_items = $variant_items->get();

        // Replace stock quantities with branch inventory quantities
        if ($branchId) {
            $branchInventory = BranchInventory::where('branch_id', $branchId)
                ->where('business_name', $businessName)
                ->get();

            // Replace standard item stock with branch inventory stock
            foreach ($standard_items as $item) {
                $branchStock = $branchInventory->where('item_type', 'standard')
                    ->where('item_id', $item->id)
                    ->first();
                if ($branchStock) {
                    $item->current_stock = $branchStock->current_quantity;
                    $item->branch_inventory_id = $branchStock->id;
                }
            }

            // Replace variant stock with branch inventory stock
            foreach ($variant_items as $variantItem) {
                foreach ($variantItem->variants as $variant) {
                    $branchStock = $branchInventory->where('item_type', 'variant')
                        ->where('item_id', $variant->id)
                        ->first();
                    if ($branchStock) {
                        $variant->stock_quantity = $branchStock->current_quantity;
                        $variant->branch_inventory_id = $branchStock->id;
                    }
                }
            }
        }

        // Get all unique categories
        $categories = Category::where('business_name', $businessName)
            ->orderBy('category_name')
            ->get();

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

        return view('staff.sell.sell_product', compact('all_items', 'standard_items', 'variant_items', 'categories'));
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

            $staff = Auth::guard('staff')->user();
            $managerName = trim(($staff->firstname ?? '') . ' ' . ($staff->othername ?? '') . ' ' . ($staff->surname ?? ''));

            // Get staff's branch information
            $branch = $staff->branch;
            $branchId = $branch ? $branch->id : null;
            $branchName = $branch ? $branch->branch_name : null;
            $managerId = $branch ? $branch->manager_id : null;

            foreach ($validated['items'] as $item) {
                $itemType = isset($item['type']) ? $item['type'] : 'standard';
                $itemCode = isset($item['code']) ? $item['code'] : null;
                CartItem::create([
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
                    'staff_id' => Auth::guard('staff')->id(),
                    'business_name' => $staff->business_name,
                    'manager_name' => $managerName,
                    'manager_email' => $staff->email,
                    'branch_id' => $branchId,
                    'branch_name' => $branchName,
                    'user_id' => $managerId
                ]);
                \App\Helpers\ActivityLogger::log('add_to_cart', 'Staff added item to cart: ' . ($item['name'] ?? ''));
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
                'discount' => 'nullable|numeric'
            ]);



            $sessionId = Str::uuid();
            $receiptNumber = 'RCPT-' . strtoupper(substr($sessionId, 0, 8));
            $discount = $validated['discount'] ?? 0;

            $staff = Auth::guard('staff')->user();
            $managerName = trim(($staff->firstname ?? '') . ' ' . ($staff->othername ?? '') . ' ' . ($staff->surname ?? ''));

            // Get staff's assigned branch (use the same logic as sell_product)
            $staffBranch = $staff->branches->first();
            $branchId = $staffBranch ? $staffBranch->id : null;
            $branchName = $staffBranch ? $staffBranch->branch_name : null;
            $managerId = $staffBranch ? $staffBranch->user_id : null;

            // Log checkout activity for staff
            $details = [
                'customer_id' => $request->input('customer_id'),
                'customer_name' => $request->input('customer_name'),
                'total' => $request->input('total'),
                'discount' => $request->input('discount'),
                'items_count' => is_array($request->input('items')) ? count($request->input('items')) : 0,
            ];
            \App\Helpers\ActivityLogger::log('Checkout completed', json_encode($details));

            foreach ($validated['items'] as $item) {
                $itemSubtotal = $item['price'] * $item['quantity'];
                $itemDiscount = ($discount / $validated['total']) * $itemSubtotal;
                $itemTotal = $itemSubtotal - $itemDiscount;

                $itemType = isset($item['type']) ? $item['type'] : 'standard';
                $itemCode = isset($item['code']) ? $item['code'] : null;
                CartItem::create([
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
                    'total' => $itemTotal,
                    'status' => 'completed',
                    'session_id' => $sessionId,
                    'receipt_number' => $receiptNumber,
                    'staff_id' => Auth::guard('staff')->id(),
                    'business_name' => $staff->business_name,
                    'manager_name' => $managerName,
                    'manager_email' => $staff->email,
                    'branch_id' => $branchId,
                    'branch_name' => $branchName,
                    'user_id' => $managerId
                ]);

                // Update branch inventory if applicable
                if ($branchId) {
                    $branchInventory = BranchInventory::where('branch_id', $branchId)
                        ->where('item_id', $item['id'])
                        ->where('item_type', $itemType)
                        ->first();

                    if ($branchInventory) {
                        // Item is in branch inventory - use branch stock
                        if ($branchInventory->current_quantity < $item['quantity']) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Insufficient stock in branch inventory for item: ' . $item['name']
                            ], 400);
                        }

                        // Deduct from branch inventory
                        $branchInventory->deductStock($item['quantity']);
                    } else {
                        // Item not in branch inventory - check if it was added by the branch manager
                        // If so, deduct from main inventory instead
                        $manager = $managerId ? User::find($managerId) : null;

                        if ($itemType === 'standard') {
                            $standardItem = null;
                            if ($manager) {
                                $standardItem = StandardItem::where('id', $item['id'])
                                    ->where('business_name', $staff->business_name)
                                    ->where('manager_email', $manager->email)
                                    ->first();
                            }

                            if (!$standardItem) {
                                return response()->json([
                                    'success' => false,
                                    'message' => 'Item not found in branch inventory: ' . $item['name']
                                ], 400);
                            }

                            if ($standardItem->current_stock < $item['quantity']) {
                                return response()->json([
                                    'success' => false,
                                    'message' => 'Insufficient stock for item: ' . $item['name']
                                ], 400);
                            }

                            $standardItem->current_stock -= $item['quantity'];
                            if ($standardItem->current_stock < 0) {
                                $standardItem->current_stock = 0;
                            }
                            $standardItem->save();

                        } elseif ($itemType === 'variant') {
                            $productVariant = null;
                            if ($manager) {
                                $productVariant = ProductVariant::whereHas('variantItem', function($query) use ($manager, $staff) {
                                    $query->where('business_name', $staff->business_name)
                                          ->where('manager_email', $manager->email);
                                })->find($item['id']);
                            }

                            if (!$productVariant) {
                                return response()->json([
                                    'success' => false,
                                    'message' => 'Variant not found in branch inventory: ' . $item['name']
                                ], 400);
                            }

                            if ($productVariant->stock_quantity < $item['quantity']) {
                                return response()->json([
                                    'success' => false,
                                    'message' => 'Insufficient stock for variant: ' . $item['name']
                                ], 400);
                            }

                            $productVariant->stock_quantity -= $item['quantity'];
                            if ($productVariant->stock_quantity < 0) {
                                $productVariant->stock_quantity = 0;
                            }
                            $productVariant->save();
                        }
                    }
                } else {
                    // No branch assigned - update main inventory
                    if ($itemType === 'standard') {
                        $standardItem = StandardItem::where('id', $item['id'])
                            ->where('business_name', $staff->business_name)
                            ->first();
                        if ($standardItem) {
                            $standardItem->current_stock -= $item['quantity'];
                            if ($standardItem->current_stock < 0) {
                                $standardItem->current_stock = 0;
                            }
                            $standardItem->save();
                        }
                    } elseif ($itemType === 'variant') {
                        $productVariant = ProductVariant::whereHas('variantItem', function($query) use ($staff) {
                            $query->where('business_name', $staff->business_name);
                        })->find($item['id']);
                        if ($productVariant) {
                            $productVariant->stock_quantity -= $item['quantity'];
                            if ($productVariant->stock_quantity < 0) {
                                $productVariant->stock_quantity = 0;
                            }
                            $productVariant->save();
                        }
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
            $staff = Auth::guard('staff')->user();
            $businessName = $staff->business_name;

            // Show all saved carts from staff members in same business
            $savedCarts = CartItem::where('status', 'saved')
                ->where('cart_items.business_name', $businessName)
                ->join('staffs', 'cart_items.staff_id', '=', 'staffs.id')
                ->select('cart_items.session_id', 'cart_items.cart_name', 'cart_items.customer_name', 'cart_items.customer_id', 'cart_items.created_at', 'staffs.fullname as user_name')
                ->selectRaw('SUM(cart_items.total) as total')
                ->selectRaw('COUNT(*) as items_count')
                ->groupBy('cart_items.session_id', 'cart_items.cart_name', 'cart_items.customer_name', 'cart_items.customer_id', 'cart_items.created_at', 'staffs.fullname')
                ->orderBy('cart_items.created_at', 'desc')
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
            $staff = Auth::guard('staff')->user();
            $businessName = $staff->business_name;

            $cartItems = CartItem::where('session_id', $sessionId)
                ->where('status', 'saved')
                ->where('business_name', $businessName)
                ->where('staff_id', Auth::guard('staff')->id())
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
            // Get staff name from staffs table
            $staff = staffs::find($firstItem->staff_id);
            $userName = $staff ? $staff->fullname : 'Unknown Staff';

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
            $staff = Auth::guard('staff')->user();
            $businessName = $staff->business_name;

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
        $staff = Auth::guard('staff')->user();
        $businessName = $staff->business_name;

        // Show all saved carts from staff members in same business
        $savedCarts = CartItem::where('cart_items.status', 'saved')
            ->where('cart_items.business_name', $businessName)
            ->join('staffs', 'cart_items.staff_id', '=', 'staffs.id')
            ->select('cart_items.session_id', 'cart_items.cart_name', 'cart_items.customer_name', 'cart_items.customer_id', 'cart_items.created_at', 'cart_items.staff_id', 'staffs.fullname as user_name')
            ->selectRaw('SUM(cart_items.total) as total')
            ->selectRaw('COUNT(*) as items_count')
            ->groupBy('cart_items.session_id', 'cart_items.cart_name', 'cart_items.customer_name', 'cart_items.customer_id', 'cart_items.created_at', 'cart_items.staff_id', 'staffs.fullname')
            ->orderBy('cart_items.created_at', 'desc')
            ->paginate(15);

        return view('staff.sales.saved_carts', compact('savedCarts'));
    }


     public function completed_sales()
    {
        $staff = Auth::guard('staff')->user();
        $businessName = $staff->business_name;

        $completedSales = CartItem::where('status', 'completed')
            ->where('business_name', $businessName)
            ->where('staff_id', Auth::guard('staff')->id())
            ->select('receipt_number', 'customer_name', 'customer_id', 'created_at', 'staff_id')
            ->selectRaw('SUM(total) as total')
            ->selectRaw('SUM(discount) as discount')
            ->selectRaw('COUNT(*) as items_count')
            ->groupBy('receipt_number', 'customer_name', 'customer_id', 'created_at', 'staff_id')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('staff.sales.completed_sales', compact('completedSales'));
    }

    // Return sale items for a given receipt number (for completed sales AJAX)
    public function get_sale_items($receiptNumber)
    {
        $staff = Auth::guard('staff')->user();
        $businessName = $staff->business_name;

        $items = CartItem::where('receipt_number', $receiptNumber)
            ->where('status', 'completed')
            ->where('business_name', $businessName)
            ->where('staff_id', Auth::guard('staff')->id())
            ->get();

        if ($items->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No items found for this sale.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'items' => $items
        ]);
    }




  public function customers() {
        $staff = Auth::guard('staff')->user();
        $businessName = $staff->business_name;

        $customers = AddCustomer::where('business_name', $businessName)
            ->where('staff_id', $staff->id)
            ->latest()
            ->paginate(4);
        return view('staff.customer.customerinfo', compact('customers'));
    }

    public function get_all_customers() {
        $staff = Auth::guard('staff')->user();
        $businessName = $staff->business_name;

        $customers = AddCustomer::where('business_name', $businessName)
                                ->where('staff_id', $staff->id)
                                ->select('id', 'customer_name', 'email', 'phone_number')
                                ->orderBy('customer_name', 'asc')
                                ->get();

        return response()->json([
            'success' => true,
            'customers' => $customers
        ]);
    }

     public function add_customer(Request $request)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'customer_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:add_customers,email',
            'phone_number' => 'nullable|string|max:20|unique:add_customers,phone_number',
            'address' => 'nullable|string|max:500',
        ]);

        // Add the staff_id of the logged-in staff
        $staff = Auth::guard('staff')->user();
        $validatedData['staff_id'] = Auth::guard('staff')->id();
        $validatedData['business_name'] = $staff->business_name;
        $validatedData['manager_name'] = trim(($staff->firstname ?? '') . ' ' . ($staff->othername ?? '') . ' ' . ($staff->surname ?? ''));
        $validatedData['manager_email'] = $staff->email;

        // Create new customer
        $customer = AddCustomer::create($validatedData);

        // Check if request expects JSON (AJAX request)
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Customer added successfully',
                'customer' => $customer,
            ]);
        }

        // Regular form submission - redirect with flash message
        return redirect()->back()->with('success', 'Customer added successfully!');
    }

    public function edit_customer($id)
    {
        $staff = Auth::guard('staff')->user();
        $businessName = $staff->business_name;

        $customer = AddCustomer::where('business_name', $businessName)
            ->findOrFail($id);
        return view('staff.customer.edit_customer', compact('customer'));
    }

    public function update_customer(Request $request, $id)
    {
        $staff = Auth::guard('staff')->user();
        $businessName = $staff->business_name;

        $customer = AddCustomer::where('business_name', $businessName)
            ->where('staff_id', $staff->id)
            ->findOrFail($id);

        // Validate incoming request data
        $validatedData = $request->validate([
            'customer_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:add_customers,email,' . $id,
            'phone_number' => 'nullable|string|max:20|unique:add_customers,phone_number,' . $id,
            'address' => 'nullable|string|max:500',
        ]);

        // Update customer
        $customer->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Customer updated successfully',
            'customer' => $customer,
        ]);
    }

    public function delete_customer($id)
    {
        $staff = Auth::guard('staff')->user();
        $businessName = $staff->business_name;

        $customer = AddCustomer::where('business_name', $businessName)
            ->where('staff_id', $staff->id)
            ->findOrFail($id);

        $customer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Customer deleted successfully',
        ]);
    }

    public function get_customer_details($id)
    {
        $staff = Auth::guard('staff')->user();
        $businessName = $staff->business_name;

        $customer = AddCustomer::with(['user', 'staff'])
            ->where('business_name', $businessName)
            ->where('staff_id', $staff->id)
            ->findOrFail($id);

        // Get added by name
        $addedBy = '-';
        if ($customer->user) {
            $addedBy = $customer->user->name;
        } elseif ($customer->staff) {
            $addedBy = $customer->staff->fullname ?? ($customer->staff->firstname . ' ' . $customer->staff->surname);
        }

        // Get order statistics - check both customer_id and customer_name
        $orders = CartItem::where(function($query) use ($id, $customer) {
                            $query->where('customer_id', $id)
                                  ->orWhere('customer_name', $customer->customer_name);
                         })
                         ->where('status', 'completed')
                         ->select('receipt_number',
                                 DB::raw('SUM(total) as order_total'),
                                 DB::raw('MIN(created_at) as order_date'))
                         ->groupBy('receipt_number')
                         ->orderBy('order_date', 'desc')
                         ->get();

        $totalOrders = $orders->count();
        $totalSpent = $orders->sum('order_total');
        $lastPurchaseDate = $orders->first() ? $orders->first()->order_date : null;

        // Get order details with items
        $orderDetails = [];
        foreach ($orders->take(10) as $order) { // Limit to last 10 orders
            $items = CartItem::where(function($query) use ($id, $customer) {
                                $query->where('customer_id', $id)
                                      ->orWhere('customer_name', $customer->customer_name);
                             })
                           ->where('receipt_number', $order->receipt_number)
                           ->where('status', 'completed')
                           ->get();

            $orderDetails[] = [
                'receipt_number' => $order->receipt_number,
                'date' => date('M d, Y', strtotime($order->order_date)),
                'items_count' => $items->count(),
                'total' => number_format((float)$order->order_total, 2),
                'items' => $items->map(function($item) {
                    return [
                        'name' => $item->item_name,
                        'quantity' => $item->quantity,
                        'price' => number_format((float)$item->item_price, 2),
                        'subtotal' => number_format((float)$item->subtotal, 2),
                    ];
                })
            ];
        }

        return response()->json([
            'success' => true,
            'customer' => [
                'id' => $customer->id,
                'name' => $customer->customer_name,
                'email' => $customer->email ?? '-',
                'phone' => $customer->phone_number ?? '-',
                'address' => $customer->address ?? '-',
                'registrationDate' => $customer->created_at->format('M d, Y'),
                'addedBy' => $addedBy,
                'lastUpdated' => $customer->updated_at->format('M d, Y'),
                'status' => 'Active',
                'totalOrders' => $totalOrders,
                'totalSpent' => '₦' . number_format($totalSpent, 2),
                'lastPurchase' => $lastPurchaseDate ? date('M d, Y', strtotime($lastPurchaseDate)) : 'Never',
                'orders' => $orderDetails
            ]
        ]);
    }

 public function print_receipt($receiptNumber)
    {
        $staff = Auth::guard('staff')->user();
        $businessName = $staff->business_name;

        // Get all items for this receipt
        $items = CartItem::where('receipt_number', $receiptNumber)
            ->where('status', 'completed')
            ->where('business_name', $businessName)
            ->get();

        if ($items->isEmpty()) {
            abort(404, 'Sale not found');
        }

        // Get sale summary (customer, date, total, discount, etc.)
        $sale = $items->first();
        $total = $items->sum('total');
        $discount = $items->sum('discount');
        $subtotal = $items->sum('subtotal');

        return view('staff.sales.print_receipt', [
            'items' => $items,
            'sale' => $sale,
            'total' => $total,
            'discount' => $discount,
            'subtotal' => $subtotal,
        ]);
    }

}
