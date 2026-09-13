<?php

use App\Models\User;
use App\Models\UserSubscription;
use App\Models\Welcome\SignupRequest;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create([
        'role' => 'staff',
        'status' => 1,
        'password_set' => true,
    ]);

    // Seed the signup verification record the controller requires
    SignupRequest::create([
        'email' => $user->email,
        'token' => \Illuminate\Support\Str::random(32),
        'token_expires_at' => now()->addDays(1),
        'is_used' => true,
    ]);

    // Seed an active subscription so the user reaches their dashboard
    UserSubscription::create([
        'user_id' => $user->id,
        'subscription_plan_id' => 1,
        'start_date' => now()->subDay(),
        'end_date' => now()->addMonth(),
        'status' => 'active',
        'amount_paid' => 0,
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});
