<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Discover every table that has business_name but NOT yet business_id
        $allTables = DB::select('SHOW TABLES');
        $missing   = [];

        foreach ($allTables as $row) {
            $table = array_values((array) $row)[0];

            $hasBN  = DB::select("SHOW COLUMNS FROM `{$table}` LIKE 'business_name'");
            $hasBID = DB::select("SHOW COLUMNS FROM `{$table}` LIKE 'business_id'");

            if (! empty($hasBN) && empty($hasBID)) {
                $missing[] = $table;
            }
        }

        // Add business_id column + index to each discovered table
        foreach ($missing as $table) {
            Schema::table($table, function (Blueprint $t) use ($table) {
                $t->unsignedBigInteger('business_id')->nullable()->after('id');
                $t->index('business_id', $table . '_business_id_idx');
            });

            // Backfill: join on business_name to the owner user
            DB::statement("
                UPDATE `{$table}` t
                INNER JOIN users u
                    ON LOWER(u.business_name) = LOWER(t.business_name)
                    AND (u.addby IS NULL OR u.addby = '')
                SET t.business_id = u.id
                WHERE t.business_id IS NULL
            ");

            $filled = DB::selectOne("SELECT COUNT(*) cnt FROM `{$table}` WHERE business_id IS NOT NULL")->cnt;
            $total  = DB::selectOne("SELECT COUNT(*) cnt FROM `{$table}`")->cnt;

            \Log::info("business_id backfill [{$table}]: {$filled}/{$total} rows filled");
        }
    }

    public function down(): void
    {
        // Reverse: drop business_id from any table that has it but was not in the
        // original tenant migration (i.e., we added it here)
        $allTables = DB::select('SHOW TABLES');

        // Tables already covered by the earlier migration — skip them
        $alreadyCovered = [
            'users', 'staffs', 'standard_items', 'product_variants', 'variant_items',
            'cart_items', 'add_customers', 'add_discounts', 'categories', 'suppliers',
            'units', 'branches', 'branch_inventory', 'activity_logs',
        ];

        foreach ($allTables as $row) {
            $table = array_values((array) $row)[0];
            if (in_array($table, $alreadyCovered)) continue;

            $hasBID = DB::select("SHOW COLUMNS FROM `{$table}` LIKE 'business_id'");
            if (empty($hasBID)) continue;

            Schema::table($table, function (Blueprint $t) use ($table) {
                try { $t->dropIndex($table . '_business_id_idx'); } catch (\Exception $e) {}
                $t->dropColumn('business_id');
            });
        }
    }
};
