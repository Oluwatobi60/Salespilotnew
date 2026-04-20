<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticableTrait;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property string $address
 * @property string $region
 * @property string $referral_code
 * @property string $password
 * @property string $notes
 * @property int $status
 * @method HasMany customers()
 */
class Brm extends Model implements Authenticatable
{
    use AuthenticableTrait;

    protected $table = 'brms';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'region',
        'referral_code',
        'password',
        'notes',
        'status',
    ];

    protected $hidden = ['password'];

    protected $casts = ['password' => 'hashed'];

    public function customers(): HasMany
    {
        return $this->hasMany(User::class, 'brm_id');
    }

    /**
     * Get all commissions for this BRM
     */
    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class);
    }

    /**
     * Get all withdrawals for this BRM
     */
    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class);
    }

    /**
     * Get the wallet for this BRM
     */
    public function wallet(): HasOne
    {
        return $this->hasOne(BrmWallet::class);
    }
}
