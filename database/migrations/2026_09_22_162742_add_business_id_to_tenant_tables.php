<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tenant tables that need business_id added.
     */
    private array $tables = [
        'staffs',
        'standard_items',
        'product_variants',
        'variant_items',
        'cart_items',
        'add_customers',
        'add_discounts',
        'categories',
        'suppliers',
        'units',
        'branches',
        'branch_inventory',
        'activity_logs',
    ];

    public function up(): void
    {
        // 1. Add business_id column to each tenant table
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->unsignedBigInteger('business_id')->nullable()->after('id');
                $t->index('business_id', $t->getTable().'_business_id_idx');
            });
        }

        // 2. Backfill: join each table's business_name to the owner in users
        //    Owner = users row where (addby IS NULL OR addby = '')
        foreach ($this->tables as $table) {
            // Verify the table actually has a business_name column before joining
            $cols = DB::select("SHOW COLUMNS FROM `{$table}` LIKE 'business_name'");
            if (empty($cols)) {
                continue;
            }

            DB::statement("
                UPDATE `{$table}` t
                INNER JOIN users u
                    ON LOWER(u.business_name) = LOWER(t.business_name)
                    AND (u.addby IS NULL OR u.addby = '')
                SET t.business_id = u.id
                WHERE t.business_id IS NULL
            ");
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $t) use ($table) {
                try {
                    $t->dropIndex($table.'_business_id_idx');
                } catch (\Exception $e) {}
                $t->dropColumn('business_id');
            });
        }
    }
};
