<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use App\Models\Staffs;
use Illuminate\Support\Facades\Auth;

class ActivityLogsController extends Controller
{
    public function activity_logs(\Illuminate\Http\Request $request)
    {
        $currentUser = Auth::user();
        $businessName = $currentUser->business_name;

        // Get filter inputs
        $search = $request->input('search');
        $accessType = $request->input('access_type');
        $staffIdParam = $request->input('staff_id');
        $dateRange = $request->input('date_range');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Get users and staff for dropdowns
        $businessUsers = User::where('business_name', $businessName)->get();
        $businessStaffs = Staffs::where('business_name', $businessName)->get();

        // Query builder for all logs
        $query = ActivityLog::with(['user', 'staff'])
            ->where('business_name', $businessName);

        // Access Type Filter
        if ($accessType === 'Manager') {
            $query->whereNotNull('user_id');
        } elseif ($accessType === 'Staff') {
            $query->whereNotNull('staff_id');
        }

        // Staff Filter
        if ($staffIdParam) {
            if (str_starts_with($staffIdParam, 'user_')) {
                $query->where('user_id', str_replace('user_', '', $staffIdParam));
            } elseif (str_starts_with($staffIdParam, 'staff_')) {
                $query->where('staff_id', str_replace('staff_', '', $staffIdParam));
            }
        }

        // Date Filter
        if ($dateRange) {
            $now = \Carbon\Carbon::now();
            switch ($dateRange) {
                case 'today':
                    $query->whereDate('created_at', $now->toDateString());
                    break;
                case 'yesterday':
                    $query->whereDate('created_at', $now->subDay()->toDateString());
                    break;
                case 'last7days':
                    $query->where('created_at', '>=', $now->subDays(7));
                    break;
                case 'last30days':
                    $query->where('created_at', '>=', $now->subDays(30));
                    break;
                case 'custom':
                    if ($startDate) {
                        $query->whereDate('created_at', '>=', $startDate);
                    }
                    if ($endDate) {
                        $query->whereDate('created_at', '<=', $endDate);
                    }
                    break;
            }
        }

        // Search Filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                  ->orWhere('details', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('surname', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('staff', function ($sq) use ($search) {
                      $sq->where('fullname', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Order and paginate
        $logs = $query->orderByDesc('created_at')->paginate(10)->withQueryString();

        return view('manager.reports.activity_logs', compact('logs', 'businessUsers', 'businessStaffs'));
    }
}
