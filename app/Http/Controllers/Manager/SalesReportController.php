<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SalesReportController extends Controller
{
   public function completed_sales()
    {
        return view('manager.sales.completed_sales');
    }

    public function sales_summary()
    {
        return view('manager.reports.sales_summary');
    }

    public function staff_sales()
    {
        return view('manager.reports.sales_by_staff');
    }


    public function sales_by_item()
    {
        return view('manager.reports.sales_by_item');
    }


    public function sales_by_category()
    {
        return view('manager.reports.sale_by_category');
    }

    public function valuation_report()
    {
        return view('manager.reports.inventory_valuation');
    }

    public function taxes()
    {
        return view('manager.reports.taxes');
    }

    public function discount_report()
    {
        return view('manager.reports.discount_report');
    }
}
