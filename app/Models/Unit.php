<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = [
        'name',
        'abbreviation',
        'is_custom',
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
