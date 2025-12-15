<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\StandardItem;
use App\Models\Category;
use Illuminate\Http\Request;

class ManagerMainController extends Controller
{
    public function index()
    {
        return view('manager');
    }


    public function add_item_standard()
    {
        $suppliers = Supplier::all();
        $categories = Category::all();
        return view('manager.standardItems.add_item_standard', compact('suppliers', 'categories'));
    }

    public function add_item_variant()
    {
        $suppliers = Supplier::all();
        $units = Unit::all();
        $categories = Category::all();
        return view('manager.variantItems.add_item_variant', compact('suppliers', 'units', 'categories'));
    }

    public function add_item_bundle()
    {
        $suppliers = Supplier::all();
        $units = Unit::all();
        $standardItems = StandardItem::where('current_stock', '>', 0)->get();
        $variantItems = ProductVariant::where('stock_quantity', '>', 0)->get();

        return view('manager.bundleItems.add_item_bundle', compact('suppliers', 'units', 'standardItems', 'variantItems'));
    }




}
