<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule the subscription expiry check to run daily
Schedule::command('subscriptions:check-expired')->daily();

// Process auto-renewals and send expiry reminder emails (runs daily at 8 AM)
Schedule::command('subscriptions:process-renewals')->dailyAt('08:00');
