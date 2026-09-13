<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;

class TaxController extends Controller
{
    public function taxes()
    {
        return view('manager.reports.taxes');
    }
}
