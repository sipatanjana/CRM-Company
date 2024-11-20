<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\{post, assertAuthenticated};

test('user can login with correct credentials', function () {
    User::create([
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = post('/api/auth', [
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200);
    assertAuthenticated();
});
