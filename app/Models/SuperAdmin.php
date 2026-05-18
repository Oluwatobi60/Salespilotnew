<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\TrackLoginAttempts;

class SuperAdmin extends Authenticatable
{
    use Notifiable, TrackLoginAttempts;

    protected $table = 'superadmins';

    protected $guard = 'superadmin';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
