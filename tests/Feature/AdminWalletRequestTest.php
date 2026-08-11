<?php

use App\Enums\WalletRequestStatus;
use App\Enums\WalletRequestType;
use App\Enums\WalletTransactionType;
use App\Models\User;
use App\Models\WalletRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('approves a deposit and credits the wallet', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->create(['is_admin' => false]);
    $wallet = $customer->wallet()->create(['balance' => 0]);

    $walletRequest = WalletRequest::factory()->deposit()->pending()->create([
        'user_id' => $customer->id,
        'wallet_id' => $wallet->id,
        'amount' => 200,
    ]);

    $this->actingAs($admin)
        ->post(route('admin.wallet-requests.approve', $walletRequest), [
            'admin_note' => 'Received',
        ])
        ->assertRedirect();

    expect($walletRequest->fresh()->status)->toBe(WalletRequestStatus::Approved)
        ->and((float) $wallet->fresh()->balance)->toBe(200.0);

    $this->assertDatabaseHas('wallet_transactions', [
        'wallet_id' => $wallet->id,
        'type' => WalletTransactionType::Deposit->value,
        'amount' => 200.00,
    ]);
});

it('approves a withdrawal and debits the wallet', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->create(['is_admin' => false]);
    $wallet = $customer->wallet()->create(['balance' => 300]);

    $walletRequest = WalletRequest::factory()->withdrawal()->pending()->create([
        'user_id' => $customer->id,
        'wallet_id' => $wallet->id,
        'amount' => 100,
    ]);

    $this->actingAs($admin)
        ->post(route('admin.wallet-requests.approve', $walletRequest))
        ->assertRedirect();

    expect($walletRequest->fresh()->status)->toBe(WalletRequestStatus::Approved)
        ->and((float) $wallet->fresh()->balance)->toBe(200.0);
});

it('rejects a pending wallet request without changing balance', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->create(['is_admin' => false]);
    $wallet = $customer->wallet()->create(['balance' => 150]);

    $walletRequest = WalletRequest::factory()->deposit()->pending()->create([
        'user_id' => $customer->id,
        'wallet_id' => $wallet->id,
        'amount' => 50,
        'type' => WalletRequestType::Deposit,
    ]);

    $this->actingAs($admin)
        ->post(route('admin.wallet-requests.reject', $walletRequest), [
            'admin_note' => 'Invalid',
        ])
        ->assertRedirect();

    expect($walletRequest->fresh()->status)->toBe(WalletRequestStatus::Rejected)
        ->and((float) $wallet->fresh()->balance)->toBe(150.0);
});
