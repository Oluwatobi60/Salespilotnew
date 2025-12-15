<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BundleItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'bundle_name',
        'bundle_code',
        'category',
        'supplier_id',
        'unit',
        'barcode',
        'description',
        'bundle_image',
        'total_item_cost',
        'assembly_fee',
        'total_bundle_cost',
        'bundle_selling_price',
        'individual_total',
        'customer_savings',
        'profit_margin',
        'bundle_profit',
        'tax_rate',
        'max_possible_bundles',
        'current_stock',
        'low_stock_threshold',
        'storage_location',
        'expiry_date',
    ];

    protected $casts = [
        'total_item_cost' => 'decimal:2',
        'assembly_fee' => 'decimal:2',
        'total_bundle_cost' => 'decimal:2',
        'bundle_selling_price' => 'decimal:2',
        'individual_total' => 'decimal:2',
        'customer_savings' => 'decimal:2',
        'profit_margin' => 'decimal:2',
        'bundle_profit' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'max_possible_bundles' => 'integer',
        'current_stock' => 'integer',
        'low_stock_threshold' => 'integer',
        'expiry_date' => 'date',
    ];

    /**
     * Get the supplier for this bundle
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get all components in this bundle
     */
    public function components()
    {
        return $this->hasMany(BundleComponent::class);
    }

    /**
     * Get standard items in this bundle through components
     */
    public function standardItems()
    {
        return $this->hasManyThrough(
            StandardItem::class,
            BundleComponent::class,
            'bundle_item_id',
            'id',
            'id',
            'product_id'
        )->where('bundle_components.product_type', 'standard');
    }

    /**
     * Get variant items in this bundle through components
     */
    public function variantItems()
    {
        return $this->hasManyThrough(
            ProductVariant::class,
            BundleComponent::class,
            'bundle_item_id',
            'id',
            'id',
            'variant_id'
        )->where('bundle_components.product_type', 'variant');
    }

    /**
     * Check if bundle has sufficient stock based on components
     */
    public function hasInsufficientStock()
    {
        foreach ($this->components as $component) {
            $availableStock = $component->getAvailableStock();
            $requiredStock = $component->quantity_in_bundle;

            if ($availableStock < $requiredStock) {
                return true;
            }
        }
        return false;
    }

    /**
     * Calculate maximum possible bundles based on component stock
     */
    public function calculateMaxPossibleBundles()
    {
        $maxBundles = PHP_INT_MAX;

        foreach ($this->components as $component) {
            $availableStock = $component->getAvailableStock();
            $requiredPerBundle = $component->quantity_in_bundle;

            if ($requiredPerBundle > 0) {
                $possibleFromThisItem = floor($availableStock / $requiredPerBundle);
                $maxBundles = min($maxBundles, $possibleFromThisItem);
            }
        }

        return $maxBundles === PHP_INT_MAX ? 0 : $maxBundles;
    }

    /**
     * Deduct stock when bundle is sold
     */
    public function deductStock($quantity = 1)
    {
        foreach ($this->components as $component) {
            $component->deductComponentStock($quantity);
        }

        // Update bundle stock
        $this->decrement('current_stock', $quantity);
    }

    /**
     * Check if stock is low
     */
    public function isLowStock()
    {
        if ($this->low_stock_threshold === null) {
            return false;
        }
        return $this->current_stock <= $this->low_stock_threshold;
    }
}
