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
        Schema::create('add_discounts', function (Blueprint $table) {
            $table->id();
            $table->string('discount_name');
            $table->decimal('discount_rate', 5, 2); // e.g., 10.00 for 10%
            $table->integer('time_used')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('add_discounts');
    }
};
