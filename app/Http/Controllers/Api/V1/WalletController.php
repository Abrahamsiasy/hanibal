<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\WalletRequestStatus;
use App\Enums\WalletRequestType;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\WalletRequestResource;
use App\Http\Resources\V1\WalletResource;
use App\Http\Resources\V1\WalletTransactionResource;
use App\Models\WalletRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        $wallet = $user->ensureWallet();

        $transactions = $wallet->transactions()->latest()->paginate(20);
        $requests = $user->walletRequests()->latest()->paginate(20);

        return response()->json([
            'wallet' => WalletResource::make($wallet),
            'transactions' => WalletTransactionResource::collection($transactions),
            'requests' => WalletRequestResource::collection($requests),
        ]);
    }

    public function deposit(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1', 'max:1000000'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $wallet = $request->user()->ensureWallet();

        $walletRequest = WalletRequest::query()->create([
            'user_id' => $request->user()->id,
            'wallet_id' => $wallet->id,
            'type' => WalletRequestType::Deposit,
            'amount' => $validated['amount'],
            'status' => WalletRequestStatus::Pending,
            'note' => $validated['note'] ?? null,
        ]);

        return response()->json([
            'message' => 'Deposit request submitted. Waiting for admin approval.',
            'request' => WalletRequestResource::make($walletRequest),
        ], 201);
    }

    public function withdraw(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1', 'max:1000000'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $wallet = $request->user()->ensureWallet();
        $amount = number_format((float) $validated['amount'], 2, '.', '');

        if (bccomp($wallet->availableBalance(), $amount, 2) === -1) {
            return response()->json([
                'message' => 'Insufficient available balance.',
                'errors' => ['amount' => ['Insufficient available balance for this withdrawal.']],
            ], 422);
        }

        $walletRequest = WalletRequest::query()->create([
            'user_id' => $request->user()->id,
            'wallet_id' => $wallet->id,
            'type' => WalletRequestType::Withdrawal,
            'amount' => $amount,
            'status' => WalletRequestStatus::Pending,
            'note' => $validated['note'] ?? null,
        ]);

        return response()->json([
            'message' => 'Withdrawal request submitted. Waiting for admin approval.',
            'request' => WalletRequestResource::make($walletRequest),
        ], 201);
    }
}
