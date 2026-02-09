<?php

namespace App\Models\Branch;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;

class Branch extends Model
{
    protected $fillable = [
        'user_id',
        'staff_id',
        'business_name',
        'branch_name',
        'address',
        'state',
        'local_govt',
        'manager_id',
        'subscription_plan_id',
        'user_subscription_id',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    /**
     * Get the user that owns this branch
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the manager assigned to this branch
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Get the subscription plan for this branch
     */
    public function subscriptionPlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    /**
     * Get the user subscription for this branch
     */
    public function userSubscription(): BelongsTo
    {
        return $this->belongsTo(UserSubscription::class);
    }

    /**
     * Scope for active branches
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope for inactive branches
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 0);
    }
}
