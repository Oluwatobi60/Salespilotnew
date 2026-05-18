<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SubscriptionPlan;

class FixSubscriptionFeatures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:fix-features';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix subscription plan features column to ensure proper JSON array format';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fixing subscription plan features...');

        $plans = SubscriptionPlan::all();
        $fixed = 0;

        foreach ($plans as $plan) {
            $originalFeatures = $plan->getAttributes()['features'] ?? null;

            // If features is null, set it to empty array
            if ($originalFeatures === null) {
                $plan->features = [];
                $plan->save();
                $fixed++;
                $this->info("Fixed null features for plan: {$plan->name}");
                continue;
            }

            // If features is a string, try to decode it
            if (is_string($originalFeatures)) {
                $decoded = json_decode($originalFeatures, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    // Invalid JSON, set to empty array
                    $plan->features = [];
                    $plan->save();
                    $fixed++;
                    $this->warn("Fixed invalid JSON for plan: {$plan->name}");
                } else if (!is_array($decoded)) {
                    // Valid JSON but not an array
                    $plan->features = [];
                    $plan->save();
                    $fixed++;
                    $this->warn("Fixed non-array JSON for plan: {$plan->name}");
                } else {
                    // Valid JSON array, re-save to ensure proper format
                    $plan->features = $decoded;
                    $plan->save();
                    $this->info("Validated features for plan: {$plan->name}");
                }
            } else if (is_array($originalFeatures)) {
                // Already an array, just ensure it's saved properly
                $plan->features = $originalFeatures;
                $plan->save();
                $this->info("Validated features for plan: {$plan->name}");
            }
        }

        $this->info("✅ Fixed {$fixed} subscription plans with invalid features");
        $this->info("Total plans processed: {$plans->count()}");

        return Command::SUCCESS;
    }
}
