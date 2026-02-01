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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'status')) {
                $table->tinyInteger('status')->default(0)->after('role'); // 0 = inactive, 1 = active
            }
            if (!Schema::hasColumn('users', 'state')) {
                $table->string('state')->nullable()->after('business_name');
            }
            if (!Schema::hasColumn('users', 'local_govt')) {
                $table->string('local_govt')->nullable()->after('state');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('users', 'state')) {
                $table->dropColumn('state');
            }
            if (Schema::hasColumn('users', 'local_govt')) {
                $table->dropColumn('local_govt');
            }
        });
    }
};
