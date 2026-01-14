<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActivityLog;

class ActivityLogsController extends Controller
{
    public function activity_logs()
    {
        // Get all non-login activities
        $nonLoginLogs = ActivityLog::with(['user', 'staff'])
            ->where('action', '!=', 'login')
            ->orderByDesc('created_at');

        // Get latest login per user
        $latestUserLogins = ActivityLog::with(['user'])
            ->where('action', 'login')
            ->whereNotNull('user_id')
            ->orderByDesc('created_at')
            ->get()
            ->unique('user_id');

        // Get latest login per staff
        $latestStaffLogins = ActivityLog::with(['staff'])
            ->where('action', 'login')
            ->whereNotNull('staff_id')
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
