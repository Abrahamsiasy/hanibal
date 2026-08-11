<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\WalletRequestStatus;
use App\Enums\WalletRequestType;
use App\Http\Controllers\Controller;
use App\Models\WalletRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WalletController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();
        $wallet = $user->ensureWallet();

        $transactions = $wallet->transactions()
            ->latest()
            ->paginate(15, ['*'], 'transactions_page');

        $requests = $user->walletRequests()
            ->latest()
            ->paginate(10, ['*'], 'requests_page');

        return view('frontend.wallet.show', [
            'wallet' => $wallet,
            'availableBalance' => $wallet->availableBalance(),
            'transactions' => $transactions,
            'requests' => $requests,
        ]);
    }

    public function storeDeposit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1', 'max:1000000'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $wallet = $request->user()->ensureWallet();

        WalletRequest::query()->create([
            'user_id' => $request->user()->id,
            'wallet_id' => $wallet->id,
            'type' => WalletRequestType::Deposit,
            'amount' => $validated['amount'],
            'status' => WalletRequestStatus::Pending,
            'note' => $validated['note'] ?? null,
        ]);

        return redirect()
            ->route('wallet.show')
            ->with('success', 'Deposit request submitted. Waiting for admin approval.');
    }

    public function storeWithdrawal(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1', 'max:1000000'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $wallet = $request->user()->ensureWallet();
        $amount = number_format((float) $validated['amount'], 2, '.', '');

        if (bccomp($wallet->availableBalance(), $amount, 2) === -1) {
            return back()
                ->withInput()
                ->withErrors(['amount' => 'Insufficient available balance for this withdrawal.']);
        }

        WalletRequest::query()->create([
            'user_id' => $request->user()->id,
            'wallet_id' => $wallet->id,
            'type' => WalletRequestType::Withdrawal,
            'amount' => $amount,
            'status' => WalletRequestStatus::Pending,
            'note' => $validated['note'] ?? null,
        ]);

        return redirect()
            ->route('wallet.show')
            ->with('success', 'Withdrawal request submitted. Waiting for admin approval.');
    }
}
