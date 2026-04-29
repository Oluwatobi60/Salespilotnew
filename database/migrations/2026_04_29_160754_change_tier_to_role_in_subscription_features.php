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
        // Rename tier to role
        Schema::table('subscription_features', function (Blueprint $table) {
            $table->renameColumn('tier', 'role');
        });

        // Reorganize features by role
        // BUSINESS CREATOR / SUPER ADMIN FEATURES (8 features)
        DB::table('subscription_features')->where('slug', 'advanced_dashboard')->update([
            'role' => 'business_creator',
            'name' => 'Advanced Dashboard & Analytics',
            'description' => 'Full access to business analytics and insights'
        ]);
        DB::table('subscription_features')->where('slug', 'realtime_analytics')->update([
            'role' => 'business_creator',
            'name' => 'Real-Time Business Analytics',
            'description' => 'Live business performance metrics'
        ]);
        DB::table('subscription_features')->where('slug', 'profit_loss_reports')->update([
            'role' => 'business_creator',
            'name' => 'Profit & Loss Reports',
            'description' => 'Comprehensive financial reports'
        ]);
        DB::table('subscription_features')->where('slug', 'api_access')->update([
            'role' => 'business_creator',
            'name' => 'API Access',
            'description' => 'Integrate with third-party systems'
        ]);
        DB::table('subscription_features')->where('slug', 'multi_branch')->update([
            'role' => 'business_creator',
            'name' => 'Multi-Branch Management',
            'description' => 'Manage multiple branches from one account'
        ]);
        DB::table('subscription_features')->where('slug', 'export_data')->update([
            'role' => 'business_creator',
            'name' => 'Data Export (CSV/Excel)',
            'description' => 'Export all business data'
        ]);
        DB::table('subscription_features')->where('slug', 'dedicated_account_manager')->update([
            'role' => 'business_creator',
            'name' => 'Dedicated Account Manager',
            'description' => 'Personal support for your business'
        ]);
        DB::table('subscription_features')->where('slug', 'phone_support')->update([
            'role' => 'business_creator',
            'name' => '24/7 Phone Support',
            'description' => 'Priority phone support anytime'
        ]);

        // MANAGER FEATURES (9 features)
        DB::table('subscription_features')->where('slug', 'basic_dashboard')->update([
            'role' => 'manager',
            'name' => 'Manager Dashboard',
            'description' => 'Overview of branch operations and performance'
        ]);
        DB::table('subscription_features')->where('slug', 'advanced_inventory')->update([
            'role' => 'manager',
            'name' => 'Inventory Management',
            'description' => 'Manage stock, suppliers, and orders'
        ]);
        DB::table('subscription_features')->where('slug', 'supplier_management')->update([
            'role' => 'manager',
            'name' => 'Supplier Management',
            'description' => 'Manage supplier information and orders'
        ]);
        DB::table('subscription_features')->where('slug', 'advanced_reports')->update([
            'role' => 'manager',
            'name' => 'Sales & Inventory Reports',
            'description' => 'Generate detailed reports for branch'
        ]);
        DB::table('subscription_features')->where('slug', 'customer_management')->update([
            'role' => 'manager',
            'name' => 'Customer Management',
            'description' => 'Track customer data and purchase history'
        ]);
        DB::table('subscription_features')->where('slug', 'advanced_user_roles')->update([
            'role' => 'manager',
            'name' => 'Staff Role Management',
            'description' => 'Assign roles and permissions to staff'
        ]);
        DB::table('subscription_features')->where('slug', 'low_stock_alerts')->update([
            'role' => 'manager',
            'name' => 'Low Stock Alerts',
            'description' => 'Get notified when inventory is low'
        ]);
        DB::table('subscription_features')->where('slug', 'activity_logs')->update([
            'role' => 'manager',
            'name' => 'Activity Logs & Audit Trail',
            'description' => 'Track all staff actions and changes'
        ]);
        DB::table('subscription_features')->where('slug', 'stock_transfer')->update([
            'role' => 'manager',
            'name' => 'Stock Transfer Between Branches',
            'description' => 'Transfer inventory between locations'
        ]);

        // STAFF FEATURES (6 features)
        DB::table('subscription_features')->where('slug', 'pos_system')->update([
            'role' => 'staff',
            'name' => 'POS System',
            'description' => 'Process sales and transactions'
        ]);
        DB::table('subscription_features')->where('slug', 'invoicing')->update([
            'role' => 'staff',
            'name' => 'Invoice Generation',
            'description' => 'Create and print invoices'
        ]);
        DB::table('subscription_features')->where('slug', 'basic_inventory')->update([
            'role' => 'staff',
            'name' => 'View Inventory',
            'description' => 'View stock levels and product information'
        ]);
        DB::table('subscription_features')->where('slug', 'basic_reports')->update([
            'role' => 'staff',
            'name' => 'View Basic Reports',
            'description' => 'View sales summaries and basic reports'
        ]);
        DB::table('subscription_features')->where('slug', 'discounts_promotions')->update([
            'role' => 'staff',
            'name' => 'Apply Discounts',
            'description' => 'Apply approved discounts to sales'
        ]);
        DB::table('subscription_features')->where('slug', 'basic_user_roles')->update([
            'role' => 'staff',
            'name' => 'Basic Profile Access',
            'description' => 'View and update own profile'
        ]);

        // BRANCH FEATURES (3 features)
        DB::table('subscription_features')->where('slug', 'brm_system')->update([
            'role' => 'branch',
            'name' => 'Branch Request Management (BRM)',
            'description' => 'Request items from main inventory'
        ]);
        DB::table('subscription_features')->where('slug', 'email_support')->update([
            'role' => 'branch',
            'name' => 'Branch Communication',
            'description' => 'Email notifications and updates'
        ]);
        DB::table('subscription_features')->where('slug', 'priority_support')->update([
            'role' => 'branch',
            'name' => 'Branch Support Access',
            'description' => 'Get help and support for branch operations'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscription_features', function (Blueprint $table) {
            $table->renameColumn('role', 'tier');
        });
    }
};
