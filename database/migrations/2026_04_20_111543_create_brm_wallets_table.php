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
        Schema::create('brm_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brm_id')->unique()->constrained('brms')->onDelete('cascade');
            $table->decimal('balance', 15, 2)->default(0)->comment('Current wallet balance');
            $table->decimal('total_earned', 15, 2)->default(0)->comment('Total commissions earned');
            $table->decimal('total_withdrawn', 15, 2)->default(0)->comment('Total amount withdrawn');
            $table->decimal('pending_approval', 15, 2)->default(0)->comment('Amount pending approval');
            $table->timestamps();
            
            $table->index('brm_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brm_wallets');
    }
};
