<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\StandardItem;
use App\Models\BranchInventory;
use App\Models\Branch\Branch;

echo "\n=== Allocating Items to Branch ===\n\n";

// Get the branch
$branchId = 4; // Iwo - Road branch
$branch = Branch::find($branchId);

if (!$branch) {
    echo "Branch not found!\n";
    exit;
}

echo "Branch: {$branch->branch_name}\n";
echo "Business: {$branch->business_name}\n\n";

// Get items to allocate (items 1, 2, 3 - the ones by Timothy)
$itemsToAllocate = [1, 2, 3];

foreach ($itemsToAllocate as $itemId) {
    $item = StandardItem::find($itemId);
    
    if (!$item) {
        echo "Item ID {$itemId} not found - skipping\n";
        continue;
    }
    
    // Check if already allocated
    $existing = BranchInventory::where('branch_id', $branchId)
        ->where('item_id', $itemId)
        ->where('item_type', 'standard')
        ->first();
    
    if ($existing) {
        echo "Item '{$item->item_name}' already allocated to this branch - skipping\n";
        continue;
    }
    
    // Create allocation
    $allocation = BranchInventory::create([
        'branch_id' => $branchId,
        'business_name' => $branch->business_name,
        'item_id' => $itemId,
        'item_type' => 'standard',
        'allocated_quantity' => $item->current_stock ?? 0,
        'current_quantity' => $item->current_stock ?? 0,
    ]);
    
    echo "✓ Allocated '{$item->item_name}' (ID: {$itemId}) to branch\n";
}

echo "\n=== Done ===\n";
echo "\nNow staff at this branch should see:\n";
echo "1. Items added by their branch manager (manager_email match)\n";
echo "2. Items allocated to their branch (branch_inventory)\n";
