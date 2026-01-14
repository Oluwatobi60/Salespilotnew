<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleManager
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if(!Auth::check()){
            // Log login attempt (not authenticated)
            \App\Helpers\ActivityLogger::log('login_attempt', 'Unauthenticated login attempt');
            if ($role === 'staff') {
                return redirect()->route('staff.login');
            }
            return redirect()->route('login');
        }
    // Log successful login
    \App\Helpers\ActivityLogger::log('login', 'User logged in');

        $authUserRole = Auth::user()->role;

        switch($role){
            case 'manager':
                if($authUserRole == 'manager'){
                    return $next($request);
                }
                break;
            case 'superadmin':
                if($authUserRole == 'superadmin'){
                    return $next($request);
                }
                break;
            case 'staff':
                if($authUserRole == 'staff'){
                    return $next($request);
                }
                break;
            case 'businessmanager':
                if($authUserRole == 'businessmanager'){
                    return $next($request);
                }
                break;
        }

        switch($authUserRole){
            case 'superadmin':
                return redirect()->route('superadmin');
            case 'manager':
                return redirect()->route('manager');
            case 'staff':
                return redirect()->route('dashboard');
           case 'businessmanager':
                return redirect()->route('businessdashboard');
        }
        return redirect()->route('login');
    }
}
