<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            ['name' => 'Piece', 'abbreviation' => 'pcs', 'is_custom' => false],
            ['name' => 'Kilogram', 'abbreviation' => 'kg', 'is_custom' => false],
            ['name' => 'Gram', 'abbreviation' => 'g', 'is_custom' => false],
            ['name' => 'Liter', 'abbreviation' => 'L', 'is_custom' => false],
            ['name' => 'Milliliter', 'abbreviation' => 'ml', 'is_custom' => false],
            ['name' => 'Meter', 'abbreviation' => 'm', 'is_custom' => false],
            ['name' => 'Centimeter', 'abbreviation' => 'cm', 'is_custom' => false],
            ['name' => 'Box', 'abbreviation' => 'box', 'is_custom' => false],
            ['name' => 'Carton', 'abbreviation' => 'ctn', 'is_custom' => false],
            ['name' => 'Dozen', 'abbreviation' => 'doz', 'is_custom' => false],
            ['name' => 'Pack', 'abbreviation' => 'pack', 'is_custom' => false],
            ['name' => 'Bottle', 'abbreviation' => 'btl', 'is_custom' => false],
            ['name' => 'Can', 'abbreviation' => 'can', 'is_custom' => false],
            ['name' => 'Bag', 'abbreviation' => 'bag', 'is_custom' => false],
            ['name' => 'Roll', 'abbreviation' => 'roll', 'is_custom' => false],
            ['name' => 'Set', 'abbreviation' => 'set', 'is_custom' => false],
            ['name' => 'Pair', 'abbreviation' => 'pair', 'is_custom' => false],
            ['name' => 'Unit', 'abbreviation' => 'unit', 'is_custom' => false],
        ];

        foreach ($units as $unit) {
            Unit::firstOrCreate(
                ['abbreviation' => $unit['abbreviation']],
                $unit
            );
        }
    }
}
