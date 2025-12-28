<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CartItem;

class StaffSalesController extends Controller
{
      public function staff_sales()
    {
        $salesbystaff = CartItem::where('status', 'completed')
            ->select('staff_id', 'user_id')
            ->selectRaw('SUM(total) as total_sales')
            ->selectRaw('COUNT(DISTINCT receipt_number) as transactions_count')
            ->selectRaw('SUM(quantity) as items_sold')
                ->selectRaw('MAX(created_at) as last_transaction_date')
            ->groupBy('staff_id', 'user_id')
            ->orderBy('total_sales', 'desc')
            ->with(['user', 'staff'])
            ->paginate(15);


            $totals = CartItem::where('status', 'completed')
            ->selectRaw('SUM(total) as total_sales')
            ->selectRaw('COUNT(DISTINCT receipt_number) as transactions_count')
            ->selectRaw('SUM(quantity) as items_sold')
            ->first();

        return view('manager.reports.sales_by_staff', ['salesbystaff' => $salesbystaff], compact('totals'));
    }
}
