<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceiptSetting extends Model
{
    protected $fillable = [
        'business_name',
        'receipt_title',
        'header_text',
        'footer_text',
        'paper_size',
        'font_size',
        'show_invoice_number',
        'show_date',
        'show_cashier',
        'show_logo',
        'show_barcode',
        'show_tax_details',
        'show_item_codes',
        'show_discounts',
    ];

    protected $casts = [
        'show_invoice_number' => 'boolean',
        'show_date' => 'boolean',
        'show_cashier' => 'boolean',
        'show_logo' => 'boolean',
        'show_barcode' => 'boolean',
        'show_tax_details' => 'boolean',
        'show_item_codes' => 'boolean',
        'show_discounts' => 'boolean',
    ];

    /**
     * Get receipt settings for a business
     */
    public static function getForBusiness($businessName)
    {
        return static::firstOrCreate(
            ['business_name' => $businessName],
            [
                'receipt_title' => 'SALES RECEIPT',
                'header_text' => 'Thank you for shopping with us!',
                'footer_text' => 'Visit us again soon!',
                'paper_size' => '80mm Thermal',
                'font_size' => 'Medium',
            ]
        );
    }
}
