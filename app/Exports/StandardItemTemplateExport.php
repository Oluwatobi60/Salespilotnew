<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StandardItemTemplateExport implements FromArray, WithHeadings, WithStyles
{
    public function array(): array
    {
        return [
            // Sample data row
            [
                'POWERADD PRO Power Bank',  // Item Name
                'ITM-001',                   // Item Code
                'Electronics',              // Category (will be created if not found)
                'Pieces',                   // Unit
                '35000',                    // Cost Price
                '45000',                    // Selling Price
                '40',                       // Opening Stock
                '40',                       // Current Stock
                '10',                       // Low Stock Threshold
                'ABC Supplies Ltd',         // Supplier Name (will be created if not found)
                'supplier@example.com',     // Supplier Email
                '08012345678',              // Supplier Phone
                '10 Supplier Street, Lagos',// Supplier Address
                'https://example.com/image.jpg', // Product Image URL (optional)
                '1234567890',               // Barcode
                'Example standard item description.',  // Description
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'Item Name',
            'Item Code',
            'Category',
            'Unit',
            'Cost Price',
            'Selling Price',
            'Opening Stock',
            'Current Stock',
            'Low Stock Threshold',
            'Supplier Name',
            'Supplier Email',
            'Supplier Phone',
            'Supplier Address',
            'Product Image URL',
            'Barcode',
            'Description',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Bold the heading row
            1 => ['font' => ['bold' => true]],
        ];
    }
}
