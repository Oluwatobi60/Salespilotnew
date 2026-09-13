<?php

use App\Models\Branch\Branch;
use App\Models\BranchInventory;
use App\Models\CartItem;
use App\Models\Sale;
use App\Models\StandardItem;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

it('allows distinct businesses to create items with identical item codes and SKUs', function () {
    $businessA = 'Business Alpha';
    $businessB = 'Business Beta';

    $itemA = StandardItem::create([
        'business_name' => $businessA,
        'manager_name' => 'Manager A',
        'manager_email' => 'a@example.com',
        'item_name' => 'Standard Pen',
        'item_code' => 'SKU-PEN-001',
        'category' => 'Stationery',
        'unit' => 'pcs',
        'cost_price' => 50,
        'selling_price' => 100,
        'opening_stock' => 100,
        'current_stock' => 100,
    ]);

    $itemB = StandardItem::create([
        'business_name' => $businessB,
        'manager_name' => 'Manager B',
        'manager_email' => 'b@example.com',
        'item_name' => 'Standard Pen (Beta)',
        'item_code' => 'SKU-PEN-001', // Same item_code across different business
        'category' => 'Stationery',
        'unit' => 'pcs',
        'cost_price' => 60,
        'selling_price' => 120,
        'opening_stock' => 50,
        'current_stock' => 50,
    ]);

    expect($itemA->id)->not->toBeNull()
        ->and($itemB->id)->not->toBeNull()
        ->and($itemA->item_code)->toBe($itemB->item_code)
        ->and($itemA->business_name)->not->toBe($itemB->business_name);
});

it('supports fractional decimal quantities across standard items and cart items', function () {
    $item = StandardItem::create([
        'business_name' => 'Butcher Shop',
        'manager_name' => 'Meat Manager',
        'manager_email' => 'meat@example.com',
        'item_name' => 'Beef Steak',
        'item_code' => 'BEEF-'.uniqid(),
        'category' => 'Meat',
        'unit' => 'kg',
        'cost_price' => 2000,
        'selling_price' => 3500,
        'opening_stock' => 25.50,
        'current_stock' => 25.50,
        'low_stock_threshold' => 5.25,
    ]);

    $item->refresh();

    expect($item->current_stock)->toEqual(25.50)
        ->and($item->opening_stock)->toEqual(25.50)
        ->and($item->low_stock_threshold)->toEqual(5.25);

    $cartItem = CartItem::create([
        'business_name' => 'Butcher Shop',
        'manager_name' => 'Meat Manager',
        'manager_email' => 'meat@example.com',
        'customer_name' => 'John Doe',
        'item_id' => $item->id,
        'item_name' => $item->item_name,
        'item_price' => 3500,
        'quantity' => 1.75, // Fractional 1.75 kg
        'subtotal' => 6125.00,
        'discount' => 0,
        'total' => 6125.00,
        'status' => 'completed',
        'receipt_number' => 'RCPT-TEST-1234',
    ]);

    $cartItem->refresh();

    expect($cartItem->quantity)->toEqual(1.75)
        ->and($cartItem->total)->toEqual(6125.00);
});

it('creates a normalized sale header and links to cart items', function () {
    $receipt = 'RCPT-'.strtoupper(Str::random(8));
    $sessionId = Str::uuid()->toString();

    $cartItem1 = CartItem::create([
        'business_name' => 'Test Mart',
        'item_id' => 1,
        'item_name' => 'Apple',
        'item_price' => 100,
        'quantity' => 2,
        'subtotal' => 200,
        'discount' => 0,
        'total' => 200,
        'status' => 'completed',
        'session_id' => $sessionId,
        'receipt_number' => $receipt,
    ]);

    $cartItem2 = CartItem::create([
        'business_name' => 'Test Mart',
        'item_id' => 2,
        'item_name' => 'Orange',
        'item_price' => 150,
        'quantity' => 1,
        'subtotal' => 150,
        'discount' => 10,
        'total' => 140,
        'status' => 'completed',
        'session_id' => $sessionId,
        'receipt_number' => $receipt,
    ]);

    $sale = Sale::create([
        'receipt_number' => $receipt,
        'session_id' => $sessionId,
        'business_name' => 'Test Mart',
        'customer_name' => 'Jane Smith',
        'subtotal' => 350.00,
        'discount_total' => 10.00,
        'tax_total' => 0.00,
        'grand_total' => 340.00,
        'payment_method' => 'card',
        'status' => 'completed',
        'items_count' => 2,
        'completed_at' => now(),
    ]);

    expect($sale->items)->toHaveCount(2)
        ->and($cartItem1->sale->receipt_number)->toBe($sale->receipt_number)
        ->and($cartItem2->sale->grand_total)->toEqual(340.00);
});

it('prevents negative stock deductions in branch inventory', function () {
    $user = User::create([
        'first_name' => 'Owner',
        'surname' => 'Branch',
        'email' => 'branchowner-'.uniqid().'@example.com',
        'password' => Hash::make('password'),
        'role' => 'manager',
        'business_name' => 'Branch Retail',
        'state' => 'Lagos',
        'local_govt' => 'Ikeja',
        'address' => 'HQ Address',
        'phone_number' => '08012345678',
    ]);

    $branch = Branch::create([
        'user_id' => $user->id,
        'business_name' => 'Branch Retail',
        'branch_name' => 'Ikeja Branch',
        'state' => 'Lagos',
        'local_govt' => 'Ikeja',
        'status' => 1,
    ]);

    $inventory = BranchInventory::create([
        'branch_id' => $branch->id,
        'business_name' => 'Branch Retail',
        'item_id' => 999,
        'item_type' => 'standard',
        'allocated_quantity' => 10.00,
        'current_quantity' => 10.00,
        'sold_quantity' => 0.00,
    ]);

    $deducted = $inventory->deductStock(6.50);
    expect($deducted)->toBeTrue();
    expect($inventory->current_quantity)->toEqual(3.50);
    expect($inventory->sold_quantity)->toEqual(6.50);

    // Attempting to deduct more than remaining 3.50 should return false
    $failedDeduction = $inventory->deductStock(5.00);
    expect($failedDeduction)->toBeFalse();
    expect($inventory->current_quantity)->toEqual(3.50);
});
