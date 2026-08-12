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
        
        $businessName = null;
        if ($user) {
            $businessName = $user->business_name;
        } elseif ($staff) {
            $businessName = $staff->business_name;
        }

        ActivityLog::create([
            'user_id' => $user ? $user->id : null,
            'staff_id' => $staff ? $staff->id : null,
            'business_name' => $businessName,
            'action' => $action,
            'device' => $device,
            'ip_address' => $ip,
            'details' => $details,
        ]);
    }
}
