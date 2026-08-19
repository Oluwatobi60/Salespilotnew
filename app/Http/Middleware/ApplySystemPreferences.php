<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Config;

class ApplySystemPreferences
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Apply system timezone
        $timezone = default_timezone();
        if ($timezone) {
            date_default_timezone_set($timezone);
        }

        // Apply upload limits
        $maxUpload = max_upload_size_mb();
        if ($maxUpload) {
            config(['app.max_upload_size' => $maxUpload]);
        }

        // Enforce Force HTTPS policy
        if (setting('force_https') == '1' && !$request->secure() && app()->environment('production')) {
            return redirect()->secure($request->getRequestUri());
        }

        // Enforce IP Whitelist policy
        $whitelist = setting('ip_whitelist');
        if ($whitelist && !app()->environment('local')) {
            $ips = array_map('trim', explode(',', $whitelist));
            if (!in_array($request->ip(), $ips)) {
                abort(403, 'Access denied. Your IP address (' . $request->ip() . ') is not whitelisted.');
            }
        }

        return $next($request);
    }
}
