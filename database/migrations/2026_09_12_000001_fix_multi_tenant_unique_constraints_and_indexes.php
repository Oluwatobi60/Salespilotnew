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
        // 1. Standard Items: Fix global unique constraint & add tenant compound unique + query indexes
        Schema::table('standard_items', function (Blueprint $table) {
            $table->dropUnique(['item_code']);
            $table->unique(['business_name', 'item_code'], 'standard_items_business_item_code_unique');
            $table->index(['business_name', 'enable_sale'], 'standard_items_business_enable_sale_idx');
            $table->index(['business_name', 'manager_email'], 'standard_items_business_mgr_idx');
        });

        // 2. Variant Items: Fix global unique constraint & add tenant compound unique + query index
        Schema::table('variant_items', function (Blueprint $table) {
            $table->dropUnique(['item_code']);
            $table->unique(['business_name', 'item_code'], 'variant_items_business_item_code_unique');
            $table->index(['business_name'], 'variant_items_business_idx');
        });

        // 3. Product Variants: Fix global SKU unique constraint & add tenant compound unique + query index
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropUnique(['sku']);
            $table->unique(['business_name', 'sku'], 'product_variants_business_sku_unique');
            $table->index(['business_name', 'sell_item'], 'product_variants_business_sell_idx');
        });

        // 4. Cart Items: Add high-performance indexes for POS reporting & receipt lookups
        Schema::table('cart_items', function (Blueprint $table) {
            $table->index(['status', 'business_name', 'created_at'], 'cart_items_status_biz_created_idx');
            $table->index(['receipt_number', 'status'], 'cart_items_receipt_status_idx');
            $table->index(['session_id'], 'cart_items_session_idx');
        });

        // 5. Activity Logs: Add tenant timeline index
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->index(['business_name', 'created_at'], 'activity_logs_biz_created_idx');
        });

        // 6. Add Customers: Add lookup indexes
        Schema::table('add_customers', function (Blueprint $table) {
            $table->index(['business_name', 'phone_number'], 'customers_biz_phone_idx');
            $table->index(['business_name', 'email'], 'customers_biz_email_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('add_customers', function (Blueprint $table) {
            $table->dropIndex('customers_biz_phone_idx');
            $table->dropIndex('customers_biz_email_idx');
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex('activity_logs_biz_created_idx');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropIndex('cart_items_status_biz_created_idx');
            $table->dropIndex('cart_items_receipt_status_idx');
            $table->dropIndex('cart_items_session_idx');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropIndex('product_variants_business_sell_idx');
            $table->dropUnique('product_variants_business_sku_unique');
            $table->unique('sku', 'product_variants_sku_unique');
        });

        Schema::table('variant_items', function (Blueprint $table) {
            $table->dropIndex('variant_items_business_idx');
            $table->dropUnique('variant_items_business_item_code_unique');
            $table->unique('item_code', 'variant_items_item_code_unique');
        });

        Schema::table('standard_items', function (Blueprint $table) {
            $table->dropIndex('standard_items_business_mgr_idx');
            $table->dropIndex('standard_items_business_enable_sale_idx');
            $table->dropUnique('standard_items_business_item_code_unique');
            $table->unique('item_code', 'standard_items_item_code_unique');
        });
    }
};
