<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'variant_item_id',
        'variant_name',
        'sku',
        'barcode',
        'variant_options',
        'primary_value',
        'secondary_value',
        'tertiary_value',
        'sell_item',
        'pricing_type',
        // Fixed Pricing
        'cost_price',
        'selling_price',
        'profit_margin',
        'potential_profit',
        // Manual Pricing
        'manual_cost_price',
        // Margin Pricing
        'margin_cost_price',
        'target_margin',
        'calculated_price',
        'margin_profit',
        // Range Pricing
        'range_cost_price',
        'min_price',
        'max_price',
        'range_potential_profit',
        // Additional Pricing
        'tax_rate',
        'discount',
        'final_price',
        // Stock Management
        'stock_quantity',
        'low_stock_threshold',
        'expiry_date',
        'location',
    ];

    protected $casts = [
        'variant_options' => 'array',
        'sell_item' => 'boolean',
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'profit_margin' => 'decimal:2',
        'potential_profit' => 'decimal:2',
        'manual_cost_price' => 'decimal:2',
        'margin_cost_price' => 'decimal:2',
        'target_margin' => 'decimal:2',
        'calculated_price' => 'decimal:2',
        'margin_profit' => 'decimal:2',
        'range_cost_price' => 'decimal:2',
        'min_price' => 'decimal:2',
        'max_price' => 'decimal:2',
        'range_potential_profit' => 'decimal:2',
        'tax_rate' => 'decimal:2',
      /* /* 'discount' => 'decimal:2', */
        'final_price' => 'decimal:2',
        'expiry_date' => 'date',
    ];

    // Relationships
    public function variantItem()
    {
        return $this->belongsTo(VariantItem::class);
    }

    public function pricingTiers()
    {
        return $this->hasMany(VariantPricingTier::class);
    }
}
