@extends('frontend.layouts.app')

@section('title', 'Bet Ticket #' . $bet->id)

@section('content')
    @php
        $taxRate = 0.15;
        $gross   = (float) $bet->potential_payout;
        $profit  = $gross - (float) $bet->stake;
        $tax     = $profit * $taxRate;
        $net     = $gross - $tax;
    @endphp

    <div class="max-w-md mx-auto px-4 py-8">

        <div class="mb-4">
            <a href="{{ route('bets.index') }}"
               class="text-xs text-zinc-500 hover:text-white transition-colors flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                My Bets
            </a>
        </div>

        {{-- Ticket card --}}
        <div class="bg-[#1a1a1a] border border-[#2a2a2a] rounded-2xl overflow-hidden">

            {{-- Header --}}
            <div class="bg-[#111] px-5 py-4 flex items-center justify-between border-b border-[#2a2a2a]">
                <div>
                    <p class="font-display font-black text-sm uppercase tracking-widest text-red-500">
                        {{ config('app.name') }}
                    </p>
                    <p class="text-[10px] text-zinc-600 mt-0.5 uppercase tracking-wider">Bet Ticket</p>
                </div>
                <span class="inline-block text-[10px] font-black uppercase tracking-wider px-2.5 py-1 rounded-lg
                    @if($bet->status === \App\Enums\BetStatus::Won) bg-green-900/60 text-green-400 border border-green-700/50
                    @elseif($bet->status === \App\Enums\BetStatus::Lost) bg-red-900/40 text-red-400 border border-red-700/30
                    @elseif($bet->status === \App\Enums\BetStatus::Pending) bg-yellow-900/40 text-yellow-400 border border-yellow-700/30
                    @else bg-zinc-800 text-zinc-400 border border-zinc-700 @endif">
                    {{ $bet->status->label() }}
                </span>
            </div>

            {{-- Perforation line --}}
            <div class="relative py-0">
                <div class="border-t border-dashed border-[#3a3a3a]"></div>
                <div class="absolute -left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-[#0d0d0d]"></div>
                <div class="absolute -right-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-[#0d0d0d]"></div>
            </div>

            {{-- Event info --}}
            <div class="px-5 py-4 border-b border-[#2a2a2a]">
                <p class="text-[10px] uppercase tracking-wider text-zinc-600 mb-1">Event</p>
                <p class="font-display font-bold text-lg uppercase leading-tight text-white">
                    {{ $bet->cityEvent->event->title }}
                </p>
                <div class="flex items-center gap-2 mt-1.5">
                    <span class="text-xs text-zinc-500">
                        📍 {{ $bet->cityEvent->city->name }}
                    </span>
                    <span class="text-zinc-700">·</span>
                    <span class="text-xs text-zinc-500">
                        {{ $bet->cityEvent->event->starts_at->format('M j, Y · g:i A') }}
                    </span>
                </div>
            </div>

            {{-- Selection --}}
            <div class="px-5 py-4 border-b border-[#2a2a2a]">
                <p class="text-[10px] uppercase tracking-wider text-zinc-600 mb-1">Your Pick</p>
                <div class="flex items-center justify-between">
                    <p class="font-semibold text-white text-base">{{ $bet->bettingOption->name }}</p>
                    <span class="font-display font-black text-xl text-red-400">
                        {{ number_format((float) $bet->odds, 2) }}x
                    </span>
                </div>
            </div>

            {{-- Stake & Payout breakdown --}}
            <div class="px-5 py-4 space-y-2.5 border-b border-[#2a2a2a]">
                <div class="flex justify-between items-center">
                    <span class="text-xs text-zinc-500 uppercase tracking-wider">Stake</span>
                    <span class="text-sm font-semibold text-white">
                        {{ number_format((float) $bet->stake, 2) }} ETB
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs text-zinc-500 uppercase tracking-wider">Gross Payout</span>
                    <span class="text-sm font-semibold text-white">
                        {{ number_format($gross, 2) }} ETB
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs text-zinc-500 uppercase tracking-wider">Tax (15% on profit)</span>
                    <span class="text-sm font-semibold text-red-400">
                        −{{ number_format($tax, 2) }} ETB
                    </span>
                </div>
            </div>

            {{-- Net payout --}}
            <div class="px-5 py-4 bg-[#111]">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-[10px] uppercase tracking-wider text-zinc-600">
                            @if($bet->status === \App\Enums\BetStatus::Won)
                                Net Won
                            @elseif($bet->status === \App\Enums\BetStatus::Lost)
                                Net Payout
                            @else
                                Net Payout (if win)
                            @endif
                        </p>
                    </div>
                    <p class="font-display font-black text-3xl
                        @if($bet->status === \App\Enums\BetStatus::Won) text-green-400
                        @elseif($bet->status === \App\Enums\BetStatus::Lost) text-zinc-600 line-through
                        @else text-white @endif">
                        {{ number_format($net, 2) }} <span class="text-base font-bold">ETB</span>
                    </p>
                </div>
            </div>

            {{-- Perforation line --}}
            <div class="relative py-0">
                <div class="border-t border-dashed border-[#3a3a3a]"></div>
                <div class="absolute -left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-[#0d0d0d]"></div>
                <div class="absolute -right-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-[#0d0d0d]"></div>
            </div>

            {{-- Footer --}}
            <div class="px-5 py-3 flex items-center justify-between">
                <p class="text-[10px] text-zinc-600 uppercase tracking-wider">
                    Ticket #{{ str_pad($bet->id, 6, '0', STR_PAD_LEFT) }}
                </p>
                <p class="text-[10px] text-zinc-600">
                    {{ $bet->created_at->format('M j, Y · g:i A') }}
                </p>
            </div>
        </div>

        {{-- Back to fight card --}}
        @if (auth()->user()->city)
            <div class="mt-4 text-center">
                <a href="{{ route('cities.show', auth()->user()->city) }}"
                   class="inline-block text-xs text-zinc-500 hover:text-white transition-colors uppercase tracking-wider">
                    View Fight Card →
                </a>
            </div>
        @endif

    </div>
@endsection
