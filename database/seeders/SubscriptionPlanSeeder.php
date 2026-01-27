<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'free',
                'monthly_price' => 0,
                'description' => 'Free 7-day trial to test all features',
                'features' => json_encode([
                    '1 Manager/Administrator Account',
                    '1 Staff Account',
                    'Basic Inventory Management',
                    'Sales Tracking',
                    'Email Support'
                ]),
                'max_managers' => 1,
                'max_staff' => 1,
                'max_branches' => 1,
                'is_active' => true,
                'is_popular' => false,
                'trial_days' => 7,
            ],
            [
                'name' => 'basic',
                'monthly_price' => 5000,
                'description' => 'Perfect for small businesses',
                'features' => json_encode([
                    '1 Manager/Administrator Account',
                    '2 Staff Accounts',
                    'Advanced Inventory Management',
                    'Sales & Purchase Tracking',
                    'Basic Reports & Analytics',
                    'Priority Email Support'
                ]),
                'max_managers' => 1,
                'max_staff' => 2,
                'max_branches' => 1,
                'is_active' => true,
                'is_popular' => true,
                'trial_days' => 0,
            ],
            [
                'name' => 'standard',
                'monthly_price' => 10000,
                'description' => 'Ideal for growing businesses',
                'features' => json_encode([
                    '2 Manager/Administrator Accounts',
                    'Up to 4 Staff Accounts',
                    'Allows 2 branches',
                    'Advanced Inventory Management',
                    'Sales & Purchase Tracking',
                    'Basic Reports & Analytics',
                    'Priority Email Support'
                ]),
                'max_managers' => 2,
                'max_staff' => 4,
                'max_branches' => 2,
                'is_active' => true,
                'is_popular' => true,
                'trial_days' => 0,
            ],
            [
                'name' => 'premium',
                'monthly_price' => 20000,
                'description' => 'Complete solution for large businesses',
                'features' => json_encode([
                    '3 Manager/Administrator Accounts',
                    'Unlimited Staff Accounts',
                    'Full Inventory Management',
                    'Advanced Reports & Analytics',
                    'Multi-branch Support',
                    '24/7 Priority Support',
                    'Custom Integrations'
                ]),
                'max_managers' => 3,
                'max_staff' => null, // unlimited
                'max_branches' => null, // unlimited
                'is_active' => true,
                'is_popular' => false,
                'trial_days' => 0,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(
                ['name' => $plan['name']],
                $plan
            );
        }
    }
}
