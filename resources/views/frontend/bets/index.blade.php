@extends('frontend.layouts.app')

@section('title', 'My Bets')

@section('content')
    @php $taxRate = 0.15; @endphp
    <div class="max-w-5xl mx-auto px-4 py-8">

        <h1 class="font-display font-black text-3xl uppercase tracking-wide text-white mb-6">My Bets</h1>

        @if (request('placed'))
            <div class="bg-green-900/50 border border-green-700 text-green-300 rounded-lg px-4 py-3 text-sm mb-6">
                {{ request('placed') }} bet{{ request('placed') == 1 ? '' : 's' }} placed successfully. Good luck!
            </div>
        @endif

        {{-- Mobile card list --}}
        <div class="space-y-3 sm:hidden">
            @forelse ($bets as $bet)
                @php
                    $gross  = (float) $bet->potential_payout;
                    $profit = $gross - (float) $bet->stake;
                    $tax    = $profit * $taxRate;
                    $net    = $gross - $tax;
                @endphp
                <div class="bg-[#1a1a1a] border border-[#2a2a2a] rounded-xl overflow-hidden">
                    <div class="px-4 py-3 border-b border-[#2a2a2a] flex items-center justify-between">
                        <div class="min-w-0">
                            <p class="font-semibold text-white text-sm truncate">{{ $bet->cityEvent->event->title }}</p>
                            <p class="text-xs text-zinc-500 mt-0.5">{{ $bet->cityEvent->city->name }} · {{ $bet->bettingOption->name }}</p>
                        </div>
                        <span class="shrink-0 ml-3 inline-block text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded
                            @if($bet->status === \App\Enums\BetStatus::Won) bg-green-900/60 text-green-400 border border-green-700/50
                            @elseif($bet->status === \App\Enums\BetStatus::Lost) bg-red-900/40 text-red-400 border border-red-700/30
                            @elseif($bet->status === \App\Enums\BetStatus::Pending) bg-yellow-900/40 text-yellow-400 border border-yellow-700/30
                            @else bg-zinc-800 text-zinc-400 border border-zinc-700 @endif">
                            {{ $bet->status->label() }}
                        </span>
                    </div>
                    <div class="px-4 py-3 grid grid-cols-2 gap-y-2 text-sm">
                        <div>
                            <p class="text-[10px] uppercase tracking-wider text-zinc-500">Stake</p>
                            <p class="text-white font-semibold">{{ number_format((float) $bet->stake, 2) }} ETB</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] uppercase tracking-wider text-zinc-500">Odds</p>
                            <p class="text-white font-semibold">{{ number_format((float) $bet->odds, 2) }}x</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-wider text-zinc-500">Gross Payout</p>
                            <p class="text-white font-semibold">{{ number_format($gross, 2) }} ETB</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] uppercase tracking-wider text-zinc-500">Tax (15%)</p>
                            <p class="text-red-400 font-semibold">−{{ number_format($tax, 2) }} ETB</p>
                        </div>
                        <div class="col-span-2 pt-2 border-t border-[#2a2a2a]">
                            <p class="text-[10px] uppercase tracking-wider text-zinc-500">
                                @if($bet->status === \App\Enums\BetStatus::Won) Net Won @else Net Payout (if win) @endif
                            </p>
                            <p class="font-display font-black text-xl
                                @if($bet->status === \App\Enums\BetStatus::Won) text-green-400
                                @elseif($bet->status === \App\Enums\BetStatus::Lost) text-zinc-600 line-through
                                @else text-white @endif">
                                {{ number_format($net, 2) }} ETB
                            </p>
                        </div>
                    </div>
                    <div class="px-4 py-2 border-t border-[#2a2a2a] text-xs text-zinc-600">
                        {{ $bet->created_at->format('M j, Y · g:i A') }}
                    </div>
                </div>
            @empty
                <div class="bg-[#1a1a1a] border border-[#2a2a2a] rounded-xl p-8 text-center">
                    <p class="text-zinc-500 mb-4">No bets placed yet.</p>
                    @auth
                        @if (auth()->user()->city)
                            <a href="{{ route('cities.show', auth()->user()->city) }}"
                               class="inline-block bg-red-600 hover:bg-red-500 text-white font-display font-black uppercase tracking-widest text-xs px-5 py-2 rounded-lg transition-colors">
                                View Fight Card
                            </a>
                        @endif
                    @endauth
                </div>
            @endforelse
        </div>

        {{-- Desktop table --}}
        <div class="hidden sm:block bg-[#1a1a1a] border border-[#2a2a2a] rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-[#2a2a2a]">
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-zinc-500">Event / Pick</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold uppercase tracking-wider text-zinc-500">Stake</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold uppercase tracking-wider text-zinc-500">Odds</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold uppercase tracking-wider text-zinc-500">Gross</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold uppercase tracking-wider text-zinc-500">Tax (15%)</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold uppercase tracking-wider text-zinc-500">Net Payout</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-zinc-500">Status</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-zinc-500 hidden lg:table-cell">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#2a2a2a]">
                        @forelse ($bets as $bet)
                            @php
                                $gross  = (float) $bet->potential_payout;
                                $profit = $gross - (float) $bet->stake;
                                $tax    = $profit * $taxRate;
                                $net    = $gross - $tax;
                            @endphp
                            <tr class="hover:bg-[#222] transition-colors">
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-white truncate max-w-[180px]">{{ $bet->cityEvent->event->title }}</p>
                                    <p class="text-xs text-zinc-500 mt-0.5">{{ $bet->cityEvent->city->name }} · {{ $bet->bettingOption->name }}</p>
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-white">{{ number_format((float) $bet->stake, 2) }}</td>
                                <td class="px-4 py-3 text-right text-zinc-300">{{ number_format((float) $bet->odds, 2) }}x</td>
                                <td class="px-4 py-3 text-right text-zinc-300">{{ number_format($gross, 2) }}</td>
                                <td class="px-4 py-3 text-right text-red-400">−{{ number_format($tax, 2) }}</td>
                                <td class="px-4 py-3 text-right font-display font-black
                                    @if($bet->status === \App\Enums\BetStatus::Won) text-green-400
                                    @elseif($bet->status === \App\Enums\BetStatus::Lost) text-zinc-600 line-through
                                    @else text-white @endif">
                                    {{ number_format($net, 2) }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-block text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded
                                        @if($bet->status === \App\Enums\BetStatus::Won) bg-green-900/60 text-green-400 border border-green-700/50
                                        @elseif($bet->status === \App\Enums\BetStatus::Lost) bg-red-900/40 text-red-400 border border-red-700/30
                                        @elseif($bet->status === \App\Enums\BetStatus::Pending) bg-yellow-900/40 text-yellow-400 border border-yellow-700/30
                                        @else bg-zinc-800 text-zinc-400 border border-zinc-700 @endif">
                                        {{ $bet->status->label() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-zinc-500 hidden lg:table-cell">{{ $bet->created_at->format('M j, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-12 text-center text-zinc-600">
                                    <p class="mb-3">No bets placed yet.</p>
                                    @auth
                                        @if (auth()->user()->city)
                                            <a href="{{ route('cities.show', auth()->user()->city) }}"
                                               class="inline-block bg-red-600 hover:bg-red-500 text-white font-display font-black uppercase tracking-widest text-xs px-5 py-2 rounded-lg transition-colors">
                                                View Fight Card
                                            </a>
                                        @endif
                                    @endauth
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($bets->hasPages())
            <div class="mt-4">{{ $bets->links() }}</div>
        @endif

    </div>
@endsection
