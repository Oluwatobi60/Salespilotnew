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
        Schema::table('variant_items', function (Blueprint $table) {
            $table->string('business_name')->nullable()->after('id');
            $table->string('manager_name')->nullable()->after('business_name');
            $table->string('manager_email')->nullable()->after('manager_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('variant_items', function (Blueprint $table) {
            $table->dropColumn(['business_name', 'manager_name', 'manager_email']);
        });
    }
};
