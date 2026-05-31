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
        Schema::create('receipt_settings', function (Blueprint $table) {
            $table->id();
            $table->string('business_name')->unique();
            $table->string('receipt_title')->default('SALES RECEIPT');
            $table->text('header_text')->nullable();
            $table->text('footer_text')->nullable();
            $table->string('paper_size')->default('80mm Thermal');
            $table->string('font_size')->default('Medium');
            $table->boolean('show_invoice_number')->default(true);
            $table->boolean('show_date')->default(true);
            $table->boolean('show_cashier')->default(true);
            $table->boolean('show_logo')->default(true);
            $table->boolean('show_barcode')->default(true);
            $table->boolean('show_tax_details')->default(true);
            $table->boolean('show_item_codes')->default(true);
            $table->boolean('show_discounts')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipt_settings');
    }
};
