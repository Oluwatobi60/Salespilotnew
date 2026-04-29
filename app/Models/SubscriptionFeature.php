<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionFeature extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'category',
        'role',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get features grouped by role (business_creator, manager, staff, branch)
     */
    public static function getGroupedFeatures()
    {
        return self::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->groupBy('role');
    }

    /**
     * Get features grouped by category (for reference)
     */
    public static function getGroupedByCategory()
    {
        return self::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->groupBy('category');
    }

    /**
     * Get all roles with labels and descriptions
     */
    public static function getRoles()
    {
        return [
            [
                'slug' => 'business_creator',
                'label' => 'Business Creator / Super Admin',
                'description' => 'Full system access and business management',
                'color' => 'danger',
                'icon' => 'shield-fill-check'
            ],
            [
                'slug' => 'manager',
                'label' => 'Manager',
                'description' => 'Branch management and operations',
                'color' => 'primary',
                'icon' => 'person-badge'
            ],
            [
                'slug' => 'staff',
                'label' => 'Staff',
                'description' => 'Daily operations and sales',
                'color' => 'success',
                'icon' => 'person'
            ],
            [
                'slug' => 'branch',
                'label' => 'Branch',
                'description' => 'Branch-specific features',
                'color' => 'warning',
                'icon' => 'building'
            ],
        ];
    }
}
