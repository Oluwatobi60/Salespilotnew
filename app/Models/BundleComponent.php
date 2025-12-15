<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BundleComponent extends Model
{
    use HasFactory;

    protected $fillable = [
        'bundle_item_id',
        'product_id',
        'variant_id',
        'product_type',
        'quantity_in_bundle',
        'unit_cost',
        'subtotal',
    ];

    protected $casts = [
        'quantity_in_bundle' => 'integer',
        'unit_cost' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /**
     * Get the bundle this component belongs to
     */
    public function bundleItem()
    {
        return $this->belongsTo(BundleItem::class);
    }

    /**
     * Get the standard item (if product_type is 'standard')
     */
    public function standardItem()
    {
        return $this->belongsTo(StandardItem::class, 'product_id');
    }

    /**
     * Get the variant item (if product_type is 'variant')
     */
    public function variantItem()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    /**
     * Get the actual product (polymorphic)
     */
    public function getProduct()
    {
        if ($this->product_type === 'standard') {
            return $this->standardItem;
        } elseif ($this->product_type === 'variant') {
            return $this->variantItem;
        }
        return null;
    }

    /**
     * Get available stock for this component
     */
    public function getAvailableStock()
    {
        if ($this->product_type === 'standard' && $this->standardItem) {
            return $this->standardItem->stock_quantity ?? 0;
        } elseif ($this->product_type === 'variant' && $this->variantItem) {
            return $this->variantItem->stock_quantity ?? 0;
        }
        return 0;
    }

    /**
     * Deduct stock from the component product
     */
    public function deductComponentStock($bundleQuantity = 1)
    {
        $totalToDeduct = $this->quantity_in_bundle * $bundleQuantity;

        if ($this->product_type === 'standard' && $this->standardItem) {
            $this->standardItem->decrement('stock_quantity', $totalToDeduct);
        } elseif ($this->product_type === 'variant' && $this->variantItem) {
            $this->variantItem->decrement('stock_quantity', $totalToDeduct);
        }
    }

    /**
     * Get product name
     */
    public function getProductName()
    {
        if ($this->product_type === 'standard' && $this->standardItem) {
            return $this->standardItem->item_name;
        } elseif ($this->product_type === 'variant' && $this->variantItem) {
            return $this->variantItem->variant_name;
        }
        return 'Unknown Product';
    }
}
