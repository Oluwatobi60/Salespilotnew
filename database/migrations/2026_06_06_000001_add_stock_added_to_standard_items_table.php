<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('standard_items', function (Blueprint $table) {
            if (!Schema::hasColumn('standard_items', 'stock_added')) {
                $table->integer('stock_added')->default(0)->after('current_stock');
            }
        });
    }

    public function down(): void
    {
        Schema::table('standard_items', function (Blueprint $table) {
            if (Schema::hasColumn('standard_items', 'stock_added')) {
                $table->dropColumn('stock_added');
            }
        });
    }
};
