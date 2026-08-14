<?php

namespace App\Imports;

use App\Models\VariantItem;
use App\Models\ProductVariant;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class VariantItemImport implements ToCollection, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    public function collection(Collection $rows)
    {
        $manager      = Auth::user();
        $businessName = $manager->business_name;
        $managerName  = trim(($manager->firstname ?? '') . ' ' . ($manager->othername ?? '') . ' ' . ($manager->surname ?? '')) ?: ($manager->name ?? '');
        $managerEmail = $manager->email;

        // Group rows by parent item name so we create one VariantItem per parent
        $groupedVariants = $rows->groupBy('parent_item_name');

        foreach ($groupedVariants as $parentName => $variants) {
            if (empty($parentName)) {
                continue;
            }

            // Use the first row to set parent-level attributes
            $firstVariant = $variants->first();

            // ── Category: find or create ──────────────────────────────────────
            $categoryId = null;
            if (!empty($firstVariant['category'])) {
                $category = Category::firstOrCreate(
                    [
                        'category_name' => trim($firstVariant['category']),
                        'business_name' => $businessName,
                    ],
                    [
                        'manager_name'  => $managerName,
                        'manager_email' => $managerEmail,
                    ]
                );
                $categoryId = $category->id;
            }

            // ── Unit: find or create in units table ──────────────────────────
            $unitId = null;
            if (!empty($firstVariant['unit'])) {
                $unit = Unit::firstOrCreate(
                    ['name' => trim($firstVariant['unit'])],
                    [
                        'abbreviation'  => strtolower(trim($firstVariant['unit'])),
                        'is_custom'     => true,
                        'business_name' => $businessName,
                        'manager_name'  => $managerName,
                        'manager_email' => $managerEmail,
                    ]
                );
                $unitId = $unit->id;
            }

            // ── Supplier: find or create ──────────────────────────────────────
            $supplierId = null;
            if (!empty($firstVariant['supplier_name'])) {
                $supplier = Supplier::firstOrCreate(
                    [
                        'name'          => trim($firstVariant['supplier_name']),
                        'business_name' => $businessName,
                    ],
                    [
                        'manager_name'   => $managerName,
                        'manager_email'  => $managerEmail,
                        'contact_person' => trim($firstVariant['supplier_name']),
                        'email'   => !empty($firstVariant['supplier_email'])   ? trim($firstVariant['supplier_email'])   : null,
                        'phone'   => !empty($firstVariant['supplier_phone'])   ? trim($firstVariant['supplier_phone'])   : null,
                        'address' => !empty($firstVariant['supplier_address']) ? trim($firstVariant['supplier_address']) : null,
                    ]
                );
                $supplierId = $supplier->id;
            }

            // ── Parent Item Image: download from URL ──────────────────────────
            $imagePath = null;
            if (!empty($firstVariant['parent_item_image_url'])) {
                $imagePath = $this->downloadImage(trim($firstVariant['parent_item_image_url']));
            }

            // ── Create or update Parent VariantItem ───────────────────────────
            $variantItem = VariantItem::firstOrCreate(
                [
                    'item_name'     => $parentName,
                    'business_name' => $businessName,
                ],
                [
                    'manager_name'  => $managerName,
                    'manager_email' => $managerEmail,
                    'item_code'     => 'VAR-' . strtoupper(substr($parentName, 0, 3)) . '-' . time() . rand(10, 99),
                    'category'      => $categoryId,
                    'unit_id'       => $unitId,
                    'supplier_id'   => $supplierId,
                    'item_image'    => $imagePath,
                    'description'   => $firstVariant['description'] ?? null,
                    'variant_sets'  => [],
                ]
            );

            // ── Create child ProductVariants ──────────────────────────────────
            foreach ($variants as $row) {
                $costPrice    = isset($row['cost_price'])    ? floatval($row['cost_price'])    : 0;
                $sellingPrice = isset($row['selling_price']) ? floatval($row['selling_price']) : 0;
                $profitMargin = ($costPrice > 0)
                    ? (($sellingPrice - $costPrice) / $costPrice) * 100
                    : 0;

                $variantOptions = [];
                if (!empty($row['variant_options_json'])) {
                    $variantOptions = json_decode($row['variant_options_json'], true) ?? [];
                }

                ProductVariant::create([
                    'variant_item_id' => $variantItem->id,
                    'business_name'   => $businessName,
                    'manager_name'    => $managerName,
                    'manager_email'   => $managerEmail,
                    'variant_name'    => $row['variant_name'],
                    'sku'             => !empty($row['sku']) ? $row['sku'] : ('SKU-' . time() . rand(100, 999)),
                    'barcode'         => $row['barcode']     ?? null,
                    'variant_options' => $variantOptions,
                    'cost_price'      => $costPrice,
                    'selling_price'   => $sellingPrice,
                    'profit_margin'   => $profitMargin,
                    'opening_stock'   => $row['opening_stock']       ?? 0,
                    'current_stock'   => $row['current_stock']       ?? $row['opening_stock'] ?? 0,
                    'low_stock_threshold' => $row['low_stock_threshold'] ?? 10,
                    'sell_item'       => true,
                    'pricing_type'    => 'fixed',
                ]);
            }
        }
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
            Log::warning('VariantItemImport: could not download image from ' . $url . ' — ' . $e->getMessage());
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'parent_item_name' => 'required|string',
            'variant_name'     => 'required|string',
            'cost_price'       => 'nullable|numeric|min:0',
            'selling_price'    => 'nullable|numeric|min:0',
            'opening_stock'    => 'nullable|integer|min:0',
        ];
    }
}
