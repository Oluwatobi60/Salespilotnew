<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = [
        'name',
        'abbreviation',
        'is_custom',
        'business_name',
        'manager_name',
        'manager_email',
    ];

    protected $casts = [
        'is_custom' => 'boolean',
    ];

    // Accessor for 'symbol' (alias for abbreviation)
    public function getSymbolAttribute()
    {
        return $this->abbreviation;
    }
}
