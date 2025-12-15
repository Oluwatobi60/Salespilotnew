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
        // Base variant items table (stores the parent item info)
        Schema::create('variant_items', function (Blueprint $table) {
            $table->id();

            // Base Item Information
            $table->string('item_name');
            $table->string('item_code')->unique();
            $table->string('barcode')->nullable();
            $table->string('category');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('set null');
            $table->foreignId('unit_id')->nullable()->constrained('units')->onDelete('set null');
            $table->string('brand')->nullable();
            $table->text('description')->nullable();
            $table->string('item_image')->nullable();

            // Variant Set Configuration (stores which variant sets are used)
            // Example: {"set1": "Size", "set2": "Color", "set3": null}
            $table->json('variant_sets')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        // Variant options table (stores individual variants)
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_item_id')->constrained('variant_items')->onDelete('cascade');

            // Variant Identification
            $table->string('variant_name'); // e.g., "Small - Red - Classic"
            $table->string('sku')->unique();
            $table->string('barcode')->nullable();

            // Variant Options (JSON format to store which options from each set)
            // Example: {"set1": "Small", "set2": "Red", "set3": "Classic"}
            $table->json('variant_options')->nullable();

            // Primary, Secondary, Tertiary values for easier querying
            $table->string('primary_value')->nullable();
            $table->string('secondary_value')->nullable();
            $table->string('tertiary_value')->nullable();

            // Sell Item Toggle
            $table->boolean('sell_item')->default(true);

            // Pricing Type (fixed, manual, margin, range)
            $table->enum('pricing_type', ['fixed', 'manual', 'margin', 'range'])->default('fixed');

            // Fixed Pricing Fields
            $table->decimal('cost_price', 15, 2)->nullable(); // Fixed pricing cost price
            $table->decimal('selling_price', 15, 2)->nullable();
            $table->decimal('profit_margin', 8, 2)->nullable(); // percentage
            $table->decimal('potential_profit', 15, 2)->nullable();

            // Manual Pricing Fields
            $table->decimal('manual_cost_price', 15, 2)->nullable(); // Manual pricing cost price

            // Margin Pricing Fields
            $table->decimal('margin_cost_price', 15, 2)->nullable(); // Margin pricing cost price
            $table->decimal('target_margin', 8, 2)->nullable(); // percentage
            $table->decimal('calculated_price', 15, 2)->nullable();
            $table->decimal('margin_profit', 15, 2)->nullable();

            // Range Pricing Fields
            $table->decimal('range_cost_price', 15, 2)->nullable(); // Range pricing cost price
            $table->decimal('min_price', 15, 2)->nullable();
            $table->decimal('max_price', 15, 2)->nullable();
            $table->decimal('range_potential_profit', 15, 2)->nullable();

            // Additional Pricing Options (common for all pricing types)
            $table->decimal('tax_rate', 5, 2)->default(0); // percentage
           /*  $table->decimal('discount', 5, 2)->default(0); */ // percentage
            $table->decimal('final_price', 15, 2)->nullable();

            // Stock Management
            $table->integer('stock_quantity')->default(0);
            $table->integer('low_stock_threshold')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('location')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        // Pricing tiers table for range pricing
        Schema::create('variant_pricing_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained('product_variants')->onDelete('cascade');

            $table->integer('min_quantity');
            $table->integer('max_quantity')->nullable();
            $table->decimal('price_per_unit', 15, 2);

            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variant_pricing_tiers');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('variant_items');
    }
};
