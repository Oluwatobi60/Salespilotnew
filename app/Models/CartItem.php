<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AddCustomer;
use App\Models\AddDiscount;
use App\Models\User;
use App\Models\Staffs;


class CartItem extends Model
{
    protected $fillable = [
        'business_name',
        'manager_name',
        'manager_email',
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
        'discount_id',
        'total',
        'status',
        'session_id',
        'receipt_number',
        'user_id',
        'staff_id',
        'branch_id',
        'branch_name',
        'branch_manager_id'
    ];
    public function discount()
    {
        return $this->belongsTo(AddDiscount::class, 'discount_id');
    }

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

    public function staff()
    {
        return $this->belongsTo(Staffs::class, 'staff_id');
    }

    public function branch()
    {
        return $this->belongsTo(\App\Models\Branch\Branch::class);
    }
}
