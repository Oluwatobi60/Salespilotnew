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
        // Main bundle items table
        Schema::create('bundle_items', function (Blueprint $table) {
            $table->id();
            $table->string('bundle_name');
            $table->string('bundle_code')->unique()->nullable();
            $table->string('category');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->string('unit');
            $table->string('barcode')->nullable();
            $table->text('description')->nullable();
            $table->string('bundle_image')->nullable();

            // Pricing information
            $table->decimal('total_item_cost', 10, 2)->default(0); // Sum of all component costs
            $table->decimal('assembly_fee', 10, 2)->default(0); // Packaging/assembly cost
            $table->decimal('total_bundle_cost', 10, 2)->default(0); // Total cost (items + assembly)
            $table->decimal('bundle_selling_price', 10, 2);
            $table->decimal('individual_total', 10, 2)->default(0); // If bought separately
            $table->decimal('customer_savings', 10, 2)->default(0); // Discount amount
            $table->decimal('profit_margin', 8, 2)->nullable(); // Percentage
            $table->decimal('bundle_profit', 10, 2)->nullable(); // Profit per bundle
            $table->decimal('tax_rate', 5, 2)->default(0);

            // Stock management
            $table->integer('max_possible_bundles')->default(0); // Based on component stock
            $table->integer('current_stock')->default(0);
            $table->integer('low_stock_threshold')->nullable();
            $table->string('storage_location')->nullable();
            $table->date('expiry_date')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        // Pivot table for bundle components (many-to-many relationship)
        Schema::create('bundle_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bundle_item_id')->constrained('bundle_items')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained('standard_items')->onDelete('cascade'); // For standard items
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->onDelete('cascade'); // For variant items
            $table->string('product_type')->default('standard'); // 'standard' or 'variant'
            $table->integer('quantity_in_bundle')->default(1); // How many of this item in bundle
            $table->decimal('unit_cost', 10, 2); // Cost per unit at time of bundle creation
            $table->decimal('subtotal', 10, 2); // quantity * unit_cost
            $table->timestamps();

            // Ensure either product_id or variant_id is set, not both
            $table->index(['bundle_item_id', 'product_id']);
            $table->index(['bundle_item_id', 'variant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bundle_components');
        Schema::dropIfExists('bundle_items');
    }
};
