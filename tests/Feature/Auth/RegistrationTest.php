<?php

use App\Models\User;

test('new users can register as buyer', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'testbuyer@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'buyer', // important!
    ]);

    $this->assertAuthenticated();

    $user = User::latest()->first();

    if ($user->hasActiveSubscription()) {
        $response->assertRedirect(route('welcome'));
    } else {
        $response->assertRedirect(route('subscription.plans'));
    }
});

test('new users can register as seller', function () {
    $response = $this->post('/register', [
        'name' => 'Test Seller',
        'email' => 'testseller@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'seller',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard.seller'));
});

test('new users can register as admin', function () {
    $response = $this->post('/register', [
        'name' => 'Test Admin',
        'email' => 'testadmin@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'admin',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard.admin'));
});
