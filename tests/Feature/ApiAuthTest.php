<?php

use App\Models\City;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('customer can register with a city and receives a token', function () {
    $city = City::factory()->create(['active' => true]);

    $this->postJson('/api/v1/auth/register', [
        'name' => 'Abel Tesfaye',
        'phone' => '0911000001',
        'password' => 'secret123',
        'password_confirmation' => 'secret123',
        'city_id' => $city->id,
    ])
        ->assertCreated()
        ->assertJsonStructure([
            'token',
            'user' => ['id', 'name', 'phone', 'city'],
        ]);

    $this->assertDatabaseHas('users', ['phone' => '0911000001', 'city_id' => $city->id]);
    $this->assertDatabaseHas('wallets', []);
});

test('registration fails for inactive city', function () {
    $city = City::factory()->inactive()->create();

    $this->postJson('/api/v1/auth/register', [
        'name' => 'Abel',
        'phone' => '0911000002',
        'password' => 'secret123',
        'password_confirmation' => 'secret123',
        'city_id' => $city->id,
    ])->assertUnprocessable()->assertJsonPath('errors.city_id.0', 'Selected city is not available.');
});

test('customer can login and receives a token', function () {
    $city = City::factory()->create();
    $user = User::factory()->create(['phone' => '0911000003', 'city_id' => $city->id, 'is_admin' => false]);

    $this->postJson('/api/v1/auth/login', [
        'phone' => '0911000003',
        'password' => 'password',
    ])
        ->assertOk()
        ->assertJsonStructure(['token', 'user']);
});

test('admin cannot login via customer api', function () {
    User::factory()->admin()->create(['phone' => '0900000001']);

    $this->postJson('/api/v1/auth/login', [
        'phone' => '0900000001',
        'password' => 'password',
    ])->assertUnprocessable();
});

test('customer can logout', function () {
    $user = User::factory()->create(['is_admin' => false]);
    $token = $user->createToken('api')->plainTextToken;

    $this->withToken($token)
        ->postJson('/api/v1/auth/logout')
        ->assertOk()
        ->assertJsonPath('message', 'Logged out successfully.');
});

test('authenticated user can fetch their profile', function () {
    $city = City::factory()->create();
    $user = User::factory()->create(['city_id' => $city->id, 'is_admin' => false]);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/auth/me')
        ->assertOk()
        ->assertJsonPath('user.id', $user->id)
        ->assertJsonPath('user.city.slug', $city->slug);
});

test('unauthenticated request to protected route is rejected', function () {
    $this->getJson('/api/v1/auth/me')->assertUnauthorized();
});
