<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AddDiscount;
use App\Models\CartItem;
use App\Models\Welcome\SignupRequest;
use App\Models\UserSubscription;
use App\Models\User;
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
         // Get logged-in manager's business name
         $manager = Auth::user();
         $businessName = $manager->business_name;

         // Fetch discounts only for this manager's business
         $discounnts = AddDiscount::where('business_name', $businessName)->get();

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

        // Get manager info from logged-in user
        $manager = Auth::user();
        $managerFullName = trim(($manager->firstname ?? '') . ' ' . ($manager->othername ?? '') . ' ' . ($manager->surname ?? ''));

        // Create a new discount record in the database
        $discount = new AddDiscount();
        $discount->business_name = $manager->business_name ?? null;
        $discount->manager_name = $managerFullName ?: null;
        $discount->manager_email = $manager->email ?? null;
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
        // Get logged-in manager's business name
        $manager = Auth::user();
        $businessName = $manager->business_name;

        // Fetch discounts only for this manager's business
        $discounts = AddDiscount::where('business_name', $businessName)
            ->get(['id', 'discount_name', 'discount_rate', 'time_used']);

        return response()->json([
            'success' => true,
            'discounts' => $discounts
        ]);
    }

    public function update_discount(Request $request, $id)
    {
        $discount = AddDiscount::findOrFail($id);

        // Validate the request data
        $validatedData = $request->validate([
            'discount_name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'customers_group' => 'required|string|max:255',
            'discount_rate' => 'required|numeric|min:0',
        ]);

        // Update the discount
        $discount->update($validatedData);

        // Check if the request expects JSON (AJAX request)
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Discount updated successfully',
                'discount' => $discount
            ], 200);
        }

        // Redirect back with success message
        return redirect()->route('manager.add_discount')->with('success', 'Discount updated successfully.');
    }

    public function delete_discount($id)
    {
        $discount = AddDiscount::findOrFail($id);
        $discount->delete();

        // Check if the request expects JSON (AJAX request)
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Discount deleted successfully'
            ], 200);
        }

        // Redirect back with success message
        return redirect()->route('manager.add_discount')->with('success', 'Discount deleted successfully.');
    }



}
