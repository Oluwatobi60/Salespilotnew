<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddDiscount extends Model
{
    protected $fillable = [
        'business_name',
        'manager_name',
        'manager_email',
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
