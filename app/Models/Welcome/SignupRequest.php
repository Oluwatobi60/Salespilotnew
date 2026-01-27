<?php

namespace App\Models\Welcome;

use Illuminate\Database\Eloquent\Model;

class SignupRequest extends Model
{
    protected $fillable = [
        'email',
        'token',
        'token_expires_at',
        'is_used',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'token_expires_at' => 'datetime',
    ];
}
