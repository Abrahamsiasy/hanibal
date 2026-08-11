<?php

namespace App\Http\Controllers\Admin;

use App\Enums\WalletRequestStatus;
use App\Enums\WalletRequestType;
use App\Enums\WalletTransactionType;
use App\Http\Controllers\Controller;
use App\Models\WalletRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;

class WalletRequestController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();
        $statusEnum = WalletRequestStatus::tryFrom($status);

        $requests = WalletRequest::query()
            ->with(['user', 'wallet', 'reviewer'])
            ->when($statusEnum, fn ($query) => $query->where('status', $statusEnum))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.wallet-requests.index', [
            'requests' => $requests,
            'currentStatus' => $statusEnum?->value ?? 'all',
        ]);
    }

    public function approve(Request $request, WalletRequest $walletRequest): RedirectResponse
    {
        $validated = $request->validate([
            'admin_note' => ['nullable', 'string', 'max:255'],
        ]);

        if (! $walletRequest->isPending()) {
            return back()->withErrors(['wallet_request' => 'Only pending requests can be approved.']);
        }

        try {
            DB::transaction(function () use ($walletRequest, $validated, $request) {
                /** @var WalletRequest $lockedRequest */
                $lockedRequest = WalletRequest::query()
                    ->whereKey($walletRequest->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if (! $lockedRequest->isPending()) {
                    throw new RuntimeException('Only pending requests can be approved.');
                }

                $wallet = $lockedRequest->wallet()->lockForUpdate()->firstOrFail();
                $amount = number_format((float) $lockedRequest->amount, 2, '.', '');

                if ($lockedRequest->type === WalletRequestType::Deposit) {
                    $wallet->credit(
                        $amount,
                        WalletTransactionType::Deposit,
                        $lockedRequest,
                        'Deposit approved',
                    );
                } else {
                    if (bccomp((string) $wallet->balance, $amount, 2) === -1) {
                        throw new RuntimeException('Customer no longer has enough balance for this withdrawal.');
                    }

                    $wallet->debit(
                        $amount,
                        WalletTransactionType::Withdrawal,
                        $lockedRequest,
                        'Withdrawal approved',
                    );
                }

                $lockedRequest->update([
                    'status' => WalletRequestStatus::Approved,
                    'admin_note' => $validated['admin_note'] ?? null,
                    'reviewed_by' => $request->user()->id,
                    'reviewed_at' => now(),
                ]);
            });
        } catch (RuntimeException $exception) {
            return back()->withErrors(['wallet_request' => $exception->getMessage()]);
        }

        return back()->with('success', 'Wallet request approved.');
    }

    public function reject(Request $request, WalletRequest $walletRequest): RedirectResponse
    {
        $validated = $request->validate([
            'admin_note' => ['nullable', 'string', 'max:255'],
        ]);

        if (! $walletRequest->isPending()) {
            return back()->withErrors(['wallet_request' => 'Only pending requests can be rejected.']);
        }

        $walletRequest->update([
            'status' => WalletRequestStatus::Rejected,
            'admin_note' => $validated['admin_note'] ?? null,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Wallet request rejected.');
    }
}
