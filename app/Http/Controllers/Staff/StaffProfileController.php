<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StaffProfileController extends Controller
{
    public function staff_profile()
    {
        return view('staff.profile.profile');
    }
}
