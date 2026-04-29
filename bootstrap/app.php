<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\RoleManager;
use App\Http\Middleware\CheckSubscriptionStatus;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'rolemanager' => RoleManager::class,
            'check.subscription' => CheckSubscriptionStatus::class
        ]);

        // Allow superadmin routes to bypass maintenance mode
        $middleware->preventRequestsDuringMaintenance([
            'superadmin/*',
            'superadmin-bypass',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
