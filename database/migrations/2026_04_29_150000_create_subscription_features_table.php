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
        Schema::create('subscription_features', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Feature name (e.g., 'Advanced Analytics')
            $table->string('slug')->unique(); // Feature identifier (e.g., 'advanced_analytics')
            $table->text('description')->nullable(); // Feature description
            $table->string('category')->default('general'); // Category: general, reports, inventory, sales, etc.
            $table->boolean('is_active')->default(true); // Whether feature is available
            $table->integer('sort_order')->default(0); // Display order
            $table->timestamps();
        });

        // Insert default features
        $features = [
            // General Features
            ['name' => 'Basic Dashboard', 'slug' => 'basic_dashboard', 'description' => 'Access to basic dashboard with essential metrics', 'category' => 'general', 'sort_order' => 1],
            ['name' => 'Advanced Dashboard', 'slug' => 'advanced_dashboard', 'description' => 'Full dashboard with advanced analytics and insights', 'category' => 'general', 'sort_order' => 2],
            ['name' => 'Multi-Branch Support', 'slug' => 'multi_branch', 'description' => 'Manage multiple branches/locations', 'category' => 'general', 'sort_order' => 3],
            ['name' => 'BRM System', 'slug' => 'brm_system', 'description' => 'Business Relationship Manager functionality', 'category' => 'general', 'sort_order' => 4],
            ['name' => 'API Access', 'slug' => 'api_access', 'description' => 'Access to REST API for integrations', 'category' => 'general', 'sort_order' => 5],
            
            // Inventory Features
            ['name' => 'Basic Inventory', 'slug' => 'basic_inventory', 'description' => 'Basic inventory management', 'category' => 'inventory', 'sort_order' => 10],
            ['name' => 'Advanced Inventory', 'slug' => 'advanced_inventory', 'description' => 'Advanced inventory with batch tracking, expiry alerts', 'category' => 'inventory', 'sort_order' => 11],
            ['name' => 'Low Stock Alerts', 'slug' => 'low_stock_alerts', 'description' => 'Automated low stock notifications', 'category' => 'inventory', 'sort_order' => 12],
            ['name' => 'Stock Transfer', 'slug' => 'stock_transfer', 'description' => 'Transfer inventory between branches', 'category' => 'inventory', 'sort_order' => 13],
            ['name' => 'Supplier Management', 'slug' => 'supplier_management', 'description' => 'Manage suppliers and purchase orders', 'category' => 'inventory', 'sort_order' => 14],
            
            // Sales Features
            ['name' => 'POS System', 'slug' => 'pos_system', 'description' => 'Point of Sale system for quick transactions', 'category' => 'sales', 'sort_order' => 20],
            ['name' => 'Invoicing', 'slug' => 'invoicing', 'description' => 'Create and manage invoices', 'category' => 'sales', 'sort_order' => 21],
            ['name' => 'Customer Management', 'slug' => 'customer_management', 'description' => 'Track customer information and history', 'category' => 'sales', 'sort_order' => 22],
            ['name' => 'Discounts & Promotions', 'slug' => 'discounts_promotions', 'description' => 'Set up discounts and promotional campaigns', 'category' => 'sales', 'sort_order' => 23],
            
            // Reports & Analytics
            ['name' => 'Basic Reports', 'slug' => 'basic_reports', 'description' => 'Standard sales and inventory reports', 'category' => 'reports', 'sort_order' => 30],
            ['name' => 'Advanced Reports', 'slug' => 'advanced_reports', 'description' => 'Detailed analytics and custom reports', 'category' => 'reports', 'sort_order' => 31],
            ['name' => 'Export Data', 'slug' => 'export_data', 'description' => 'Export reports to Excel, PDF, CSV', 'category' => 'reports', 'sort_order' => 32],
            ['name' => 'Real-time Analytics', 'slug' => 'realtime_analytics', 'description' => 'Live data and real-time insights', 'category' => 'reports', 'sort_order' => 33],
            ['name' => 'Profit & Loss Reports', 'slug' => 'profit_loss_reports', 'description' => 'Financial reports with P&L statements', 'category' => 'reports', 'sort_order' => 34],
            
            // User Management
            ['name' => 'Basic User Roles', 'slug' => 'basic_user_roles', 'description' => 'Manager and staff roles', 'category' => 'users', 'sort_order' => 40],
            ['name' => 'Advanced User Roles', 'slug' => 'advanced_user_roles', 'description' => 'Custom roles with granular permissions', 'category' => 'users', 'sort_order' => 41],
            ['name' => 'Activity Logs', 'slug' => 'activity_logs', 'description' => 'Track user actions and audit trail', 'category' => 'users', 'sort_order' => 42],
            
            // Support Features
            ['name' => 'Email Support', 'slug' => 'email_support', 'description' => 'Email customer support', 'category' => 'support', 'sort_order' => 50],
            ['name' => 'Priority Support', 'slug' => 'priority_support', 'description' => '24/7 priority customer support', 'category' => 'support', 'sort_order' => 51],
            ['name' => 'Phone Support', 'slug' => 'phone_support', 'description' => 'Direct phone support', 'category' => 'support', 'sort_order' => 52],
            ['name' => 'Dedicated Account Manager', 'slug' => 'dedicated_account_manager', 'description' => 'Personal account manager', 'category' => 'support', 'sort_order' => 53],
        ];

        foreach ($features as $feature) {
            DB::table('subscription_features')->insert(array_merge($feature, [
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_features');
    }
};
