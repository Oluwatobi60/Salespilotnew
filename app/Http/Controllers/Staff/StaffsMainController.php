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
use App\Models\staffs;
use App\Models\ProductVariant;
use App\Models\AddCustomer;

class StaffsMainController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('category_name')->get();

        // Fetch all StandardItems with their associated relationships
        $standard_items = StandardItem::with([
            'supplier',
            'pricingTiers'
        ])->where('enable_sale', true)->get();

        // Fetch all VariantItems with their associated relationships
        $variant_items = VariantItem::with([
            'supplier',
            'unit',
            'variants' => function($query) {
                $query->where('sell_item', true)->with('pricingTiers');
            }
        ])->get();

        // Merge both collections for a unified item list
        $all_items = collect([]);
        foreach ($standard_items as $item) {
            $item->item_type = 'standard';
            $all_items->push($item);
        }
        foreach ($variant_items as $item) {
            $item->item_type = 'variant';
            $all_items->push($item);
        }

        return view('staff.dashboard', compact('categories', 'all_items', 'standard_items', 'variant_items'));
    }


     public function sell_product()
    {
        // Fetch all StandardItems with their associated relationships
        $standard_items = StandardItem::with([
            'supplier',           // Supplier relationship
            'pricingTiers'       // Pricing tiers for bulk/quantity pricing
        ])->where('enable_sale', true)->get();

        // Fetch all VariantItems with their associated relationships
        $variant_items = VariantItem::with([
            'supplier',          // Supplier relationship
            'unit',              // Unit of measurement
            'variants' => function($query) {
                $query->where('sell_item', true)->with('pricingTiers'); // Only sellable variants with their pricing tiers
            }
        ])->get();

        // Get all unique categories
        $categories = Category::orderBy('category_name')->get();

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
                    'staff_id' => Auth::guard('staff')->id()
                ]);
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
                    'staff_id' => Auth::guard('staff')->id()
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
            // Show all saved carts from all staff members (join with staffs table)
            $savedCarts = CartItem::where('status', 'saved')
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
            $cartItems = CartItem::where('session_id', $sessionId)
                ->where('status', 'saved')
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
            CartItem::where('session_id', $sessionId)
                ->where('status', 'saved')
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
        // Show all saved carts from all staff members (join with staffs table)
        $savedCarts = CartItem::where('cart_items.status', 'saved')
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

        $completedSales = CartItem::where('status', 'completed')
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
        $items = CartItem::where('receipt_number', $receiptNumber)
            ->where('status', 'completed')
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
        $customers = AddCustomer::latest()->paginate(4);
        return view('staff.customer.customerinfo', compact('customers'));
    }

    public function get_all_customers() {
        $customers = AddCustomer::select('id', 'customer_name', 'email', 'phone_number')
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

        // Create new customer
        $customer = AddCustomer::create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Customer added successfully',
            'customer' => $customer,
        ]);
    }

    public function edit_customer($id)
    {
        $customer = AddCustomer::findOrFail($id);
        return view('staff.customer.edit_customer', compact('customer'));
    }

 public function print_receipt($receiptNumber)
    {
        // Get all items for this receipt
        $items = CartItem::where('receipt_number', $receiptNumber)
            ->where('status', 'completed')
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
