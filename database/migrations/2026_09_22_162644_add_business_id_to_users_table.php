<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('business_id')->nullable()->after('brm_id');
            $table->index('business_id', 'users_business_id_idx');
        });

        // Step 1: For business owners (addby IS NULL), business_id = their own id
        DB::statement("UPDATE users SET business_id = id WHERE addby IS NULL OR addby = ''");

        // Step 2: For managers/staff added by an owner (addby = owner email),
        // set business_id to match the owner's id
        DB::statement("
            UPDATE users u
            INNER JOIN users owner ON (owner.email = u.addby AND (owner.addby IS NULL OR owner.addby = ''))
            SET u.business_id = owner.id
            WHERE u.addby IS NOT NULL AND u.addby != '' AND u.business_id IS NULL
        ");

        // Step 3: Handle any remaining nulls (staff added by managers — walk up one level)
        DB::statement("
            UPDATE users u
            INNER JOIN users mgr ON mgr.email = u.addby
            INNER JOIN users owner ON owner.id = mgr.business_id
            SET u.business_id = owner.id
            WHERE u.business_id IS NULL AND u.addby IS NOT NULL
        ");
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_business_id_idx');
            $table->dropColumn('business_id');
        });
    }
};
