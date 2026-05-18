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
        // Add login tracking columns to users table
        Schema::table('users', function (Blueprint $table) {
            $table->integer('failed_login_attempts')->default(0)->after('password');
            $table->timestamp('locked_until')->nullable()->after('failed_login_attempts');
            $table->boolean('must_change_password')->default(false)->after('locked_until');
            $table->timestamp('last_failed_login_at')->nullable()->after('must_change_password');
        });

        // Add login tracking columns to staffs table
        Schema::table('staffs', function (Blueprint $table) {
            $table->integer('failed_login_attempts')->default(0)->after('password');
            $table->timestamp('locked_until')->nullable()->after('failed_login_attempts');
            $table->boolean('must_change_password')->default(false)->after('locked_until');
            $table->timestamp('last_failed_login_at')->nullable()->after('must_change_password');
        });

        // Add login tracking columns to superadmins table
        Schema::table('superadmins', function (Blueprint $table) {
            $table->integer('failed_login_attempts')->default(0)->after('password');
            $table->timestamp('locked_until')->nullable()->after('failed_login_attempts');
            $table->boolean('must_change_password')->default(false)->after('locked_until');
            $table->timestamp('last_failed_login_at')->nullable()->after('must_change_password');
        });

        // Add login tracking columns to brms table if it exists
        if (Schema::hasTable('brms')) {
            Schema::table('brms', function (Blueprint $table) {
                $table->integer('failed_login_attempts')->default(0)->after('password');
                $table->timestamp('locked_until')->nullable()->after('failed_login_attempts');
                $table->boolean('must_change_password')->default(false)->after('locked_until');
                $table->timestamp('last_failed_login_at')->nullable()->after('must_change_password');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['failed_login_attempts', 'locked_until', 'must_change_password', 'last_failed_login_at']);
        });

        Schema::table('staffs', function (Blueprint $table) {
            $table->dropColumn(['failed_login_attempts', 'locked_until', 'must_change_password', 'last_failed_login_at']);
        });

        Schema::table('superadmins', function (Blueprint $table) {
            $table->dropColumn(['failed_login_attempts', 'locked_until', 'must_change_password', 'last_failed_login_at']);
        });

        if (Schema::hasTable('brms')) {
            Schema::table('brms', function (Blueprint $table) {
                $table->dropColumn(['failed_login_attempts', 'locked_until', 'must_change_password', 'last_failed_login_at']);
            });
        }
    }
};
