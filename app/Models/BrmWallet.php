<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BrmWallet extends Model
{
    protected $fillable = [
        'brm_id',
        'balance',
        'total_earned',
        'total_withdrawn',
        'pending_approval',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'total_earned' => 'decimal:2',
        'total_withdrawn' => 'decimal:2',
        'pending_approval' => 'decimal:2',
    ];

    /**
     * Get the BRM that owns this wallet
     */
    public function brm(): BelongsTo
    {
        return $this->belongsTo(Brm::class);
    }

    /**
     * Get all bank accounts for this wallet
     */
    public function accounts(): HasMany
    {
        return $this->hasMany(BrmWalletAccount::class);
    }

    /**
     * Get primary account
     */
    public function primaryAccount()
    {
        return $this->accounts()->where('is_primary', true)->first();
    }

    /**
     * Add commission to balance
     */
    public function addCommission($amount)
    {
        $this->increment('balance', $amount);
        $this->increment('total_earned', $amount);
    }

    /**
     * Withdraw from wallet
     */
    public function withdraw($amount)
    {
        if ($this->balance >= $amount) {
            $this->decrement('balance', $amount);
            $this->increment('total_withdrawn', $amount);
            return true;
        }
        return false;
    }
}
