<?php

namespace Database\Factories;

use App\Enums\WalletTransactionType;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WalletTransaction>
 */
class WalletTransactionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $amount = fake()->randomFloat(2, 10, 200);

        return [
            'wallet_id' => Wallet::factory(),
            'type' => WalletTransactionType::Deposit,
            'amount' => $amount,
            'balance_after' => $amount,
            'description' => 'Test transaction',
        ];
    }
}
