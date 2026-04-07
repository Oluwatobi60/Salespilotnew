<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brm extends Model
{
    protected $table = 'brms';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'region',
        'password',
        'notes',
        'status',
    ];

    protected $hidden = ['password'];

    protected $casts = ['password' => 'hashed'];

    public function customers(): HasMany
    {
        return $this->hasMany(User::class, 'brm_id');
    }
}
