<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingTier extends Model
{
    protected $fillable = [
        'standard_item_id',
        'min_quantity',
        'max_quantity',
        'price_per_unit',
    ];

    protected $casts = [
        'price_per_unit' => 'decimal:2',
        'min_quantity' => 'integer',
        'max_quantity' => 'integer',
    ];

    public function standardItem()
    {
        return $this->belongsTo(StandardItem::class);
    }
}
