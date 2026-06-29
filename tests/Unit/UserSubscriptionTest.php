<?php

uses(Tests\TestCase::class);

use App\Models\UserSubscription;
use Carbon\Carbon;

it('treats a stale active subscription as expired', function () {
    $subscription = new UserSubscription();
    $subscription->status = 'active';
    $subscription->end_date = Carbon::yesterday();

    expect($subscription->effectiveStatus())->toBe('expired')
        ->and($subscription->isActive())->toBeFalse()
        ->and($subscription->isExpired())->toBeTrue();
});
