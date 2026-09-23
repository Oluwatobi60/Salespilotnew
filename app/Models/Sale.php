<?php

namespace App\Models;

use App\Models\Branch\Branch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    protected $fillable = [
        'receipt_number',
        'session_id',
        'business_name',
        'business_id',
        'user_id',
        'staff_id',
        'branch_id',
        'branch_name',
        'customer_id',
        'customer_name',
        'subtotal',
        'discount_total',
        'tax_total',
        'grand_total',
        'payment_method',
        'status',
        'items_count',
        'notes',
        'completed_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'items_count' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the line items associated with this sale receipt.
     */
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class, 'receipt_number', 'receipt_number');
    }

    /**
     * Get the customer for this sale.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(AddCustomer::class, 'customer_id');
    }

    /**
     * Get the user (business manager / creator) who recorded this sale.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the staff (cashier) who processed this sale.
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staffs::class, 'staff_id');
    }

    /**
     * Get the branch location where this sale occurred.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    /**
     * Scope for completed sales.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for a specific business.
     */
    public function scopeForBusiness($query, string $businessName)
    {
        return $query->where('business_name', $businessName);
    }
}
