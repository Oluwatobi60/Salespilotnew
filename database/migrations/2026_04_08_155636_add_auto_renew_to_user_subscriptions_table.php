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
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->boolean('auto_renew')->default(false)->after('payment_reference');
            $table->timestamp('last_renewed_at')->nullable()->after('auto_renew');
            $table->timestamp('renewal_notified_at')->nullable()->after('last_renewed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->dropColumn(['auto_renew', 'last_renewed_at', 'renewal_notified_at']);
        });
    }
};
