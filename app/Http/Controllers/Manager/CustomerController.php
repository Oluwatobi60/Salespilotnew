<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AddCustomer;

class CustomerController extends Controller
{

    public function customers() {
        $customers = AddCustomer::latest()->paginate(4);
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
}
