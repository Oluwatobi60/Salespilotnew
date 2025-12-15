<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staffs extends Model
{
    protected $fillable = [
        'fullname',
        'username',
        'email',
        'phone',
        'password',
        'role',
        'status',
        'address',
        'passport_photo',
    ];
}
