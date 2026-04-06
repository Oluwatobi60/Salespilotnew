

$managers = App\Models\User::whereNotNull('addby')->get();

foreach ($managers as $manager) {
    echo "\nManager: {$manager->email} (ID: {$manager->id})\n";
    $branches = App\Models\Branch\Branch::where('manager_id', $manager->id)->get();
    if ($branches->isEmpty()) {
        echo "  No branches assigned as manager.\n";
        continue;
    }
    foreach ($branches as $branch) {
        echo "  Branch: {$branch->branch_name} (ID: {$branch->id})\n";
        $allocs = App\Models\BranchInventory::where('branch_id', $branch->id)
            ->where('allocated_by', $manager->id)
            ->get();
        if ($allocs->isEmpty()) {
            echo "    No allocations by this manager for this branch.\n";
        } else {
            foreach ($allocs as $alloc) {
                echo "    Allocated item_id={$alloc->item_id} type={$alloc->item_type} qty={$alloc->allocated_quantity}\n";
            }
        }
    }
}
echo "\nDone.\n";
