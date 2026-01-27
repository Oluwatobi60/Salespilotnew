<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name',
        'monthly_price',
        'description',
        'features',
        'max_managers',
        'max_staff',
        'max_branches',
        'is_active',
        'is_popular',
        'trial_days',
    ];

    protected $casts = [
        'features' => 'array',
        'monthly_price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_popular' => 'boolean',
    ];

    /**
     * Get all user subscriptions for this plan
     */
    public function userSubscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }

    /**
     * Calculate discounted price based on duration
     */
    public function calculatePrice(int $months): array
    {
        $discounts = [
            1 => 0,
            3 => 0.05,
            6 => 0.10,
            12 => 0.15,
        ];

        $totalWithoutDiscount = $this->monthly_price * $months;
        $discount = $discounts[$months] ?? 0;
        $totalWithDiscount = $totalWithoutDiscount * (1 - $discount);

        return [
            'original_price' => $totalWithoutDiscount,
            'discounted_price' => $totalWithDiscount,
            'discount_percentage' => $discount * 100,
            'savings' => $totalWithoutDiscount - $totalWithDiscount,
        ];
    }

    /**
     * Scope for active plans
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for popular plans
     */
    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }
}
