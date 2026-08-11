<?php

use App\Enums\WalletRequestStatus;
use App\Enums\WalletRequestType;
use App\Models\User;
use App\Models\WalletRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('submits a deposit request for admin approval', function () {
    $user = User::factory()->create(['is_admin' => false]);
    $wallet = $user->ensureWallet();

    $this->actingAs($user)
        ->post(route('wallet.deposit'), [
            'amount' => 250,
            'note' => 'Cash deposit',
        ])
        ->assertRedirect(route('wallet.show'));

    $this->assertDatabaseHas('wallet_requests', [
        'user_id' => $user->id,
        'wallet_id' => $wallet->id,
        'type' => WalletRequestType::Deposit->value,
        'amount' => 250.00,
        'status' => WalletRequestStatus::Pending->value,
    ]);

    expect((float) $wallet->fresh()->balance)->toBe(0.0);
});

it('submits a withdrawal request against available balance', function () {
    $user = User::factory()->create(['is_admin' => false]);
    $wallet = $user->wallet()->create(['balance' => 500]);

    $this->actingAs($user)
        ->post(route('wallet.withdraw'), [
            'amount' => 100,
        ])
        ->assertRedirect(route('wallet.show'));

    expect(WalletRequest::query()->where('user_id', $user->id)->first())
        ->type->toBe(WalletRequestType::Withdrawal)
        ->status->toBe(WalletRequestStatus::Pending)
        ->and($wallet->fresh()->availableBalance())->toBe('400.00');
});

it('rejects withdrawal above available balance', function () {
    $user = User::factory()->create(['is_admin' => false]);
    $user->wallet()->create(['balance' => 50]);

    $this->actingAs($user)
        ->from(route('wallet.show'))
        ->post(route('wallet.withdraw'), [
            'amount' => 100,
        ])
        ->assertRedirect(route('wallet.show'))
        ->assertSessionHasErrors('amount');
});
