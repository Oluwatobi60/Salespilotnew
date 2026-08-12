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
        $featuresToAdd = [
            // Business Creator Reports
            ['name' => 'Sales Summary', 'slug' => 'sales_summary', 'description' => 'View overall sales summary', 'category' => 'reports', 'role' => 'business_creator', 'is_active' => true, 'sort_order' => 100],
            ['name' => 'Sales by Staff', 'slug' => 'sales_by_staff', 'description' => 'View sales broken down by staff', 'category' => 'reports', 'role' => 'business_creator', 'is_active' => true, 'sort_order' => 101],
            ['name' => 'Sales by Item', 'slug' => 'sales_by_item', 'description' => 'View sales broken down by item', 'category' => 'reports', 'role' => 'business_creator', 'is_active' => true, 'sort_order' => 102],
            ['name' => 'Sales by Category', 'slug' => 'sales_by_category', 'description' => 'View sales broken down by category', 'category' => 'reports', 'role' => 'business_creator', 'is_active' => true, 'sort_order' => 103],
            ['name' => 'Inventory Valuation', 'slug' => 'inventory_valuation', 'description' => 'View inventory valuation report', 'category' => 'reports', 'role' => 'business_creator', 'is_active' => true, 'sort_order' => 104],
            ['name' => 'Discount Report', 'slug' => 'discount_report', 'description' => 'View discount usage report', 'category' => 'reports', 'role' => 'business_creator', 'is_active' => true, 'sort_order' => 105],

            // Manager General Features
            ['name' => 'Manager POS', 'slug' => 'manager_pos', 'description' => 'Manager POS access', 'category' => 'sales', 'role' => 'manager', 'is_active' => true, 'sort_order' => 110],
            ['name' => 'Manager Inventory', 'slug' => 'manager_inventory', 'description' => 'Manager inventory access', 'category' => 'inventory', 'role' => 'manager', 'is_active' => true, 'sort_order' => 111],
            ['name' => 'Manager Suppliers', 'slug' => 'manager_suppliers', 'description' => 'Manager supplier access', 'category' => 'inventory', 'role' => 'manager', 'is_active' => true, 'sort_order' => 112],
            ['name' => 'Manager Customers', 'slug' => 'manager_customers', 'description' => 'Manager customer access', 'category' => 'sales', 'role' => 'manager', 'is_active' => true, 'sort_order' => 113],
            ['name' => 'Manager Activity Logs', 'slug' => 'manager_activity_logs', 'description' => 'Manager view activity logs', 'category' => 'users', 'role' => 'manager', 'is_active' => true, 'sort_order' => 114],
            ['name' => 'Manager Discounts', 'slug' => 'manager_discounts', 'description' => 'Manager discounts access', 'category' => 'sales', 'role' => 'manager', 'is_active' => true, 'sort_order' => 115],
            ['name' => 'Manager View Branches', 'slug' => 'manager_view_branches', 'description' => 'Manager view branch info', 'category' => 'branches', 'role' => 'manager', 'is_active' => true, 'sort_order' => 116],

            // Manager Reports
            ['name' => 'Manager Sales Summary', 'slug' => 'manager_sales_summary', 'description' => 'Manager view sales summary', 'category' => 'reports', 'role' => 'manager', 'is_active' => true, 'sort_order' => 120],
            ['name' => 'Manager Sales by Staff', 'slug' => 'manager_sales_by_staff', 'description' => 'Manager view sales by staff', 'category' => 'reports', 'role' => 'manager', 'is_active' => true, 'sort_order' => 121],
            ['name' => 'Manager Sales by Item', 'slug' => 'manager_sales_by_item', 'description' => 'Manager view sales by item', 'category' => 'reports', 'role' => 'manager', 'is_active' => true, 'sort_order' => 122],
            ['name' => 'Manager Sales by Category', 'slug' => 'manager_sales_by_category', 'description' => 'Manager view sales by category', 'category' => 'reports', 'role' => 'manager', 'is_active' => true, 'sort_order' => 123],
            ['name' => 'Manager Inventory Valuation', 'slug' => 'manager_inventory_valuation', 'description' => 'Manager view inventory valuation', 'category' => 'reports', 'role' => 'manager', 'is_active' => true, 'sort_order' => 124],
            ['name' => 'Manager Discount Report', 'slug' => 'manager_discount_report', 'description' => 'Manager view discount report', 'category' => 'reports', 'role' => 'manager', 'is_active' => true, 'sort_order' => 125],
        ];

        foreach ($featuresToAdd as $feature) {
            $feature['created_at'] = now();
            $feature['updated_at'] = now();
            DB::table('subscription_features')->updateOrInsert(
                ['slug' => $feature['slug']],
                $feature
            );
        }

        $allNewSlugs = array_column($featuresToAdd, 'slug');
        $plans = DB::table('subscription_plans')->get();

        foreach ($plans as $plan) {
            $features = json_decode($plan->features, true) ?? [];
            
            // Add all new features to all plans by default to restore functionality 
            // without downgrading users.
            foreach ($allNewSlugs as $slug) {
                if (!in_array($slug, $features)) {
                    $features[] = $slug;
                }
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
        $slugsToRemove = [
            'sales_summary', 'sales_by_staff', 'sales_by_item', 'sales_by_category', 'inventory_valuation', 'discount_report',
            'manager_pos', 'manager_inventory', 'manager_suppliers', 'manager_customers', 'manager_activity_logs', 'manager_discounts', 'manager_view_branches',
            'manager_sales_summary', 'manager_sales_by_staff', 'manager_sales_by_item', 'manager_sales_by_category', 'manager_inventory_valuation', 'manager_discount_report'
        ];

        DB::table('subscription_features')
            ->whereIn('slug', $slugsToRemove)
            ->delete();
            
        $plans = DB::table('subscription_plans')->get();
        foreach ($plans as $plan) {
            $features = json_decode($plan->features, true) ?? [];
            $features = array_filter($features, function($f) use ($slugsToRemove) {
                return !in_array($f, $slugsToRemove);
            });
            DB::table('subscription_plans')
                ->where('id', $plan->id)
                ->update(['features' => json_encode(array_values($features))]);
        }
    }
};
