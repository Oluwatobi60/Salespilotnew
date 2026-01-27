<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            ['name' => 'Piece', 'abbreviation' => 'pcs', 'is_custom' => false],
            ['name' => 'Carton', 'abbreviation' => 'ct', 'is_custom' => false],
            ['name' => 'Centimeter', 'abbreviation' => 'cm', 'is_custom' => false],
            ['name' => 'Litre', 'abbreviation' => 'L', 'is_custom' => false],
            ['name' => 'Gram', 'abbreviation' => 'g', 'is_custom' => false],
            ['name' => 'Kilogram', 'abbreviation' => 'kg', 'is_custom' => false],
            ['name' => 'Per item', 'abbreviation' => 'pi', 'is_custom' => false],
            ['name' => 'Yard', 'abbreviation' => 'yd', 'is_custom' => false],
            ['name' => 'Metre', 'abbreviation' => 'm', 'is_custom' => false],
            ['name' => 'Millimetre', 'abbreviation' => 'mm', 'is_custom' => false],
        ];

        // Check if units already exist to prevent duplicates
        if (DB::table('units')->count() > 0) {
            $this->command->info('Units table already has data. Skipping seeder.');
            return;
        }

        foreach ($units as $unit) {
            DB::table('units')->insert(array_merge($unit, [
                'business_name' => null,
                'manager_name' => null,
                'manager_email' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $this->command->info('Units seeded successfully!');
    }
}
