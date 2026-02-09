<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Branch\Branch;

class Staffs extends Authenticatable
{
    use Notifiable;

    protected $table = 'staffs';

    protected $fillable = [
        'staffsid',
        'business_name',
        'manager_name',
        'manager_email',
        'fullname',
        'email',
        'phone',
        'password',
        'role',
        'status',
        'address',
        'passport_photo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    /**
     * Get the branch assigned to this staff member.
     */
    public function branch()
    {
        return $this->hasOne(Branch::class, 'staff_id');
    }
}
