<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class UserSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'subscription_plan_id',
        'duration_months',
        'amount_paid',
        'discount_percentage',
        'start_date',
        'end_date',
        'status',
        'payment_reference',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the user that owns the subscription
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subscription plan
     */
    public function subscriptionPlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    /**
     * Check if subscription is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && $this->end_date >= Carbon::today();
    }

    /**
     * Check if subscription is expired
     */
    public function isExpired(): bool
    {
        return $this->end_date < Carbon::today();
    }

    /**
     * Get remaining days
     */
    public function remainingDays(): int
    {
        if ($this->isExpired()) {
            return 0;
        }
        return Carbon::today()->diffInDays($this->end_date);
    }

    /**
     * Scope for active subscriptions
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('end_date', '>=', Carbon::today());
    }

    /**
     * Scope for expired subscriptions
     */
    public function scopeExpired($query)
    {
        return $query->where('end_date', '<', Carbon::today())
                    ->orWhere('status', 'expired');
    }
}
