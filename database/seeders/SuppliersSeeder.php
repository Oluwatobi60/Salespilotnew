<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SuppliersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'ABC Wholesale Ltd',
                'contact_person' => 'John Smith',
                'email' => 'john@abcwholesale.com',
                'phone' => '+234 801 234 5678',
                'address' => '123 Market Street, Lagos, Nigeria',
            ],
            [
                'name' => 'Global Distributors Inc',
                'contact_person' => 'Sarah Johnson',
                'email' => 'sarah@globaldist.com',
                'phone' => '+234 802 345 6789',
                'address' => '45 Industrial Avenue, Abuja, Nigeria',
            ],
            [
                'name' => 'Prime Supplies Co',
                'contact_person' => 'Michael Brown',
                'email' => 'michael@primesupplies.com',
                'phone' => '+234 803 456 7890',
                'address' => '78 Commerce Road, Port Harcourt, Nigeria',
            ],
            [
                'name' => 'Metro Trading Company',
                'contact_person' => 'Grace Adeyemi',
                'email' => 'grace@metrotrading.ng',
                'phone' => '+234 804 567 8901',
                'address' => '12 Business District, Ibadan, Nigeria',
            ],
            [
                'name' => 'Elite Merchants Ltd',
                'contact_person' => 'David Okafor',
                'email' => 'david@elitemerchants.com',
                'phone' => '+234 805 678 9012',
                'address' => '34 Trade Center, Kano, Nigeria',
            ],
        ];

        // Check if suppliers already exist to prevent duplicates
        if (DB::table('suppliers')->count() > 0) {
            $this->command->info('Suppliers table already has data. Skipping seeder.');
            return;
        }

        foreach ($suppliers as $supplier) {
            DB::table('suppliers')->insert(array_merge($supplier, [
                'business_name' => null,
                'manager_name' => null,
                'manager_email' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $this->command->info('Suppliers seeded successfully!');
    }
}
