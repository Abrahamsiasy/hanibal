<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can view their wallet', function () {
    $user = User::factory()->create(['is_admin' => false]);
    $user->wallet()->create(['balance' => 250]);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/wallet')
        ->assertOk()
        ->assertJsonStructure([
            'wallet' => ['balance', 'available_balance', 'pending_withdrawals'],
            'transactions',
            'requests',
        ])
        ->assertJsonPath('wallet.balance', '250.00');
});

test('customer can submit a deposit request', function () {
    $user = User::factory()->create(['is_admin' => false]);

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/wallet/deposit', ['amount' => 500, 'note' => 'CBE transfer'])
        ->assertCreated()
        ->assertJsonPath('request.type', 'deposit')
        ->assertJsonPath('request.status', 'pending')
        ->assertJsonPath('request.amount', '500.00');

    $this->assertDatabaseHas('wallet_requests', ['user_id' => $user->id, 'amount' => 500, 'type' => 'deposit']);
});

test('customer can submit a withdrawal request within available balance', function () {
    $user = User::factory()->create(['is_admin' => false]);
    $user->wallet()->create(['balance' => 300]);

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/wallet/withdraw', ['amount' => 200])
        ->assertCreated()
        ->assertJsonPath('request.type', 'withdrawal');
});

test('withdrawal request fails when amount exceeds available balance', function () {
    $user = User::factory()->create(['is_admin' => false]);
    $user->wallet()->create(['balance' => 100]);

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/wallet/withdraw', ['amount' => 500])
        ->assertUnprocessable()
        ->assertJsonPath('errors.amount.0', 'Insufficient available balance for this withdrawal.');
});

test('unauthenticated user cannot access wallet', function () {
    $this->getJson('/api/v1/wallet')->assertUnauthorized();
});
