<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VariantItemTemplateExport implements FromArray, WithHeadings, WithStyles
{
    public function array(): array
    {
        return [
            // Sample data row 1 — first variant of a parent
            [
                'Example T-Shirt',                  // Parent Item Name
                'Clothing',                         // Category (created if not found)
                'Pieces',                           // Unit
                'Red - Large',                      // Variant Name
                'TSHIRT-RED-L',                     // SKU
                '1000',                             // Cost Price
                '1500',                             // Selling Price
                '10',                               // Opening Stock
                '10',                               // Current Stock
                '5',                                // Low Stock Threshold
                '{"Color": "Red", "Size": "L"}',    // Variant Options JSON
                'Fashion Suppliers Ltd',             // Supplier Name (created if not found)
                'supplier@example.com',             // Supplier Email
                '08011223344',                      // Supplier Phone
                '5 Fashion Avenue, Lagos',          // Supplier Address
                'https://example.com/tshirt.jpg',  // Parent Item Image URL (optional)
                '1234567891',                       // Barcode
                'Example t-shirt description',      // Description
            ],
            // Sample data row 2 — second variant of the SAME parent
            [
                'Example T-Shirt',
                'Clothing',
                'Pieces',
                'Blue - Large',
                'TSHIRT-BLUE-L',
                '1000',
                '1500',
                '15',
                '15',
                '5',
                '{"Color": "Blue", "Size": "L"}',
                'Fashion Suppliers Ltd',
                'supplier@example.com',
                '08011223344',
                '5 Fashion Avenue, Lagos',
                'https://example.com/tshirt.jpg',
                '1234567892',
                'Example t-shirt description',
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'Parent Item Name',
            'Category',
            'Unit',
            'Variant Name',
            'SKU',
            'Cost Price',
            'Selling Price',
            'Opening Stock',
            'Current Stock',
            'Low Stock Threshold',
            'Variant Options (JSON)',
            'Supplier Name',
            'Supplier Email',
            'Supplier Phone',
            'Supplier Address',
            'Parent Item Image URL',
            'Barcode',
            'Description',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
