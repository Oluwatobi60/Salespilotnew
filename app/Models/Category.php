<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\VariantItem;
use App\Models\StandardItem;

class Category extends Model
{
    protected $fillable = [
        'category_name',
    ];

    public function variantitems()
    {
        return $this->hasMany(VariantItem::class);
    }

    public function standarditems()
    {
        return $this->hasMany(StandardItem::class);
    }







}
