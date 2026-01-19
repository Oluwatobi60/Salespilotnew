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
        Schema::table('add_customers', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('address');
            $table->unsignedBigInteger('staff_id')->nullable()->after('user_id');

            // Add foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('staff_id')->references('id')->on('staffs')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('add_customers', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['staff_id']);
            $table->dropColumn(['user_id', 'staff_id']);
        });
    }
};
