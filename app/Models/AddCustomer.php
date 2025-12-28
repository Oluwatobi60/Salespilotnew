<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Staffs;
use App\Models\User;

class AddCustomer extends Model
{
    protected $table = 'add_customers';

    protected $fillable = [
        'customer_name',
        'email',
        'phone_number',
        'address',
      /*   'staff_id',
        'user_id', */
    ];

    // Relationship to user
   /*  public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    } */

    // Relationship to staff
    /* public function staff()
    {
        return $this->belongsTo(Staffs::class, 'staff_id');
    } */
}
