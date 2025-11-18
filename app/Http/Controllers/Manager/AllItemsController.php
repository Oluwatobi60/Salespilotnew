<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AllItemsController extends Controller
{
    public function all_items()
    {
        return view('manager.inventory.all_items.all_items');
    }
}
