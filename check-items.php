<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\StandardItem;
use App\Models\Staffs;
use App\Models\User;
use App\Models\Branch\Branch;
use App\Models\BranchInventory;

echo "\n=== Checking Items Data ===\n\n";

// Get a staff to test with
$staff = Staffs::first();
if (!$staff) {
    echo "No staff found!\n";
    exit;
}

echo "Staff: {$staff->fullname}\n";
echo "Business: {$staff->business_name}\n";
echo "Manager Email: {$staff->manager_email}\n";

$branch = $staff->branch;
if ($branch) {
    echo "Branch ID: {$branch->id}\n";
    echo "Branch Name: {$branch->branch_name}\n";

    if ($branch->manager_id) {
        $manager = User::find($branch->manager_id);
        if ($manager) {
            $managerName = trim(($manager->first_name ?? '') . ' ' . ($manager->other_name ?? '') . ' ' . ($manager->surname ?? ''));
            echo "Manager ID: {$manager->id}\n";
            echo "Manager Name: {$managerName}\n";
            echo "Manager Email: {$manager->email}\n";
        }
    }

    // Check branch inventory
    $branchInventory = BranchInventory::where('branch_id', $branch->id)
        ->where('business_name', $staff->business_name)
        ->get();
    echo "\nBranch Inventory Items: " . $branchInventory->count() . "\n";

    $standardBranchItemIds = [];
    foreach ($branchInventory as $inv) {
        echo "  - Item ID: {$inv->item_id}, Type: {$inv->item_type}\n";
        if ($inv->item_type === 'standard') {
            $standardBranchItemIds[] = $inv->item_id;
        }
    }
} else {
    echo "No branch found for this staff!\n";
}

// Check standard items
echo "\n=== Standard Items ===\n";
$standardItems = StandardItem::where('business_name', $staff->business_name)
    ->where('enable_sale', true)
    ->get();

echo "Total Standard Items: " . $standardItems->count() . "\n";
foreach ($standardItems->take(5) as $item) {
    echo "  - ID: {$item->id} - {$item->item_name}\n";
    echo "    Manager Name: " . ($item->manager_name ?: 'NULL') . "\n";
    echo "    Manager Email: " . ($item->manager_email ?: 'NULL') . "\n";
}

if ($branch && isset($manager)) {
    echo "\n=== Simulating Staff Filter (Manager OR Branch Inventory) ===\n";

    $filteredQuery = StandardItem::where('business_name', $staff->business_name)
        ->where('enable_sale', true)
        ->where(function($query) use ($managerName, $manager, $standardBranchItemIds) {
            $started = false;
            if ($managerName) {
                $query->where('manager_name', $managerName);
                $started = true;
            }
            if ($manager->email) {
                if ($started) {
                    $query->orWhere('manager_email', $manager->email);
                } else {
                    $query->where('manager_email', $manager->email);
                    $started = true;
                }
            }
            if (!empty($standardBranchItemIds)) {
                if ($started) {
                    $query->orWhereIn('id', $standardBranchItemIds);
                } else {
                    $query->whereIn('id', $standardBranchItemIds);
                }
            }
        });

    $filteredItems = $filteredQuery->get();
    echo "Items matching filter: " . $filteredItems->count() . "\n";
    foreach ($filteredItems as $item) {
        echo "  - ID: {$item->id} - {$item->item_name}\n";
    }
}

echo "\n=== Done ===\n";
