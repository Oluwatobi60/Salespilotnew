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
        // Add tier column
        Schema::table('subscription_features', function (Blueprint $table) {
            $table->string('tier')->after('category')->default('free');
        });

        // Reorganize features by tier
        // FREE TIER - Basic features (7 features)
        DB::table('subscription_features')->where('slug', 'dashboard_access')->update([
            'tier' => 'free',
            'name' => 'Basic Dashboard Access',
            'description' => 'Access to basic dashboard with essential metrics'
        ]);
        DB::table('subscription_features')->where('slug', 'basic_inventory')->update([
            'tier' => 'free',
            'name' => 'Basic Inventory Management',
            'description' => 'Track products and stock levels'
        ]);
        DB::table('subscription_features')->where('slug', 'pos_system')->update([
            'tier' => 'free',
            'name' => 'POS System',
            'description' => 'Point of sale for basic transactions'
        ]);
        DB::table('subscription_features')->where('slug', 'invoice_generation')->update([
            'tier' => 'free',
            'name' => 'Invoice Generation',
            'description' => 'Create and print invoices'
        ]);
        DB::table('subscription_features')->where('slug', 'basic_reports')->update([
            'tier' => 'free',
            'name' => 'Basic Sales Reports',
            'description' => 'View simple sales and inventory reports'
        ]);
        DB::table('subscription_features')->where('slug', 'email_support')->update([
            'tier' => 'free',
            'name' => 'Email Support',
            'description' => 'Get help via email during business hours'
        ]);
        DB::table('subscription_features')->where('slug', 'basic_user_management')->update([
            'tier' => 'free',
            'name' => 'Basic User Management',
            'description' => 'Add and manage user accounts'
        ]);

        // BASIC TIER - Enhanced features (7 features)
        DB::table('subscription_features')->where('slug', 'multi_user_support')->update([
            'tier' => 'basic',
            'name' => 'Multi-User Support',
            'description' => 'Support for multiple staff accounts'
        ]);
        DB::table('subscription_features')->where('slug', 'email_notifications')->update([
            'tier' => 'basic',
            'name' => 'Email Notifications',
            'description' => 'Automated email alerts and notifications'
        ]);
        DB::table('subscription_features')->where('slug', 'stock_alerts')->update([
            'tier' => 'basic',
            'name' => 'Stock Alerts',
            'description' => 'Get notified when stock is low'
        ]);
        DB::table('subscription_features')->where('slug', 'customer_management')->update([
            'tier' => 'basic',
            'name' => 'Customer Management',
            'description' => 'Track customer information and purchase history'
        ]);
        DB::table('subscription_features')->where('slug', 'sales_reports')->update([
            'tier' => 'basic',
            'name' => 'Advanced Sales Reports',
            'description' => 'Detailed sales analytics and trends'
        ]);
        DB::table('subscription_features')->where('slug', 'role_based_access')->update([
            'tier' => 'basic',
            'name' => 'Role-Based Access Control',
            'description' => 'Define permissions and roles for users'
        ]);
        DB::table('subscription_features')->where('slug', 'priority_support')->update([
            'tier' => 'basic',
            'name' => 'Priority Email Support',
            'description' => 'Faster response times for support requests'
        ]);

        // STANDARD TIER - Professional features (7 features)
        DB::table('subscription_features')->where('slug', 'data_export')->update([
            'tier' => 'standard',
            'name' => 'Data Export (CSV/Excel)',
            'description' => 'Export your data to CSV and Excel formats'
        ]);
        DB::table('subscription_features')->where('slug', 'api_access')->update([
            'tier' => 'standard',
            'name' => 'API Access',
            'description' => 'Integrate with third-party applications'
        ]);
        DB::table('subscription_features')->where('slug', 'barcode_scanning')->update([
            'tier' => 'standard',
            'name' => 'Barcode Scanning',
            'description' => 'Scan products with barcode scanner'
        ]);
        DB::table('subscription_features')->where('slug', 'batch_tracking')->update([
            'tier' => 'standard',
            'name' => 'Batch & Lot Tracking',
            'description' => 'Track inventory by batch numbers'
        ]);
        DB::table('subscription_features')->where('slug', 'supplier_management')->update([
            'tier' => 'standard',
            'name' => 'Supplier Management',
            'description' => 'Manage supplier information and orders'
        ]);
        DB::table('subscription_features')->where('slug', 'advanced_analytics')->update([
            'tier' => 'standard',
            'name' => 'Advanced Analytics',
            'description' => 'Deep insights with charts and graphs'
        ]);
        DB::table('subscription_features')->where('slug', 'activity_logs')->update([
            'tier' => 'standard',
            'name' => 'Activity Logs & Audit Trail',
            'description' => 'Track all user actions and changes'
        ]);

        // PREMIUM TIER - Enterprise features (5 features)
        DB::table('subscription_features')->where('slug', 'custom_reports')->update([
            'tier' => 'premium',
            'name' => 'Custom Report Builder',
            'description' => 'Create your own custom reports'
        ]);
        DB::table('subscription_features')->where('slug', 'realtime_dashboard')->update([
            'tier' => 'premium',
            'name' => 'Real-Time Dashboard',
            'description' => 'Live updates and real-time data'
        ]);
        DB::table('subscription_features')->where('slug', 'export_pdf')->update([
            'tier' => 'premium',
            'name' => 'PDF Export & Branding',
            'description' => 'Export reports as PDF with your branding'
        ]);
        DB::table('subscription_features')->where('slug', '24_7_support')->update([
            'tier' => 'premium',
            'name' => '24/7 Phone & Chat Support',
            'description' => 'Round-the-clock support via phone and chat'
        ]);
        DB::table('subscription_features')->where('slug', 'dedicated_account_manager')->update([
            'tier' => 'premium',
            'name' => 'Dedicated Account Manager',
            'description' => 'Personal account manager for your business'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscription_features', function (Blueprint $table) {
            $table->dropColumn('tier');
        });
    }
};
