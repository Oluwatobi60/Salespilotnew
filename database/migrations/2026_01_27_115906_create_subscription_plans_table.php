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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // free, basic, standard, premium
            $table->decimal('monthly_price', 10, 2); // Monthly price in Naira
            $table->text('description')->nullable();
            $table->json('features')->nullable(); // Store features as JSON
            $table->integer('max_managers')->default(1); // Maximum manager accounts
            $table->integer('max_staff')->nullable(); // Maximum staff accounts (null = unlimited)
            $table->integer('max_branches')->nullable(); // Maximum branches (null = unlimited)
            $table->boolean('is_active')->default(true);
            $table->boolean('is_popular')->default(false);
            $table->integer('trial_days')->default(0); // For free trial
            $table->timestamps();
        });

        // Create user_subscriptions table to track user plan subscriptions
        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_plan_id')->constrained()->onDelete('cascade');
            $table->integer('duration_months'); // 1, 3, 6, 12
            $table->decimal('amount_paid', 10, 2); // Actual amount paid after discount
            $table->decimal('discount_percentage', 5, 2)->default(0); // Discount applied
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active');
            $table->string('payment_reference')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_subscriptions');
        Schema::dropIfExists('subscription_plans');
    }
};
