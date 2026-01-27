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
        Schema::table('users', function (Blueprint $table) {
            // Drop the name field
            $table->dropColumn('name');

            // Add new fields
            $table->string('first_name')->after('id');
            $table->string('surname')->after('first_name');
            $table->string('other_name')->nullable()->after('surname');
            $table->string('business_name')->after('other_name');
            $table->string('branch_name')->after('business_name');
            $table->string('business_logo')->nullable()->after('branch_name');
            $table->string('state')->after('business_logo');
            $table->string('local_govt')->after('state');
            $table->text('address')->after('local_govt');
            $table->string('phone_number', 11)->after('address');
            $table->string('referral_code', 11)->nullable()->after('phone_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add back the name field
            $table->string('name')->after('id');

            // Drop the new fields
            $table->dropColumn([
                'first_name',
                'surname',
                'other_name',
                'business_name',
                'branch_name',
                'business_logo',
                'state',
                'local_govt',
                'address',
                'phone_number',
                'referral_code'
            ]);
        });
    }
};
