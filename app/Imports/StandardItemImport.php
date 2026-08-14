<?php

namespace App\Imports;

use App\Models\StandardItem;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class StandardItemImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    public function model(array $row)
    {
        $manager  = Auth::user();
        $businessName = $manager->business_name;
        $managerName  = trim(($manager->firstname ?? '') . ' ' . ($manager->othername ?? '') . ' ' . ($manager->surname ?? '')) ?: ($manager->name ?? '');
        $managerEmail = $manager->email;

        // ── Category: find or create ──────────────────────────────────────────
        $categoryId = null;
        if (!empty($row['category'])) {
            $category = Category::firstOrCreate(
                [
                    'category_name' => trim($row['category']),
                    'business_name' => $businessName,
                ],
                [
                    'manager_name'  => $managerName,
                    'manager_email' => $managerEmail,
                ]
            );
            $categoryId = $category->id;
        }

        // ── Unit: find or create in units table ───────────────────────────────
        $unitValue = 'pcs';
        if (!empty($row['unit'])) {
            $unit = Unit::firstOrCreate(
                ['name' => trim($row['unit'])],
                [
                    'abbreviation'  => strtolower(trim($row['unit'])),
                    'is_custom'     => true,
                    'business_name' => $businessName,
                    'manager_name'  => $managerName,
                    'manager_email' => $managerEmail,
                ]
            );
            $unitValue = $unit->name;
        }

        // ── Supplier: find or create ──────────────────────────────────────────
        $supplierId = null;
        if (!empty($row['supplier_name'])) {
            $supplier = Supplier::firstOrCreate(
                [
                    'name'          => trim($row['supplier_name']),
                    'business_name' => $businessName,
                ],
                [
                    'manager_name'   => $managerName,
                    'manager_email'  => $managerEmail,
                    'contact_person' => trim($row['supplier_name']),
                    'email'          => !empty($row['supplier_email'])  ? trim($row['supplier_email'])  : null,
                    'phone'          => !empty($row['supplier_phone'])  ? trim($row['supplier_phone'])  : null,
                    'address'        => !empty($row['supplier_address'])? trim($row['supplier_address']): null,
                ]
            );
            $supplierId = $supplier->id;
        }

        // ── Product Image: download from URL and store in public disk ─────────
        $imagePath = null;
        if (!empty($row['product_image_url'])) {
            $imagePath = $this->downloadImage(trim($row['product_image_url']));
        }

        // ── Pricing ───────────────────────────────────────────────────────────
        $costPrice    = isset($row['cost_price'])    ? floatval($row['cost_price'])    : 0;
        $sellingPrice = isset($row['selling_price']) ? floatval($row['selling_price']) : 0;
        $profitMargin = ($costPrice > 0)
            ? (($sellingPrice - $costPrice) / $costPrice) * 100
            : 0;

        return new StandardItem([
            'business_name'      => $businessName,
            'manager_name'       => $managerName,
            'manager_email'      => $managerEmail,
            'item_name'          => trim($row['item_name']),
            'item_code'          => !empty($row['item_code']) ? trim($row['item_code']) : ('ITM-' . strtoupper(substr(trim($row['item_name']), 0, 3)) . '-' . time() . rand(10, 99)),
            'category'           => $categoryId,
            'unit'               => $unitValue,
            'supplier_id'        => $supplierId,
            'cost_price'         => $costPrice,
            'selling_price'      => $sellingPrice,
            'profit_margin'      => $profitMargin,
            'opening_stock'      => $row['opening_stock']      ?? 0,
            'current_stock'      => $row['current_stock']      ?? $row['opening_stock'] ?? 0,
            'low_stock_threshold'=> $row['low_stock_threshold'] ?? 10,
            'barcode'            => $row['barcode']             ?? null,
            'description'        => $row['description']         ?? null,
            'item_image'         => $imagePath,
            'enable_sale'        => true,
            'track_stock'        => true,
            'pricing_type'       => 'fixed',
        ]);
    }

    /**
     * Download an image from a URL and save it to the public storage disk.
     * Returns the stored path (relative to storage/app/public) or null on failure.
     */
    private function downloadImage(string $url): ?string
    {
        try {
            $response = Http::timeout(15)->get($url);

            if (!$response->successful()) {
                return null;
            }

            // Derive extension from Content-Type or URL
            $contentType = $response->header('Content-Type');
            $ext = match (true) {
                str_contains($contentType, 'jpeg'), str_contains($contentType, 'jpg') => 'jpg',
                str_contains($contentType, 'png')  => 'png',
                str_contains($contentType, 'webp') => 'webp',
                str_contains($contentType, 'gif')  => 'gif',
                default => pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg',
            };

            $filename = 'item_images/' . uniqid('import_', true) . '.' . $ext;
            Storage::disk('public')->put($filename, $response->body());

            return $filename;
        } catch (\Throwable $e) {
            Log::warning('StandardItemImport: could not download image from ' . $url . ' — ' . $e->getMessage());
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'item_name'     => 'required|string',
            'cost_price'    => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'opening_stock' => 'nullable|integer|min:0',
        ];
    }
}
