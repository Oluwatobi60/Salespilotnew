<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\BranchInventory;
use App\Models\Branch\Branch;
use App\Models\StandardItem;
use App\Models\VariantItem;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BranchInventoryController extends Controller
{
    /**
     * Display allocation interface for business creator
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Only business creator can access
        if (!$user->isBusinessCreator()) {
            return redirect()->route('manager.dashboard')
                ->with('error', 'Only business creator can manage inventory allocation');
        }

        $businessName = $user->business_name;

        // Get all active branches for this business with user_id only (exclude staff_id branches)
        $branches = Branch::where('business_name', $businessName)
            ->where('status', 1)
            ->whereNotNull('user_id')
            ->whereNull('staff_id')
            ->orderBy('branch_name')
            ->get();

        // Get all standard items
        $standardItems = StandardItem::where('business_name', $businessName)
            ->with('supplier')
            ->get();

        // Get all variant items with their variants
        $variantItems = VariantItem::where('business_name', $businessName)
            ->with(['variants' => function($query) {
                $query->where('sell_item', true);
            }, 'supplier', 'unit'])
            ->get();

        return view('manager.inventory.branch_allocation', compact(
            'branches',
            'standardItems',
            'variantItems'
        ));
    }

    /**
     * Get branch inventory for a specific branch
     */
    public function branchInventory($branchId)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $branch = Branch::where('id', $branchId)
            ->where('business_name', $user->business_name)
            ->firstOrFail();

        $inventory = BranchInventory::where('branch_id', $branchId)
            ->with('item')
            ->get()
            ->map(function($item) {
                $itemDetails = null;
                if ($item->item_type === 'standard') {
                    $itemDetails = StandardItem::find($item->item_id);
                } else {
                    $itemDetails = ProductVariant::find($item->item_id);
                }

                return [
                    'id' => $item->id,
                    'item_name' => $itemDetails->item_name ?? $itemDetails->variant_name ?? 'Unknown',
                    'item_type' => $item->item_type,
                    'allocated_quantity' => $item->allocated_quantity,
                    'current_quantity' => $item->current_quantity,
                    'sold_quantity' => $item->sold_quantity,
                    'low_stock' => $item->isLowStock(),
                    'allocated_at' => $item->allocated_at?->format('M d, Y'),
                ];
            });

        return response()->json([
            'success' => true,
            'branch' => $branch,
            'inventory' => $inventory
        ]);
    }

    /**
     * Allocate inventory to branches
     */
    public function allocate(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Only business creator can allocate
        if (!$user->isBusinessCreator()) {
            return response()->json([
                'success' => false,
                'message' => 'Only business creator can allocate inventory'
            ], 403);
        }

        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'item_id' => 'required|integer',
            'item_type' => 'required|in:standard,variant',
            'quantity' => 'required|numeric|min:0.01',
            'low_stock_threshold' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            // Verify branch belongs to this business
            $branch = Branch::where('id', $validated['branch_id'])
                ->where('business_name', $user->business_name)
                ->firstOrFail();

            // Verify item exists and belongs to this business
            if ($validated['item_type'] === 'standard') {
                $item = StandardItem::where('id', $validated['item_id'])
                    ->where('business_name', $user->business_name)
                    ->firstOrFail();

                // Check if sufficient stock available
                $unitName = $item->unit ?? '';
                if ($item->current_stock < $validated['quantity']) {
                    return response()->json([
                        'success' => false,
                        'message' => "Insufficient stock. Available: {$item->current_stock} {$unitName}"
                    ], 400);
                }

                // Deduct from main inventory
                $item->current_stock -= $validated['quantity'];
                $item->save();
            } else {
                $item = ProductVariant::where('id', $validated['item_id'])
                    ->whereHas('variantItem', function($query) use ($user) {
                        $query->where('business_name', $user->business_name);
                    })
                    ->firstOrFail();

                // Check if sufficient stock available
                if ($item->stock_quantity < $validated['quantity']) {
                    return response()->json([
                        'success' => false,
                        'message' => "Insufficient stock. Available: {$item->stock_quantity}"
                    ], 400);
                }

                // Deduct from main inventory
                $item->stock_quantity -= $validated['quantity'];
                $item->save();
            }

            // Check if allocation already exists
            $existingAllocation = BranchInventory::where('branch_id', $validated['branch_id'])
                ->where('item_id', $validated['item_id'])
                ->where('item_type', $validated['item_type'])
                ->first();

            if ($existingAllocation) {
                // Add to existing allocation
                $existingAllocation->addStock($validated['quantity']);

                if (isset($validated['low_stock_threshold'])) {
                    $existingAllocation->low_stock_threshold = $validated['low_stock_threshold'];
                }
                if (isset($validated['notes'])) {
                    $existingAllocation->notes = $validated['notes'];
                }
                $existingAllocation->save();
            } else {
                // Create new allocation
                BranchInventory::create([
                    'branch_id' => $validated['branch_id'],
                    'business_name' => $user->business_name,
                    'item_id' => $validated['item_id'],
                    'item_type' => $validated['item_type'],
                    'allocated_quantity' => $validated['quantity'],
                    'current_quantity' => $validated['quantity'],
                    'sold_quantity' => 0,
                    'low_stock_threshold' => $validated['low_stock_threshold'] ?? null,
                    'allocated_by' => $user->id,
                    'allocated_at' => now(),
                    'notes' => $validated['notes'] ?? null
                ]);
            }

            DB::commit();

            // Log activity
            \App\Helpers\ActivityLogger::log(
                'Inventory allocated to branch',
                json_encode([
                    'branch' => $branch->branch_name,
                    'item_type' => $validated['item_type'],
                    'item_id' => $validated['item_id'],
                    'quantity' => $validated['quantity']
                ])
            );

            return response()->json([
                'success' => true,
                'message' => 'Inventory allocated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to allocate inventory: ' . $e->getMessage()
            ], 500);
        }
    }
}
