<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BrmWalletAccount extends Model
{
    protected $fillable = [
        'brm_wallet_id',
        'brm_id',
        'account_number',
        'account_name',
        'bank_code',
        'bank_name',
        'is_primary',
        'is_verified',
        'verified_at',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    /**
     * Get the wallet this account belongs to
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(BrmWallet::class);
    }

    /**
     * Get the BRM that owns this account
     */
    public function brm(): BelongsTo
    {
        return $this->belongsTo(Brm::class);
    }

    /**
     * Set as primary account
     */
    public function setPrimary()
    {
        // Remove primary from other accounts
        $this->wallet->accounts()->update(['is_primary' => false]);

        // Set this as primary
        $this->update(['is_primary' => true]);
    }
}
