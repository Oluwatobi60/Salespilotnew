<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Supplier extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'contact_person',
        'email',
        'phone',
        'address',
    ];

    public function standardItems()
    {
        return $this->hasMany(StandardItem::class);
    }

    public function variantItems()
    {
        return $this->hasMany(VariantItem::class);
    }
}
