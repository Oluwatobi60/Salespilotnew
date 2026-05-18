<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

$user = User::where('email', 'tobestic53@gmail.com')->first();

if ($user) {
    echo "Customer Found: " . $user->business_name . "\n";
    echo "Email: " . $user->email . "\n";
    echo "User ID: " . $user->id . "\n";
    echo "BRM: " . ($user->brm ? $user->brm->name : 'None') . "\n";
    echo "Created: " . $user->created_at . "\n";

    echo "\n--- ALL SUBSCRIPTIONS ---\n";
    $subs = $user->subscriptions()->latest()->get();
    if ($subs->count() > 0) {
        foreach ($subs as $sub) {
            $daysLeft = now()->diffInDays($sub->end_date, false);
            echo "ID: {$sub->id} | Plan: " . ($sub->subscriptionPlan->name ?? 'N/A') . " | Status: {$sub->status} | Start: {$sub->start_date} | End: {$sub->end_date} | Days Left: {$daysLeft}\n";
        }
    } else {
        echo "No subscriptions found.\n";
    }

    echo "\n--- CURRENT SUBSCRIPTION (using currentSubscription relation) ---\n";
    $current = $user->currentSubscription;
    if ($current) {
        echo "Has active subscription: YES\n";
        echo "Plan: " . ($current->subscriptionPlan->name ?? 'N/A') . "\n";
        echo "Status: {$current->status}\n";
        echo "End Date: {$current->end_date}\n";
        echo "Result: This will show as ACTIVE on dashboard\n";
    } else {
        echo "Has active subscription: NO\n";
        echo "Result: This is why it shows as INACTIVE on dashboard\n";
    }
} else {
    echo "Customer not found with email: tobestic53@gmail.com\n";
}
