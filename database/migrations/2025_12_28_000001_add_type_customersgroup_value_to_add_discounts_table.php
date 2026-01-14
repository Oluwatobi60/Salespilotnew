<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('add_discounts', function (Blueprint $table) {
            $table->string('type')->nullable()->after('discount_name');
            $table->string('customers_group')->nullable()->after('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('add_discounts', function (Blueprint $table) {
            $table->dropColumn(['type', 'customers_group']);
        });
    }
};
