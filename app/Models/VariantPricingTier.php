<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductVariant;

class VariantPricingTier extends Model
{
    protected $fillable = [
        'product_variant_id',
        'min_quantity',
        'max_quantity',
        'price_per_unit',
    ];

    protected $casts = [
        'price_per_unit' => 'decimal:2',
    ];

    // Relationships
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
