<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\SubscriptionFeature;
use App\Models\SubscriptionPlan;

$features = SubscriptionFeature::getGroupedFeatures();
echo 'Grouped feature keys: ' . implode(', ', $features->keys()->toArray()) . "\n";
foreach ($features as $role => $group) {
    echo 'Role: ' . $role . ' count=' . $group->count() . "\n";
}
echo "---\n";
$plans = SubscriptionPlan::all();
foreach ($plans as $p) {
    echo $p->id . ' ' . $p->name . ' => ' . json_encode($p->features) . "\n";
}
