<?php

namespace App\Models;

use App\Enums\WalletRequestStatus;
use App\Enums\WalletRequestType;
use App\Enums\WalletTransactionType;
use Database\Factories\WalletFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

class Wallet extends Model
{
    /** @use HasFactory<WalletFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'balance',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function requests(): HasMany
    {
        return $this->hasMany(WalletRequest::class);
    }

    public function pendingWithdrawalTotal(): string
    {
        return (string) $this->requests()
            ->where('type', WalletRequestType::Withdrawal)
            ->where('status', WalletRequestStatus::Pending)
            ->sum('amount');
    }

    public function availableBalance(): string
    {
        return bcsub((string) $this->balance, $this->pendingWithdrawalTotal(), 2);
    }

    public function credit(
        string $amount,
        WalletTransactionType $type,
        ?Model $reference = null,
        ?string $description = null,
    ): WalletTransaction {
        if (! $type->isCredit()) {
            throw new InvalidArgumentException('Transaction type must be a credit.');
        }

        return $this->applyTransaction($amount, $type, $reference, $description);
    }

    public function debit(
        string $amount,
        WalletTransactionType $type,
        ?Model $reference = null,
        ?string $description = null,
    ): WalletTransaction {
        if ($type->isCredit()) {
            throw new InvalidArgumentException('Transaction type must be a debit.');
        }

        return $this->applyTransaction($amount, $type, $reference, $description);
    }

    private function applyTransaction(
        string $amount,
        WalletTransactionType $type,
        ?Model $reference = null,
        ?string $description = null,
    ): WalletTransaction {
        if (bccomp($amount, '0', 2) !== 1) {
            throw new InvalidArgumentException('Amount must be greater than zero.');
        }

        return DB::transaction(function () use ($amount, $type, $reference, $description) {
            /** @var Wallet $wallet */
            $wallet = static::query()->whereKey($this->id)->lockForUpdate()->firstOrFail();

            $balance = (string) $wallet->balance;

            if ($type->isCredit()) {
                $newBalance = bcadd($balance, $amount, 2);
            } else {
                if (bccomp($balance, $amount, 2) === -1) {
                    throw new RuntimeException('Insufficient wallet balance.');
                }

                $newBalance = bcsub($balance, $amount, 2);
            }

            $wallet->update(['balance' => $newBalance]);
            $this->setRawAttributes($wallet->getAttributes(), true);

            return $wallet->transactions()->create([
                'type' => $type,
                'amount' => $amount,
                'balance_after' => $newBalance,
                'reference_type' => $reference?->getMorphClass(),
                'reference_id' => $reference?->getKey(),
                'description' => $description,
            ]);
        });
    }
}
