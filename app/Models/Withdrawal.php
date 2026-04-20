<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Withdrawal extends Model
{
    protected $fillable = [
        'brm_id',
        'brm_wallet_account_id',
        'amount',
        'status',
        'notes',
        'approved_at',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    /**
     * Get the BRM that made the withdrawal request
     */
    public function brm(): BelongsTo
    {
        return $this->belongsTo(Brm::class);
    }

    /**
     * Get the bank account for this withdrawal
     */
    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BrmWalletAccount::class, 'brm_wallet_account_id');
    }

    /**
     * Scope for pending withdrawals
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved withdrawals
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for paid withdrawals
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
     * Approve withdrawal request (deduct from wallet)
     */
    public function approve($notes = null)
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
            'notes' => $notes,
        ]);

        // Deduct from wallet balance
        if ($this->brm && $this->brm->wallet) {
            $this->brm->wallet->decrement('balance', $this->amount);
            $this->brm->wallet->increment('total_withdrawn', $this->amount);
        }
    }

    /**
     * Mark withdrawal as paid (after bank transfer complete)
     */
    public function markAsPaid($notes = null)
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
            'notes' => $notes,
        ]);
    }

    /**
     * Reject/cancel withdrawal (return amount to balance)
     */
    public function reject($notes = null)
    {
        // If already approved, return amount to wallet
        if ($this->status === 'approved') {
            $this->brm->wallet->increment('balance', $this->amount);
            $this->brm->wallet->decrement('total_withdrawn', $this->amount);
        }

        $this->update([
            'status' => 'rejected',
            'notes' => $notes,
        ]);
    }
}
