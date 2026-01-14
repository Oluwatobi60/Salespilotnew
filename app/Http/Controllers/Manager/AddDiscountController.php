<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AddDiscount;
use App\Models\CartItem;
use Carbon\Carbon;

class AddDiscountController extends Controller
{
       public function discount_report(Request $request)
    {
        // Get all discounts
        $discounnts = AddDiscount::all();

        // Build query for CartItems with date filter
        $query = CartItem::query();

        // Apply date range filter
        if ($request->filled('date_range')) {
            $dateRange = $request->date_range;
            $startDate = null;
            $endDate = null;

            switch ($dateRange) {
                case 'today':
                    $startDate = Carbon::today();
                    $endDate = Carbon::today()->endOfDay();
                    break;
                case 'yesterday':
                    $startDate = Carbon::yesterday();
                    $endDate = Carbon::yesterday()->endOfDay();
                    break;
                case 'last7':
                    $startDate = Carbon::today()->subDays(6);
                    $endDate = Carbon::today()->endOfDay();
                    break;
                case 'last30':
                    $startDate = Carbon::today()->subDays(29);
                    $endDate = Carbon::today()->endOfDay();
                    break;
                case 'thisMonth':
                    $startDate = Carbon::now()->startOfMonth();
                    $endDate = Carbon::now()->endOfMonth();
                    break;
                case 'lastMonth':
                    $startDate = Carbon::now()->subMonth()->startOfMonth();
                    $endDate = Carbon::now()->subMonth()->endOfMonth();
                    break;
                case 'custom':
                    if ($request->filled('start_date')) {
                        $startDate = Carbon::parse($request->start_date)->startOfDay();
                    }
                    if ($request->filled('end_date')) {
                        $endDate = Carbon::parse($request->end_date)->endOfDay();
                    }
                    break;
            }

            if ($startDate) {
                $query->where('created_at', '>=', $startDate);
            }
            if ($endDate) {
                $query->where('created_at', '<=', $endDate);
            }
        }

        // Apply staff filter
        if ($request->filled('staff_id')) {
            $staffId = $request->staff_id;
            $query->where(function($q) use ($staffId) {
                $q->where('staff_id', $staffId)
                  ->orWhere('user_id', $staffId);
            });
        }

        // For each discount, calculate total times used and total amount discounted
        $discountStats = $discounnts->map(function($discount) use ($query) {
            // Clone the query for each discount
            $discountQuery = clone $query;
            $timesUsed = $discountQuery->where('discount_id', $discount->id)->count();

            $discountQuery2 = clone $query;
            $amountDiscounted = $discountQuery2->where('discount_id', $discount->id)->sum('discount');

            return [
                'discount_name' => $discount->discount_name,
                'type' => $discount->type,
                'customers_group' => $discount->customers_group,
                'discount_rate' => $discount->discount_rate,
                'times_used' => $timesUsed,
                'amount_discounted' => $amountDiscounted
            ];
        });

        return view('manager.reports.discount_report', [
            'discountStats' => $discountStats
        ]);
    }


   public function add_discount()
    {
         $discounnts = AddDiscount::all(); // Fetch all discounts
        return view('manager.customer.add_discount', compact('discounnts'));
    }


    public function create_discount(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'discount_name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'customers_group' => 'required|string|max:255',
            'discount_rate' => 'required|numeric|min:0',
        ]);

        // Create a new discount record in the database
        $discount = new AddDiscount();
        $discount->discount_name = $validatedData['discount_name'];
        $discount->type = $validatedData['type'];
        $discount->customers_group = $validatedData['customers_group'];
        $discount->discount_rate = $validatedData['discount_rate'];
        $discount->time_used = 0; // Initialize time_used to 0
        $discount->save();

        // Redirect to the discount report page with a success message
        return redirect()->route('manager.add_discount')->with('success', 'Discount created successfully!');
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
