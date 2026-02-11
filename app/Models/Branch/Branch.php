<?php

namespace App\Models\Branch;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use App\Models\Staffs;

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
     * Get the staff assigned to this branch (single - legacy)
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staffs::class, 'staff_id');
    }

    /**
     * Get all staff members assigned to this branch (many-to-many)
     */
    public function staffMembers(): BelongsToMany
    {
        return $this->belongsToMany(Staffs::class, 'branch_staff', 'branch_id', 'staff_id')
            ->withTimestamps();
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
