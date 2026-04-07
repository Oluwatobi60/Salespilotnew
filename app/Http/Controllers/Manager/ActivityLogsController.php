<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use App\Models\Staffs;
use Illuminate\Support\Facades\Auth;

class ActivityLogsController extends Controller
{
    public function activity_logs()
    {
        $currentUser = Auth::user();
        $businessName = $currentUser->business_name;

        // Get all user IDs for this business (creator + all addby managers)
        $userIds = User::where('business_name', $businessName)->pluck('id');

        // Get all staff IDs for this business
        $staffIds = Staffs::where('business_name', $businessName)->pluck('id');

        // Get all non-login activities for this business
        $nonLoginLogs = ActivityLog::with(['user', 'staff'])
            ->where('action', '!=', 'login')
            ->where(function ($q) use ($userIds, $staffIds) {
                $q->whereIn('user_id', $userIds)
                  ->orWhereIn('staff_id', $staffIds);
            })
            ->orderByDesc('created_at');

        // Get latest login per user for this business
        $latestUserLogins = ActivityLog::with(['user'])
            ->where('action', 'login')
            ->whereIn('user_id', $userIds)
            ->orderByDesc('created_at')
            ->get()
            ->unique('user_id');

        // Get latest login per staff for this business
        $latestStaffLogins = ActivityLog::with(['staff'])
            ->where('action', 'login')
            ->whereIn('staff_id', $staffIds)
            ->orderByDesc('created_at')
            ->get()
            ->unique('staff_id');

        // Merge all logs and sort
        $mergedLogs = $nonLoginLogs->get()->merge($latestUserLogins)->merge($latestStaffLogins)->sortByDesc('created_at');

        // Paginate using LengthAwarePaginator
        $page = request()->input('page', 1);
        $perPage = 30;
        $total = $mergedLogs->count();
        $currentPageLogs = $mergedLogs->slice(($page - 1) * $perPage, $perPage)->values();
        $logs = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentPageLogs,
            $total,
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('manager.reports.activity_logs', compact('logs'));
    }
}
