<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StandardItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'item_name',
        'item_code',
        'category',
        'supplier_id',
        'unit',
        'barcode',
        'description',
        'item_image',
        'enable_sale',
        'pricing_type',
        'cost_price',
        'selling_price',
        'profit_margin',
        'potential_profit',
        'target_margin',
        'calculated_price',
        'margin_profit',
        'min_price',
        'max_price',
        'range_potential_profit',
        'tax_rate',
        'discount',
        'final_price',
        'track_stock',
        'opening_stock',
        'current_stock',
        'low_stock_threshold',
        'expiry_date',
        'location',
    ];

    protected $casts = [
        'enable_sale' => 'boolean',
        'track_stock' => 'boolean',
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'profit_margin' => 'decimal:2',
        'potential_profit' => 'decimal:2',
        'target_margin' => 'decimal:2',
        'calculated_price' => 'decimal:2',
        'margin_profit' => 'decimal:2',
        'min_price' => 'decimal:2',
        'max_price' => 'decimal:2',
        'range_potential_profit' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        /* 'discount' => 'decimal:2', */
        'final_price' => 'decimal:2',
        'expiry_date' => 'date',
        'opening_stock' => 'integer',
        'current_stock' => 'integer',
        'low_stock_threshold' => 'integer',
    ];

    // Relationships
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function pricingTiers()
    {
        return $this->hasMany(PricingTier::class);
    }

    public function categoryRelation()
    {
        return $this->belongsTo(Category::class, 'category', 'id');
    }

    // Accessor to get category name
    public function getCategoryNameAttribute()
    {
        if (is_numeric($this->category)) {
            $category = Category::find($this->category);
            return $category ? $category->category_name : $this->category;
        }
        return $this->category;
    }
}

