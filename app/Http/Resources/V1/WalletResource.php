<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    /**
     * @return array{
     *     balance: string,
     *     available_balance: string,
     *     pending_withdrawals: string,
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'balance' => $this->balance,
            'available_balance' => $this->availableBalance(),
            'pending_withdrawals' => $this->pendingWithdrawalTotal(),
        ];
    }
}
