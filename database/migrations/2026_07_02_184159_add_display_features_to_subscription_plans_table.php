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
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->json('display_features')->nullable()->after('features');
        });

        // Pre-populate with welcome page features
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
                ->update(['display_features' => json_encode($features)]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->dropColumn('display_features');
        });
    }
};
