<?php

use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('blocks added managers from creating categories without the edit-items feature', function () {
    $creator = User::create([
        'first_name' => 'Creator',
        'surname' => 'User',
        'email' => 'creator-' . uniqid() . '@example.com',
        'password' => Hash::make('password'),
        'role' => 'manager',
        'business_name' => 'Demo Business',
        'addby' => null,
        'state' => 'Lagos',
        'local_govt' => 'Ikeja',
        'address' => 'Test address',
        'phone_number' => '08000000000',
    ]);

    $manager = User::create([
        'first_name' => 'Added',
        'surname' => 'Manager',
        'email' => 'manager-' . uniqid() . '@example.com',
        'password' => Hash::make('password'),
        'role' => 'manager',
        'business_name' => 'Demo Business',
        'addby' => $creator->email,
        'state' => 'Lagos',
        'local_govt' => 'Ikeja',
        'address' => 'Test address',
        'phone_number' => '08000000001',
    ]);

    $this->actingAs($manager);

    $response = $this->post('/manager/category/create', [
        'category_name' => 'Restricted Category',
    ]);

    $response->assertSessionHas('error', 'You do not have permission to create categories. This must be enabled by your business creator.');
    $this->assertDatabaseMissing('categories', [
        'business_name' => 'Demo Business',
        'category_name' => 'Restricted Category',
    ]);
});

it('blocks added managers from updating categories without the edit-items feature', function () {
    $creator = User::create([
        'first_name' => 'Creator',
        'surname' => 'User',
        'email' => 'creator-' . uniqid() . '@example.com',
        'password' => Hash::make('password'),
        'role' => 'manager',
        'business_name' => 'Demo Business',
        'addby' => null,
        'state' => 'Lagos',
        'local_govt' => 'Ikeja',
        'address' => 'Test address',
        'phone_number' => '08000000000',
    ]);

    $manager = User::create([
        'first_name' => 'Added',
        'surname' => 'Manager',
        'email' => 'manager-' . uniqid() . '@example.com',
        'password' => Hash::make('password'),
        'role' => 'manager',
        'business_name' => 'Demo Business',
        'addby' => $creator->email,
        'state' => 'Lagos',
        'local_govt' => 'Ikeja',
        'address' => 'Test address',
        'phone_number' => '08000000001',
    ]);

    $category = Category::create([
        'business_name' => 'Demo Business',
        'manager_name' => 'Added Manager',
        'manager_email' => $manager->email,
        'category_name' => 'Original Category',
    ]);

    $this->actingAs($manager);

    $response = $this->put('/manager/update_category/' . $category->id, [
        'category_name' => 'Updated Category',
    ]);

    $response->assertSessionHas('error', 'You do not have permission to update categories. This must be enabled by your business creator.');
    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'category_name' => 'Original Category',
    ]);
});
