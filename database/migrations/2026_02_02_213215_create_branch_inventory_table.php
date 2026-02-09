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
        Schema::create('branch_inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->string('business_name');

            // Item reference (polymorphic relationship)
            $table->unsignedBigInteger('item_id');
            $table->string('item_type'); // 'standard' or 'variant'

            // Inventory tracking
            $table->decimal('allocated_quantity', 10, 2)->default(0)->comment('Quantity allocated to this branch');
            $table->decimal('current_quantity', 10, 2)->default(0)->comment('Current available quantity');
            $table->decimal('sold_quantity', 10, 2)->default(0)->comment('Total sold from this branch');
            $table->decimal('low_stock_threshold', 10, 2)->nullable();

            // Allocation metadata
            $table->foreignId('allocated_by')->nullable()->constrained('users')->onDelete('set null')->comment('User who allocated inventory');
            $table->timestamp('allocated_at')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['branch_id', 'item_id', 'item_type'], 'branch_item_idx');
            $table->index(['business_name'], 'branch_inventory_business_idx');

            // Unique constraint: one record per branch-item combination
            $table->unique(['branch_id', 'item_id', 'item_type'], 'branch_item_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_inventory');
    }
};
