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
        // Free Plan Features
        $freeFeatures = [
            'basic_dashboard',
            'basic_inventory',
            'pos_system',
            'basic_reports',
            'basic_user_roles',
            'email_support'
        ];

        // Basic Plan Features
        $basicFeatures = array_merge($freeFeatures, [
            'advanced_inventory',
            'invoicing',
            'customer_management',
            'priority_support',
            'manager_edit_items_features'
        ]);

        // Standard Plan Features
        $standardFeatures = array_merge($basicFeatures, [
            'advanced_dashboard',
            'multi_branch',
            'low_stock_alerts',
            'stock_transfer',
            'supplier_management',
            'discounts_promotions',
            'advanced_reports',
            'export_data',
            'advanced_user_roles',
            'manager_edit_subscription'
        ]);

        // Get all active features for Premium
        $allFeatures = DB::table('subscription_features')->where('is_active', true)->pluck('slug')->toArray();
        $premiumFeatures = $allFeatures;

        $plansFeatures = [
            'free' => $freeFeatures,
            'basic' => $basicFeatures,
            'standard' => $standardFeatures,
            'premium' => $premiumFeatures
        ];

        foreach ($plansFeatures as $name => $features) {
            DB::table('subscription_plans')
                ->where('name', $name)
                ->update(['features' => json_encode(array_values(array_unique($features)))]);
        }
        
        // Also clear cache for all plans so the changes take effect immediately
        // Though Cache facade cannot be used here safely without application context sometimes,
        // it's usually safe in a Laravel migration.
        try {
            \Illuminate\Support\Facades\Cache::forget('active_subscription_plans');
            $plans = DB::table('subscription_plans')->get();
            foreach ($plans as $plan) {
                \Illuminate\Support\Facades\Cache::forget("subscription_plan_{$plan->id}");
            }
        } catch (\Exception $e) {
            // Ignore cache errors during migration
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to old text descriptions
        $plansFeatures = [
            'free' => [
                '1 Manager/Administrator Account',
                '1 Staff Account',
                'Basic Inventory Management',
                'Sales Tracking',
                'Email Support'
            ],
            'basic' => [
                '1 Manager/Administrator Account',
                '2 Staff Accounts',
                'Advanced Inventory Management',
                'Sales & Purchase Tracking',
                'Basic Reports & Analytics',
                'Priority Email Support'
            ],
            'standard' => [
                '2 Manager/Administrator Accounts',
                'Up to 4 Staff Accounts',
                'Allows 2 branches',
                'Advanced Inventory Management',
                'Sales & Purchase Tracking',
                'Basic Reports & Analytics',
                'Priority Email Support'
            ],
            'premium' => [
                '3 Manager/Administrator Accounts',
                'Unlimited Staff Accounts',
                'Full Inventory Management',
                'Advanced Reports & Analytics',
                'Multi-branch Support',
                '24/7 Priority Support',
                'Custom Integrations'
            ]
        ];

        foreach ($plansFeatures as $name => $features) {
            DB::table('subscription_plans')
                ->where('name', $name)
                ->update(['features' => json_encode($features)]);
        }
    }
};
