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
        // Add missing User Management features that layout.blade.php checks for
        DB::table('subscription_features')->insert([
            [
                'name' => 'Manage Staff',
                'slug' => 'manage_staff',
                'description' => 'Create and manage staff accounts',
                'category' => 'users',
                'role' => 'business_creator',
                'is_active' => true,
                'sort_order' => 43,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Manage Managers',
                'slug' => 'manage_managers',
                'description' => 'Create and manage manager accounts',
                'category' => 'users',
                'role' => 'business_creator',
                'is_active' => true,
                'sort_order' => 44,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Manage Branch Staff',
                'slug' => 'manager_manage_staff',
                'description' => 'Allow branch managers to create and manage staff',
                'category' => 'users',
                'role' => 'manager',
                'is_active' => true,
                'sort_order' => 45,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // Now, append these to the appropriate plans
        $plans = DB::table('subscription_plans')->get();

        foreach ($plans as $plan) {
            $features = json_decode($plan->features, true) ?? [];
            $planName = strtolower($plan->name);

            // All plans can manage staff
            if (!in_array('manage_staff', $features)) {
                $features[] = 'manage_staff';
            }

            // Standard, Premium, Basic can manage managers
            if (in_array($planName, ['standard', 'premium', 'basic']) && !in_array('manage_managers', $features)) {
                $features[] = 'manage_managers';
            }

            // Standard and Premium managers can manage staff
            if (in_array($planName, ['standard', 'premium']) && !in_array('manager_manage_staff', $features)) {
                $features[] = 'manager_manage_staff';
            }

            DB::table('subscription_plans')
                ->where('id', $plan->id)
                ->update(['features' => json_encode(array_values(array_unique($features)))]);
        }

        // Clear cache
        try {
            \Illuminate\Support\Facades\Cache::forget('active_subscription_plans');
            foreach ($plans as $plan) {
                \Illuminate\Support\Facades\Cache::forget("subscription_plan_{$plan->id}");
            }
        } catch (\Exception $e) {
            // Ignore
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('subscription_features')
            ->whereIn('slug', ['manage_staff', 'manage_managers', 'manager_manage_staff'])
            ->delete();
            
        // We don't strictly need to remove them from the JSON array in down(), 
        // as invalid slugs are just ignored, but for cleanliness:
        $plans = DB::table('subscription_plans')->get();
        foreach ($plans as $plan) {
            $features = json_decode($plan->features, true) ?? [];
            $features = array_filter($features, function($f) {
                return !in_array($f, ['manage_staff', 'manage_managers', 'manager_manage_staff']);
            });
            DB::table('subscription_plans')
                ->where('id', $plan->id)
                ->update(['features' => json_encode(array_values($features))]);
        }
    }
};
