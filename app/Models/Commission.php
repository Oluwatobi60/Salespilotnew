<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Commission extends Model
{
    protected $fillable = [
        'brm_id',
        'user_id',
        'user_subscription_id',
        'subscription_amount',
        'commission_rate',
        'commission_amount',
        'status',
        'commission_type',
        'notes',
        'paid_at',
    ];

    protected $casts = [
        'subscription_amount' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    /**
     * Get the BRM (referrer) that earned this commission
     */
    public function brm(): BelongsTo
    {
        return $this->belongsTo(Brm::class);
    }

    /**
     * Get the customer (user) who made the payment
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subscription that generated this commission
     */
    public function userSubscription(): BelongsTo
    {
        return $this->belongsTo(UserSubscription::class);
    }

    /**
     * Scope for pending commissions
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved commissions
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for paid commissions
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope for a specific BRM
     */
    public function scopeForBrm($query, $brmId)
    {
        return $query->where('brm_id', $brmId);
    }

    /**
     * Scope for a specific commission type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('commission_type', $type);
    }

    /**
     * Mark commission as approved
     * Directly adds to wallet balance (skip pending_approval)
     */
    public function approve()
    {
        $this->update(['status' => 'approved']);
        
        // Add commission directly to BRM's wallet balance
        if ($this->brm && $this->brm->wallet) {
            $this->brm->wallet->increment('balance', $this->commission_amount);
            $this->brm->wallet->increment('total_earned', $this->commission_amount);
        }
    }

    /**
     * Mark commission as paid (no longer needed - kept for backwards compatibility)
     */
    public function markAsPaid()
    {
        $this->update(['status' => 'paid', 'paid_at' => now()]);
    }

    /**
     * Mark commission as rejected
     */
    public function reject()
    {
        $this->update(['status' => 'rejected']);
    }
}
