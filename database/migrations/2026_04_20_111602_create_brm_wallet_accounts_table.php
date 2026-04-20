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
        Schema::create('brm_wallet_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brm_wallet_id')->constrained('brm_wallets')->onDelete('cascade');
            $table->foreignId('brm_id')->constrained('brms')->onDelete('cascade');
            $table->string('account_number');
            $table->string('account_name');
            $table->string('bank_code');
            $table->string('bank_name');
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->dateTime('verified_at')->nullable();
            $table->timestamps();
            
            $table->unique(['brm_id', 'account_number']);
            $table->index(['brm_id', 'brm_wallet_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brm_wallet_accounts');
    }
};
