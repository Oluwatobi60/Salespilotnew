<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\UserSubscription;
use App\Models\Branch\Branch;

/**
 * @property int $id
 * @property string $email
 * @property string $business_name
 * @property string|null $addby
 * @method \Illuminate\Database\Eloquent\Relations\HasOne currentSubscription()
 * @method bool isBusinessCreator()
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'surname',
        'other_name',
        'business_name',
        'branch_name',
        'business_logo',
        'state',
        'local_govt',
        'addby',
        'address',
        'phone_number',
        'referral_code',
        'email',
        'password',
        'role',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function currentSubscription()
    {
        return $this->hasOne(UserSubscription::class)->where('status', 'active')->latest('end_date');
    }

    /**
     * Get the branch this user is assigned to (if any)
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    /**
     * Get the branch this user manages (where they are the branch manager)
     */
    public function managedBranch()
    {
        return $this->hasOne(Branch::class, 'manager_id');
    }

    /**
     * Check if this user is the business creator/owner
     * The creator is identified by addby being null or pointing to themselves
     */
    public function isBusinessCreator(): bool
    {
        return is_null($this->addby) || $this->addby === $this->email || $this->addby === $this->id;
    }
}
