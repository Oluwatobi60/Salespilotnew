<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ManagerMainController extends Controller
{
    public function index()
    {
        return view('manager.dashboard');
    }

    public function add_staff()
    {
        return view('manager.staff.add_staff');
    }

    public function add_item_standard()
    {
        return view('manager.standardItems.add_item_standard');
    }

    public function add_item_variant()
    {
        return view('manager.variantItems.add_item_variant');
    }

    public function add_item_bundle()
    {
        return view('manager.bundleItems.add_item_bundle');
    }

    
}
