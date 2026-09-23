<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillBusinessId extends Command
{
    protected $signature   = 'tenancy:backfill-business-id';
    protected $description = 'Backfill business_id on all tenant tables by joining on business_name to the owner user';

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
        'sales',
        'sell_products',
        'receipt_settings',
    ];

    public function handle(): int
    {
        $this->info('Backfilling business_id on tenant tables...');
        $this->newLine();

        foreach ($this->tables as $table) {
            // Skip tables that don't have a business_name column
            $cols = DB::select("SHOW COLUMNS FROM `{$table}` LIKE 'business_name'");
            if (empty($cols)) {
                $this->line("  <comment>SKIP</comment>  {$table} (no business_name column)");
                continue;
            }

            // Backfill: match on business_name to the business owner row in users
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
            $nulls  = $total - $filled;

            $status = $nulls === 0 ? '<info>OK</info>' : '<error>WARN</error>';
            $this->line("  {$status}  {$table}: {$filled}/{$total} filled" . ($nulls > 0 ? " ({$nulls} still NULL - no matching owner found)" : ''));
        }

        $this->newLine();
        $this->info('Done.');

        return self::SUCCESS;
    }
}
