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
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brm_id')->constrained('brms')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('user_subscription_id')->nullable()->constrained('user_subscriptions')->onDelete('set null');
            $table->decimal('subscription_amount', 12, 2)->comment('Amount customer paid for subscription');
            $table->decimal('commission_rate', 5, 2)->default(10)->comment('Commission percentage (e.g., 10 for 10%)');
            $table->decimal('commission_amount', 12, 2)->comment('Calculated commission amount');
            $table->enum('status', ['pending', 'approved', 'paid', 'rejected'])->default('pending');
            $table->enum('commission_type', ['referral', 'renewal', 'upgrade'])->default('referral');
            $table->text('notes')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->timestamps();

            // Indexes for faster queries
            $table->index('brm_id');
            $table->index('user_id');
            $table->index('status');
            $table->index('commission_type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
};
