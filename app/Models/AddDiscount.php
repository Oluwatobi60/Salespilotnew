<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddDiscount extends Model
{
    protected $fillable = [
        'discount_name',
        'type',
        'customers_group',
        'discount_rate',
        'time_used',
    ];

    protected $casts = [
        'discount_rate' => 'decimal:2',
        'time_used' => 'integer',
    ];
}
