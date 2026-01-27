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
        // Drop foreign keys first, then drop tables
        Schema::dropIfExists('bundle_components');
        Schema::dropIfExists('bundle_items');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optionally recreate tables if needed
    }
};
