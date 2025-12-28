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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->string('cart_name')->nullable();
            $table->foreignId('customer_id')->nullable()->constrained('add_customers')->onDelete('cascade');
            $table->string('customer_name')->nullable();
            $table->unsignedBigInteger('item_id');
            $table->string('item_type')->default('standard'); // standard, variant, bundle
            $table->string('item_name');
            $table->decimal('item_price', 10, 2);
            $table->integer('quantity')->default(1);
            $table->text('note')->nullable();
            $table->string('item_image')->nullable();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->enum('status', ['pending', 'saved', 'completed'])->default('pending');
            $table->string('session_id')->nullable();
            $table->string('receipt_number')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('staff_id')->nullable()->constrained('staffs')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
