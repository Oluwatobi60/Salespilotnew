<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class CartItem extends Model
{
    protected $fillable = [
        'cart_name',
        'customer_id',
        'customer_name',
        'item_id',
        'item_type',
        'item_name',
        'item_price',
        'quantity',
        'note',
        'item_image',
        'subtotal',
        'discount',
        'total',
        'status',
        'session_id',
        'receipt_number',
        'user_id'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'item_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(AddCustomer::class, 'customer_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
