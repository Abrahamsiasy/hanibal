<?php

namespace Database\Factories;

use App\Enums\WalletRequestStatus;
use App\Enums\WalletRequestType;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WalletRequest>
 */
class WalletRequestFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'wallet_id' => Wallet::factory(),
            'type' => WalletRequestType::Deposit,
            'amount' => fake()->randomFloat(2, 10, 500),
            'status' => WalletRequestStatus::Pending,
            'note' => fake()->optional()->sentence(3),
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (WalletRequest $walletRequest) {
            if ($walletRequest->wallet_id && ! $walletRequest->user_id) {
                $walletRequest->user_id = Wallet::query()->find($walletRequest->wallet_id)?->user_id;
            }
        })->afterCreating(function (WalletRequest $walletRequest) {
            if ($walletRequest->wallet && $walletRequest->user_id !== $walletRequest->wallet->user_id) {
                $walletRequest->update(['user_id' => $walletRequest->wallet->user_id]);
            }
        });
    }

    public function deposit(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => WalletRequestType::Deposit,
        ]);
    }

    public function withdrawal(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => WalletRequestType::Withdrawal,
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => WalletRequestStatus::Pending,
        ]);
    }
}
