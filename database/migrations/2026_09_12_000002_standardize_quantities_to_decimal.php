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
        // 1. Standard Items: Standardize quantities to DECIMAL(12,2)
        Schema::table('standard_items', function (Blueprint $table) {
            $table->decimal('opening_stock', 12, 2)->default(0)->change();
            $table->decimal('current_stock', 12, 2)->default(0)->change();
            $table->decimal('stock_added', 12, 2)->default(0)->change();
            $table->decimal('low_stock_threshold', 12, 2)->nullable()->change();
        });

        // 2. Product Variants: Standardize quantities to DECIMAL(12,2)
        Schema::table('product_variants', function (Blueprint $table) {
            $table->decimal('opening_stock', 12, 2)->default(0)->change();
            $table->decimal('current_stock', 12, 2)->default(0)->change();
            $table->decimal('stock_added', 12, 2)->default(0)->change();
            $table->decimal('low_stock_threshold', 12, 2)->nullable()->change();
        });

        // 3. Cart Items: Support decimal/fractional sold quantities
        Schema::table('cart_items', function (Blueprint $table) {
            $table->decimal('quantity', 12, 2)->default(1)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->integer('quantity')->default(1)->change();
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->integer('opening_stock')->default(0)->change();
            $table->integer('current_stock')->default(0)->change();
            $table->integer('stock_added')->default(0)->change();
            $table->integer('low_stock_threshold')->nullable()->change();
        });

        Schema::table('standard_items', function (Blueprint $table) {
            $table->integer('opening_stock')->default(0)->change();
            $table->integer('current_stock')->default(0)->change();
            $table->integer('stock_added')->default(0)->change();
            $table->integer('low_stock_threshold')->nullable()->change();
        });
    }
};
