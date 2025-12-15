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
        Schema::create('standard_items', function (Blueprint $table) {
            $table->id();
            $table->string('item_name');
            $table->string('item_code')->unique()->nullable();
            $table->string('category');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->string('unit');
            $table->string('barcode')->nullable();
            $table->text('description')->nullable();
            $table->string('item_image')->nullable();
            $table->boolean('enable_sale')->default(true);

            // Pricing fields
            $table->enum('pricing_type', ['fixed', 'manual', 'margin', 'range'])->default('fixed');
            $table->decimal('cost_price', 15, 2);
            $table->decimal('selling_price', 15, 2)->nullable();
            $table->decimal('profit_margin', 8, 2)->nullable();
            $table->decimal('potential_profit', 15, 2)->nullable();

            // Margin pricing fields
            $table->decimal('target_margin', 8, 2)->nullable();
            $table->decimal('calculated_price', 15, 2)->nullable();
            $table->decimal('margin_profit', 15, 2)->nullable();

            // Range pricing fields
            $table->decimal('min_price', 15, 2)->nullable();
            $table->decimal('max_price', 15, 2)->nullable();
            $table->decimal('range_potential_profit', 15, 2)->nullable();

            // Tax and discount
            $table->decimal('tax_rate', 5, 2)->default(0);
           /*  $table->decimal('discount', 5, 2)->default(0); */
            $table->decimal('final_price', 15, 2)->nullable();

            // Stock tracking
            $table->boolean('track_stock')->default(true);
            $table->integer('opening_stock')->default(0);
            $table->integer('current_stock')->default(0);
            $table->integer('low_stock_threshold')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('location')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('standard_items');
    }
};
