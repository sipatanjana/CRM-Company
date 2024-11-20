<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\AssertableJson;

use function Pest\Laravel\{post, get, assertAuthenticated};

beforeEach(function () {
    $this->seed();
});

uses(RefreshDatabase::class);

test('login dengan data yang salah', function () {
    $response = post('/api/auth', [
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    $response->assertStatus(400);
    $response->assertJson(
        fn(AssertableJson $json) =>
        $json->where('error', 'Invalid credentials')
    );
});

test('login dengan data yang benar', function () {
    $response = post('/api/auth', [
        'email' => 'test@example.com',
        'password' => 'test@example.com',
    ]);

    $response->assertStatus(200);
    assertAuthenticated();
});

test('logout berhasil', function () {
    $loginResponse = post('/api/auth', [
        'email' => 'test@example.com',
        'password' => 'test@example.com',
    ]);

    $response = post(
        '/api/auth/logout',
        [],
        [
            'Authorization' => 'Bearer ' . $loginResponse['access_token'],
            'Accept' => 'application/json',
        ]
    );

    $response->assertStatus(200);
    $response->assertJson(
        fn(AssertableJson $json) =>
        $json->where('message', 'Successfully logged out')
    );
});

test('logout gagal', function () {

    $response = post(
        '/api/auth/logout',
        [],
        [
            'Accept' => 'application/json',
        ]
    );

    $response->assertStatus(401);
    $response->assertJson(
        fn(AssertableJson $json) =>
        $json->where('message', 'Unauthenticated.')
    );
});

test('refresh token', function () {
    $loginResponse = post('/api/auth', [
        'email' => 'test@example.com',
        'password' => 'test@example.com',
    ]);

    $response = post(
        '/api/auth/refresh',
        [],
        [
            'Authorization' => 'Bearer ' . $loginResponse['access_token'],
            'Accept' => 'application/json',
        ]
    );

    $response->assertStatus(200);
});
