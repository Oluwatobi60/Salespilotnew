<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add business_name, manager_name, manager_email to product_variants
        Schema::table('product_variants', function (Blueprint $table) {
            $table->string('business_name')->nullable()->after('id');
            $table->string('manager_name')->nullable()->after('business_name');
            $table->string('manager_email')->nullable()->after('manager_name');
        });

        // Add business_name, manager_name, manager_email to standard_items
        Schema::table('standard_items', function (Blueprint $table) {
            $table->string('business_name')->nullable()->after('id');
            $table->string('manager_name')->nullable()->after('business_name');
            $table->string('manager_email')->nullable()->after('manager_name');
        });

        // Add business_name, manager_name, manager_email to cart_items
        Schema::table('cart_items', function (Blueprint $table) {
            $table->string('business_name')->nullable()->after('id');
            $table->string('manager_name')->nullable()->after('business_name');
            $table->string('manager_email')->nullable()->after('manager_name');
        });

        // Add business_name, manager_name, manager_email to add_discounts
        Schema::table('add_discounts', function (Blueprint $table) {
            $table->string('business_name')->nullable()->after('id');
            $table->string('manager_name')->nullable()->after('business_name');
            $table->string('manager_email')->nullable()->after('manager_name');
        });

        // Add business_name, manager_name, manager_email to add_customers
        Schema::table('add_customers', function (Blueprint $table) {
            $table->string('business_name')->nullable()->after('id');
            $table->string('manager_name')->nullable()->after('business_name');
            $table->string('manager_email')->nullable()->after('manager_name');
        });

        // Add business_name, manager_name, manager_email to categories
        Schema::table('categories', function (Blueprint $table) {
            $table->string('business_name')->nullable()->after('id');
            $table->string('manager_name')->nullable()->after('business_name');
            $table->string('manager_email')->nullable()->after('manager_name');
        });

        // Add business_name, manager_name, manager_email to suppliers
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('business_name')->nullable()->after('id');
            $table->string('manager_name')->nullable()->after('business_name');
            $table->string('manager_email')->nullable()->after('manager_name');
        });

        // Add business_name, manager_name, manager_email to units
        Schema::table('units', function (Blueprint $table) {
            $table->string('business_name')->nullable()->after('id');
            $table->string('manager_name')->nullable()->after('business_name');
            $table->string('manager_email')->nullable()->after('manager_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn(['business_name', 'manager_name', 'manager_email']);
        });

        Schema::table('standard_items', function (Blueprint $table) {
            $table->dropColumn(['business_name', 'manager_name', 'manager_email']);
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropColumn(['business_name', 'manager_name', 'manager_email']);
        });

        Schema::table('add_discounts', function (Blueprint $table) {
            $table->dropColumn(['business_name', 'manager_name', 'manager_email']);
        });

        Schema::table('add_customers', function (Blueprint $table) {
            $table->dropColumn(['business_name', 'manager_name', 'manager_email']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['business_name', 'manager_name', 'manager_email']);
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn(['business_name', 'manager_name', 'manager_email']);
        });

        Schema::table('units', function (Blueprint $table) {
            $table->dropColumn(['business_name', 'manager_name', 'manager_email']);
        });
    }
};
