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
        // Add branch tracking to cart_items
        Schema::table('cart_items', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('staff_id')->constrained('branches')->onDelete('set null');
            $table->string('branch_name')->nullable()->after('branch_id');

            $table->index(['branch_id'], 'cart_items_branch_idx');
        });

        // Add branch tracking to sell_products
        Schema::table('sell_products', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('id')->constrained('branches')->onDelete('set null');
            $table->string('branch_name')->nullable()->after('branch_id');

            $table->index(['branch_id'], 'sell_products_branch_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropIndex('cart_items_branch_idx');
            $table->dropColumn(['branch_id', 'branch_name']);
        });

        Schema::table('sell_products', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropIndex('sell_products_branch_idx');
            $table->dropColumn(['branch_id', 'branch_name']);
        });
    }
};
