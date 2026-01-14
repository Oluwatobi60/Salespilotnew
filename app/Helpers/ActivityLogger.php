<?php
namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    public static function log($action, $details = null)
    {
        $user = Auth::user();
        $staff = Auth::guard('staff')->user();
        $device = request()->header('User-Agent');
        $ip = request()->ip();
        ActivityLog::create([
            'user_id' => $user ? $user->id : null,
            'staff_id' => $staff ? $staff->staff_id : null,
            'action' => $action,
            'device' => $device,
            'ip_address' => $ip,
            'details' => $details,
        ]);
    }
}
