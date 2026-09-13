<?php

use App\Http\Middleware\ApplySystemPreferences;
use App\Http\Middleware\CheckSubscriptionStatus;
use App\Http\Middleware\RoleManager;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'rolemanager' => RoleManager::class,
            'check.subscription' => CheckSubscriptionStatus::class,
            'system.preferences' => ApplySystemPreferences::class,
        ]);

        // Apply system preferences to all web requests
        $middleware->web(append: [
            ApplySystemPreferences::class,
        ]);

        // Allow superadmin routes to bypass maintenance mode
        $middleware->preventRequestsDuringMaintenance([
            'superadmin/*',
            'superadmin-bypass',
        ]);

        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('superadmin') || $request->is('superadmin/*')) {
                return route('superadmin.login');
            }
            if ($request->is('staff') || $request->is('staff/*')) {
                return route('staff.login');
            }
            if ($request->is('brm') || $request->is('brm/*')) {
                return route('brm.login');
            }

            return route('login');
        });
        $middleware->redirectUsersTo(function () {
            if (Auth::check()) {
                $user = Auth::user();
                $hasActiveSub = UserSubscription::where('user_id', $user->id)
                    ->where('status', 'active')
                    ->where('end_date', '>=', now())
                    ->exists();

                // For managers created by another user, check creator sub
                if (! $hasActiveSub && $user->role === 'manager' && $user->addby) {
                    $creator = User::where('email', $user->addby)->first();
                    $hasActiveSub = $creator ? UserSubscription::where('user_id', $creator->id)
                        ->where('status', 'active')
                        ->where('end_date', '>=', now())
                        ->exists() : false;
                }

                if (! $hasActiveSub && $user->role !== 'superadmin') {
                    return route('plan_pricing');
                }

                if ($user->role === 'superadmin') {
                    return route('superadmin');
                }
                if ($user->role === 'manager') {
                    return route('manager');
                }
                if ($user->role === 'businessowner') {
                    return route('businessdashboard');
                }
                if ($user->role === 'staff') {
                    return route('dashboard');
                }
            }

            return '/';
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
