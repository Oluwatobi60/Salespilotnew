<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AddDiscount;

class StaffAddDiscountController extends Controller
{
      public function get_discounts()
    {
        $discounts = AddDiscount::all(['id', 'discount_name', 'discount_rate', 'time_used']);
        return response()->json([
            'success' => true,
            'discounts' => $discounts
        ]);
    }
}
