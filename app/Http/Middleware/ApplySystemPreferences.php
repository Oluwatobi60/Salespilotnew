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
            Config::set('app.max_upload_size', $maxUpload);
        }

        return $next($request);
    }
}
