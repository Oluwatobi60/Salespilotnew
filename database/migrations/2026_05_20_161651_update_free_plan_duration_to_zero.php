<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing free plan subscriptions to have duration_months = 0
        // since they use trial_days (7 days) instead of months
        DB::table('user_subscriptions')
            ->join('subscription_plans', 'user_subscriptions.subscription_plan_id', '=', 'subscription_plans.id')
            ->where('subscription_plans.name', 'free')
            ->update(['user_subscriptions.duration_months' => 0]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optionally restore to 1 if needed (though this is not recommended)
        DB::table('user_subscriptions')
            ->join('subscription_plans', 'user_subscriptions.subscription_plan_id', '=', 'subscription_plans.id')
            ->where('subscription_plans.name', 'free')
            ->where('user_subscriptions.duration_months', 0)
            ->update(['user_subscriptions.duration_months' => 1]);
    }
};
