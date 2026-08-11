<?php

use App\Models\City;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('registers a customer with a wallet', function () {
    $city = City::factory()->create(['slug' => 'gambella', 'active' => true]);

    $this->post(route('register.store'), [
        'name' => 'Abebe',
        'phone' => '0933333333',
        'password' => 'password',
        'password_confirmation' => 'password',
        'city_id' => $city->id,
    ])->assertRedirect(route('cities.show', $city));

    $user = User::query()->where('phone', '0933333333')->first();

    expect($user)->not->toBeNull()
        ->and($user->is_admin)->toBeFalse()
        ->and($user->wallet)->not->toBeNull()
        ->and((float) $user->wallet->balance)->toBe(0.0);

    $this->assertAuthenticatedAs($user);
});

it('logs in a customer with phone and password', function () {
    $user = User::factory()->create([
        'phone' => '0944444444',
        'password' => 'password',
        'is_admin' => false,
    ]);
    $user->ensureWallet();

    $this->post(route('login.store'), [
        'phone' => '0944444444',
        'password' => 'password',
    ])->assertRedirect();

    $this->assertAuthenticatedAs($user);
});

it('rejects admin credentials on customer login', function () {
    User::factory()->admin()->create([
        'phone' => '0955555555',
        'password' => 'password',
    ]);

    $this->post(route('login.store'), [
        'phone' => '0955555555',
        'password' => 'password',
    ])->assertSessionHasErrors('phone');

    $this->assertGuest();
});
