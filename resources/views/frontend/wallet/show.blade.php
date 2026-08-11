@extends('frontend.layouts.app')

@section('title', 'My Wallet')

@section('content')
    <div class="max-w-5xl mx-auto px-4 py-8">

        <h1 class="font-display font-black text-3xl uppercase tracking-wide text-white mb-6">My Wallet</h1>

        {{-- Balance cards --}}
        <div class="grid grid-cols-2 gap-3 mb-8">
            <div class="bg-[#1a1a1a] border border-[#2a2a2a] rounded-xl p-5">
                <p class="text-xs font-semibold uppercase tracking-widest text-zinc-500 mb-1">Total Balance</p>
                <p class="font-display font-black text-3xl text-white">{{ number_format((float) $wallet->balance, 2) }}</p>
                <p class="text-xs text-zinc-600 mt-0.5">ETB</p>
            </div>
            <div class="bg-[#1a1a1a] border border-[#2a2a2a] rounded-xl p-5">
                <p class="text-xs font-semibold uppercase tracking-widest text-zinc-500 mb-1">Available</p>
                <p class="font-display font-black text-3xl text-green-400">{{ number_format((float) $availableBalance, 2) }}</p>
                <p class="text-xs text-zinc-600 mt-0.5">ETB</p>
            </div>
        </div>

        {{-- Deposit / Withdraw forms --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-10">
            <div class="bg-[#1a1a1a] border border-[#2a2a2a] rounded-xl p-6">
                <h2 class="font-display font-bold text-lg uppercase tracking-wide text-white mb-4">Request Deposit</h2>
                <form method="POST" action="{{ route('wallet.deposit') }}" class="space-y-3">
                    @csrf
                    <div>
                        <label for="deposit_amount" class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1.5">Amount (ETB)</label>
                        <input type="number" step="0.01" min="1" name="amount" id="deposit_amount"
                               value="{{ old('amount') }}"
                               class="w-full bg-[#111] border border-[#2a2a2a] text-white placeholder-zinc-600 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-red-600 transition-colors"
                               required>
                    </div>
                    <div>
                        <label for="deposit_note" class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1.5">Note (optional)</label>
                        <input type="text" name="note" id="deposit_note"
                               value="{{ old('note') }}"
                               class="w-full bg-[#111] border border-[#2a2a2a] text-white placeholder-zinc-600 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-red-600 transition-colors">
                    </div>
                    <button type="submit"
                            class="w-full bg-red-600 hover:bg-red-500 text-white font-display font-black uppercase tracking-widest py-3 rounded-lg transition-colors text-sm">
                        Submit Deposit
                    </button>
                </form>
            </div>

            <div class="bg-[#1a1a1a] border border-[#2a2a2a] rounded-xl p-6">
                <h2 class="font-display font-bold text-lg uppercase tracking-wide text-white mb-4">Request Withdrawal</h2>
                <form method="POST" action="{{ route('wallet.withdraw') }}" class="space-y-3">
                    @csrf
                    <div>
                        <label for="withdraw_amount" class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1.5">Amount (ETB)</label>
                        <input type="number" step="0.01" min="1" name="amount" id="withdraw_amount"
                               class="w-full bg-[#111] border border-[#2a2a2a] text-white placeholder-zinc-600 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-red-600 transition-colors"
                               required>
                    </div>
                    <div>
                        <label for="withdraw_note" class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1.5">Note (optional)</label>
                        <input type="text" name="note" id="withdraw_note"
                               class="w-full bg-[#111] border border-[#2a2a2a] text-white placeholder-zinc-600 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-red-600 transition-colors">
                    </div>
                    <button type="submit"
                            class="w-full bg-zinc-700 hover:bg-zinc-600 text-white font-display font-black uppercase tracking-widest py-3 rounded-lg transition-colors text-sm">
                        Submit Withdrawal
                    </button>
                </form>
            </div>
        </div>

        {{-- Requests table --}}
        <h2 class="font-display font-bold text-base uppercase tracking-widest text-zinc-400 mb-3">My Requests</h2>
        <div class="bg-[#1a1a1a] border border-[#2a2a2a] rounded-xl overflow-hidden mb-8">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-[#2a2a2a]">
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-zinc-500">Type</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold uppercase tracking-wider text-zinc-500">Amount</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-zinc-500">Status</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-zinc-500 hidden sm:table-cell">Note</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-zinc-500 hidden sm:table-cell">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#2a2a2a]">
                        @forelse ($requests as $walletRequest)
                            <tr>
                                <td class="px-4 py-3 text-zinc-300">{{ $walletRequest->type->label() }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-white">{{ number_format((float) $walletRequest->amount, 2) }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-block text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-zinc-800 text-zinc-400">
                                        {{ $walletRequest->status->label() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-zinc-500 hidden sm:table-cell">{{ $walletRequest->note }}</td>
                                <td class="px-4 py-3 text-zinc-500 hidden sm:table-cell">{{ $walletRequest->created_at->format('M j, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-zinc-600">No requests yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mb-8">{{ $requests->links() }}</div>

        {{-- Transactions table --}}
        <h2 class="font-display font-bold text-base uppercase tracking-widest text-zinc-400 mb-3">Transactions</h2>
        <div class="bg-[#1a1a1a] border border-[#2a2a2a] rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-[#2a2a2a]">
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-zinc-500">Type</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold uppercase tracking-wider text-zinc-500">Amount</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold uppercase tracking-wider text-zinc-500 hidden sm:table-cell">Balance After</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-zinc-500 hidden sm:table-cell">Description</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-zinc-500 hidden sm:table-cell">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#2a2a2a]">
                        @forelse ($transactions as $transaction)
                            <tr>
                                <td class="px-4 py-3 text-zinc-300">{{ $transaction->type->label() }}</td>
                                <td class="px-4 py-3 text-right font-semibold @if($transaction->type->isCredit()) text-green-400 @else text-red-400 @endif">
                                    {{ $transaction->type->isCredit() ? '+' : '-' }}{{ number_format((float) $transaction->amount, 2) }}
                                </td>
                                <td class="px-4 py-3 text-right text-zinc-400 hidden sm:table-cell">{{ number_format((float) $transaction->balance_after, 2) }}</td>
                                <td class="px-4 py-3 text-zinc-500 hidden sm:table-cell">{{ $transaction->description }}</td>
                                <td class="px-4 py-3 text-zinc-500 hidden sm:table-cell">{{ $transaction->created_at->format('M j, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-zinc-600">No transactions yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4">{{ $transactions->links() }}</div>

    </div>
@endsection
