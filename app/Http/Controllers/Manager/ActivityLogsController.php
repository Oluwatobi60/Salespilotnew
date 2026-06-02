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

        // Restrict logs to the current user, their parent manager (if any), and their staff
        $currentUserId = $currentUser->id;

        // Include parent (business creator) if this user was added by someone
        $visibleUserIds = collect([$currentUserId]);
        if (!empty($currentUser->manager_email)) {
            $parentUser = User::where('email', $currentUser->manager_email)->first();
            if ($parentUser) {
                $visibleUserIds->push($parentUser->id);
            }
        }

        // Staff IDs that belong to this manager (staff.manager_email == manager.email)
        $staffIds = Staffs::where('manager_email', $currentUser->email)->pluck('id');

        // Get all non-login activities by visible users or their staff
        $nonLoginLogs = ActivityLog::with(['user', 'staff'])
            ->where('action', '!=', 'login')
            ->where(function ($q) use ($visibleUserIds, $staffIds) {
                $q->whereIn('user_id', $visibleUserIds->toArray());
                if ($staffIds->count() > 0) {
                    $q->orWhereIn('staff_id', $staffIds);
                }
            })
            ->orderByDesc('created_at');

        // Get latest login for visible users
        $latestUserLogins = ActivityLog::with(['user'])
            ->where('action', 'login')
            ->whereIn('user_id', $visibleUserIds->toArray())
            ->orderByDesc('created_at')
            ->get()
            ->unique('user_id');

        // Get latest login per staff for this manager
        $latestStaffLogins = collect();
        if ($staffIds->count() > 0) {
            $latestStaffLogins = ActivityLog::with(['staff'])
                ->where('action', 'login')
                ->whereIn('staff_id', $staffIds)
                ->orderByDesc('created_at')
                ->get()
                ->unique('staff_id');
        }

        // Merge all logs and sort
        $mergedLogs = $nonLoginLogs->get()->merge($latestUserLogins)->merge($latestStaffLogins)->sortByDesc('created_at');

        // Paginate using LengthAwarePaginator
        $page = request()->input('page', 1);
        $perPage = 20;
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
