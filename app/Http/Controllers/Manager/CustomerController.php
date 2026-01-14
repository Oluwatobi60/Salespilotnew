<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AddCustomer;
use App\Models\CartItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class CustomerController extends Controller
{

    public function customers(Request $request) {
        $query = AddCustomer::with(['user', 'staff']);

        // Apply staff filter
        if ($request->filled('staff_id')) {
            $staffId = $request->staff_id;
            $query->where(function($q) use ($staffId) {
                $q->where('staff_id', $staffId)
                  ->orWhere('user_id', $staffId);
            });
        }

        $customers = $query->latest()->paginate(4);
        return view('manager.customer.customerinfo', compact('customers'));
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
        return view('manager.customer.edit_customer', compact('customer'));
    }

     public function update_customer(Request $request, $id)
    {
        $customer = AddCustomer::findOrFail($id);

        // Validate incoming request data
        $validatedData = $request->validate([
            'customer_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:add_customers,email,' . $customer->id,
            'phone_number' => 'nullable|string|max:20|unique:add_customers,phone_number,' . $customer->id,
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
        $customer = AddCustomer::findOrFail($id);
        $customer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Customer deleted successfully',
        ]);
    }

    public function get_customer_details($id)
    {
        $customer = AddCustomer::with(['user', 'staff'])->findOrFail($id);

        // Get added by name
        $addedBy = '-';
        if ($customer->user) {
            $addedBy = $customer->user->name;
        } elseif ($customer->staff) {
            $addedBy = $customer->staff->fullname;
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

        // Get order details with items - check both customer_id and customer_name
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
}
