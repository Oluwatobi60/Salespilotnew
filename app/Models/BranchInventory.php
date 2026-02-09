<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Branch\Branch;
use App\Models\User;
use App\Models\StandardItem;
use App\Models\ProductVariant;

class BranchInventory extends Model
{
    protected $table = 'branch_inventory';

    protected $fillable = [
        'branch_id',
        'business_name',
        'item_id',
        'item_type',
        'allocated_quantity',
        'current_quantity',
        'sold_quantity',
        'low_stock_threshold',
        'allocated_by',
        'allocated_at',
        'notes',
    ];

    protected $casts = [
        'allocated_quantity' => 'decimal:2',
        'current_quantity' => 'decimal:2',
        'sold_quantity' => 'decimal:2',
        'low_stock_threshold' => 'decimal:2',
        'allocated_at' => 'datetime',
    ];

    /**
     * Get the branch this inventory belongs to
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the user who allocated this inventory
     */
    public function allocatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'allocated_by');
    }

    /**
     * Get the item (polymorphic relationship)
     */
    public function item()
    {
        if ($this->item_type === 'standard') {
            return $this->belongsTo(StandardItem::class, 'item_id');
        }
        return $this->belongsTo(ProductVariant::class, 'item_id');
    }

    /**
     * Check if stock is low
     */
    public function isLowStock(): bool
    {
        return $this->low_stock_threshold !== null
            && $this->current_quantity <= $this->low_stock_threshold;
    }

    /**
     * Deduct stock when sale is made
     */
    public function deductStock(float $quantity): bool
    {
        if ($this->current_quantity >= $quantity) {
            $this->current_quantity -= $quantity;
            $this->sold_quantity += $quantity;
            $this->save();
            return true;
        }
        return false;
    }

    /**
     * Add stock (restocking/adjustment)
     */
    public function addStock(float $quantity): void
    {
        $this->allocated_quantity += $quantity;
        $this->current_quantity += $quantity;
        $this->save();
    }

    /**
     * Check if sufficient stock available
     */
    public function hasStock(float $quantity): bool
    {
        return $this->current_quantity >= $quantity;
    }
}
