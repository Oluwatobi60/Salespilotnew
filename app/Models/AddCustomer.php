<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddCustomer extends Model
{
    protected $table = 'add_customers';

    protected $fillable = [
        'customer_name',
        'email',
        'phone_number',
        'address',
    ];
}
