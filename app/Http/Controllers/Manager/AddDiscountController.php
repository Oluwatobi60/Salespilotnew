<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AddDiscount;

class AddDiscountController extends Controller
{
       public function discount_report()
    {
        // This method will return the view for the discount report
        $discounnts = AddDiscount::all(); // Fetch all discounts
        return view('manager.reports.discount_report', compact('discounnts'));
    }


    public function create_discount(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'discount_name' => 'required|string|max:255',
            'discount_rate' => 'required|numeric|min:0',
        ]);

        // Create a new discount record in the database
        $discount = new AddDiscount();
        $discount->discount_name = $validatedData['discount_name'];
        $discount->discount_rate = $validatedData['discount_rate'];
        $discount->time_used = 0; // Initialize time_used to 0
        $discount->save();

        // Redirect to the discount report page with a success message
        return redirect()->route('manager.discount_report')->with('success', 'Discount created successfully!');
    }


    // Return all discounts as JSON for AJAX requests.

    public function get_discounts()
    {
        $discounts = AddDiscount::all(['id', 'discount_name', 'discount_rate', 'time_used']);
        return response()->json([
            'success' => true,
            'discounts' => $discounts
        ]);
    }
}
