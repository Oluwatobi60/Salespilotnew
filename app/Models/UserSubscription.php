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
        'auto_renew',
        'last_renewed_at',
        'renewal_notified_at',
        'cancellation_reason',
        'cancelled_at',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'auto_renew' => 'boolean',
        'last_renewed_at' => 'datetime',
        'renewal_notified_at' => 'datetime',
        'cancelled_at' => 'datetime',
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
     * Get the effective status for display and business logic.
     */
    public function effectiveStatus(): string
    {
        if ($this->status === 'cancelled') {
            return 'cancelled';
        }

        if ($this->status === 'expired') {
            return 'expired';
        }

        if ($this->end_date && $this->end_date->lt(Carbon::today())) {
            return 'expired';
        }

        if ($this->status === 'active' && $this->end_date >= Carbon::today()) {
            return 'active';
        }

        return $this->status ?? 'inactive';
    }

    /**
     * Check if subscription is active
     */
    public function isActive(): bool
    {
        return $this->effectiveStatus() === 'active';
    }

    /**
     * Check if subscription is expired
     */
    public function isExpired(): bool
    {
        return $this->effectiveStatus() === 'expired';
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
        return $query->where(function ($q) {
            $q->where('status', 'expired')
              ->orWhere(function ($subQ) {
                  $subQ->where('status', 'active')
                       ->where('end_date', '<', Carbon::today());
              });
        });
    }
}
