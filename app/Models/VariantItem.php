<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VariantItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'item_name',
        'item_code',
        'barcode',
        'category',
        'supplier_id',
        'unit_id',
        'brand',
        'description',
        'item_image',
        'variant_sets',
    ];

    protected $casts = [
        'variant_sets' => 'array',
    ];

    // Relationships
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function categoryRelation()
    {
        return $this->belongsTo(Category::class, 'category', 'id');
    }

    // Accessor to get category name
    public function getCategoryNameAttribute()
    {
        if (is_numeric($this->category)) {
            $category = Category::find($this->category);
            return $category ? $category->category_name : $this->category;
        }
        return $this->category;
    }
}
