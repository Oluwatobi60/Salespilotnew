<?php

use App\Models\ProductVariant;
use App\Models\StandardItem;
use App\Models\User;
use App\Models\VariantItem;
use Illuminate\Support\Facades\Hash;

it('adds to current stock only while keeping opening stock unchanged for standard items', function () {
    $manager = User::create([
        'first_name' => 'Stock',
        'surname' => 'Manager',
        'email' => 'stock-manager-' . uniqid() . '@example.com',
        'password' => Hash::make('password'),
        'role' => 'manager',
        'business_name' => 'Demo Business',
        'addby' => null,
        'state' => 'Lagos',
        'local_govt' => 'Ikeja',
        'address' => 'Test address',
        'phone_number' => '08000000099',
    ]);

    $item = StandardItem::create([
        'business_name' => 'Demo Business',
        'manager_name' => 'Stock Manager',
        'manager_email' => $manager->email,
        'item_name' => 'Test Item',
        'item_code' => 'STD-TEST-' . uniqid(),
        'category' => 'General',
        'unit' => 'pcs',
        'cost_price' => 10,
        'selling_price' => 15,
        'opening_stock' => 10,
        'current_stock' => 10,
        'low_stock_threshold' => 2,
    ]);

    $this->actingAs($manager);

    $response = $this->put('/manager/all_items/update/standard/' . $item->id, [
        'item_name' => 'Test Item',
        'item_code' => $item->item_code,
        'category' => 'General',
        'supplier_id' => null,
        'unit' => 'pcs',
        'description' => 'Updated item',
        'cost_price' => 10,
        'selling_price' => 15,
        'add_stock' => 5,
        'low_stock_threshold' => 2,
    ]);

    $response->assertRedirect('/manager/all_items');

    $item->refresh();

    expect($item->current_stock)->toBe(15)
        ->and($item->opening_stock)->toBe(10)
        ->and($item->stock_added)->toBe(5);

    $secondResponse = $this->put('/manager/all_items/update/standard/' . $item->id, [
        'item_name' => 'Test Item',
        'item_code' => $item->item_code,
        'category' => 'General',
        'supplier_id' => null,
        'unit' => 'pcs',
        'description' => 'Updated item',
        'cost_price' => 10,
        'selling_price' => 15,
        'add_stock' => 7,
        'low_stock_threshold' => 2,
    ]);

    $secondResponse->assertRedirect('/manager/all_items');

    $item->refresh();

    expect($item->current_stock)->toBe(22)
        ->and($item->opening_stock)->toBe(10)
        ->and($item->stock_added)->toBe(7);
});

it('adds to current stock only while keeping opening stock unchanged for product variants', function () {
    $manager = User::create([
        'first_name' => 'Variant',
        'surname' => 'Manager',
        'email' => 'variant-manager-' . uniqid() . '@example.com',
        'password' => Hash::make('password'),
        'role' => 'manager',
        'business_name' => 'Demo Business',
        'addby' => null,
        'state' => 'Lagos',
        'local_govt' => 'Ikeja',
        'address' => 'Test address',
        'phone_number' => '08000000100',
    ]);

    $variantItem = VariantItem::create([
        'business_name' => 'Demo Business',
        'manager_name' => 'Variant Manager',
        'manager_email' => $manager->email,
        'item_name' => 'Variant Parent',
        'item_code' => 'VAR-PARENT-' . uniqid(),
        'category' => 'General',
        'unit_id' => null,
        'brand' => 'Test Brand',
        'description' => 'Parent variant item',
    ]);

    $variant = ProductVariant::create([
        'variant_item_id' => $variantItem->id,
        'business_name' => 'Demo Business',
        'manager_name' => 'Variant Manager',
        'manager_email' => $manager->email,
        'variant_name' => 'Red',
        'sku' => 'VAR-RED-' . uniqid(),
        'barcode' => 'VAR-RED-' . uniqid(),
        'cost_price' => 10,
        'selling_price' => 15,
        'opening_stock' => 10,
        'current_stock' => 10,
        'low_stock_threshold' => 2,
    ]);

    $this->actingAs($manager);

    $response = $this->put('/manager/all_items/update/product_variant/' . $variant->id, [
        'variant_name' => 'Red',
        'sku' => $variant->sku,
        'barcode' => $variant->barcode,
        'cost_price' => 10,
        'selling_price' => 15,
        'add_stock' => 5,
        'low_stock_threshold' => 2,
        'variant_options' => null,
    ]);

    $response->assertRedirect('/manager/all_items');

    $variant->refresh();

    expect($variant->current_stock)->toBe(15)
        ->and($variant->opening_stock)->toBe(10)
        ->and($variant->stock_added)->toBe(5);

    $secondResponse = $this->put('/manager/all_items/update/product_variant/' . $variant->id, [
        'variant_name' => 'Red',
        'sku' => $variant->sku,
        'barcode' => $variant->barcode,
        'cost_price' => 10,
        'selling_price' => 15,
        'add_stock' => 7,
        'low_stock_threshold' => 2,
        'variant_options' => null,
    ]);

    $secondResponse->assertRedirect('/manager/all_items');

    $variant->refresh();

    expect($variant->current_stock)->toBe(22)
        ->and($variant->opening_stock)->toBe(10)
        ->and($variant->stock_added)->toBe(7);
});
