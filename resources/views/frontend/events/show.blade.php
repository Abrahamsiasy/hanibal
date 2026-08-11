@extends('frontend.layouts.app')

@section('title', $event->title)

@section('content')

    {{-- ── Back nav ──────────────────────────────────────────────────────────── --}}
    <div class="max-w-5xl mx-auto px-4 pt-5">
        <a href="{{ route('cities.show', $city) }}"
           class="inline-flex items-center gap-1.5 text-xs text-zinc-500 hover:text-white transition-colors font-semibold uppercase tracking-wide">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            {{ $city->name }} Fight Card
        </a>
    </div>

    {{-- ── Fighter hero ──────────────────────────────────────────────────────── --}}
    @php
        $bout = $event->bout;
        $red = $bout?->redFighter;
        $blue = $bout?->blueFighter;
    @endphp

    @if ($bout)
        <div class="relative overflow-hidden mt-4 bg-[#0d0d0d]">
            <div class="absolute inset-0 flex">
                <div class="w-1/2 bg-gradient-to-r from-red-900/25 to-transparent"></div>
                <div class="w-1/2 bg-gradient-to-l from-blue-900/25 to-transparent"></div>
            </div>

            <div class="relative max-w-5xl mx-auto px-4">
                <div class="text-center py-4">
                    <span class="inline-block bg-zinc-800 text-zinc-300 text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded">
                        {{ $bout->sport }} &nbsp;·&nbsp;
                        {{ $bout->is_main_event ? 'Main Event' : 'Bout '.$bout->bout_number }}
                        @if ($bout->rounds)
                            &nbsp;·&nbsp; {{ $bout->rounds }} Rounds
                        @endif
                    </span>
                </div>

                <div class="flex items-end justify-between gap-4">
                    {{-- Red corner --}}
                    <div class="flex-1 flex flex-col items-center text-center">
                        <div class="relative">
                            @if ($red?->image_path)
                                <img src="{{ asset('storage/'.$red->image_path) }}"
                                     alt="{{ $red->name }}"
                                     class="w-32 h-32 sm:w-44 sm:h-44 object-cover object-top rounded-lg shadow-xl shadow-red-900/40 border-2 border-red-700/50">
                            @else
                                <div class="w-32 h-32 sm:w-44 sm:h-44 rounded-lg bg-red-900/20 border-2 border-red-700/30 flex items-center justify-center">
                                    <span class="font-display font-black text-5xl text-red-400">
                                        {{ mb_substr($red?->name ?? '?', 0, 1) }}
                                    </span>
                                </div>
                            @endif
                            <div class="absolute -top-2 -left-2 bg-red-600 text-white text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded">Red</div>
                        </div>
                        <h2 class="font-display font-black text-xl uppercase tracking-wide mt-3 text-white">{{ $red?->name ?? '—' }}</h2>
                    </div>

                    {{-- VS --}}
                    <div class="flex flex-col items-center gap-1 pb-10 shrink-0">
                        <div class="w-px h-6 bg-zinc-700"></div>
                        <span class="font-display font-black text-2xl text-zinc-500 tracking-widest">VS</span>
                        <div class="w-px h-6 bg-zinc-700"></div>
                    </div>

                    {{-- Blue corner --}}
                    <div class="flex-1 flex flex-col items-center text-center">
                        <div class="relative">
                            @if ($blue?->image_path)
                                <img src="{{ asset('storage/'.$blue->image_path) }}"
                                     alt="{{ $blue->name }}"
                                     class="w-32 h-32 sm:w-44 sm:h-44 object-cover object-top rounded-lg shadow-xl shadow-blue-900/40 border-2 border-blue-700/50">
                            @else
                                <div class="w-32 h-32 sm:w-44 sm:h-44 rounded-lg bg-blue-900/20 border-2 border-blue-700/30 flex items-center justify-center">
                                    <span class="font-display font-black text-5xl text-blue-400">
                                        {{ mb_substr($blue?->name ?? '?', 0, 1) }}
                                    </span>
                                </div>
                            @endif
                            <div class="absolute -top-2 -right-2 bg-blue-700 text-white text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded">Blue</div>
                        </div>
                        <h2 class="font-display font-black text-xl uppercase tracking-wide mt-3 text-white">{{ $blue?->name ?? '—' }}</h2>
                    </div>
                </div>

                <div class="text-center pb-4 pt-2">
                    <p class="text-xs text-zinc-500 uppercase tracking-widest">
                        {{ $city->name }} &nbsp;·&nbsp; {{ $event->starts_at->format('M j, Y · g:i A') }}
                    </p>
                </div>
            </div>
        </div>
    @else
        <div class="max-w-5xl mx-auto px-4 pt-4">
            <h1 class="font-display font-black text-3xl uppercase tracking-wide text-white">{{ $event->title }}</h1>
            <p class="text-zinc-500 text-sm mt-1">{{ $city->name }} · {{ $event->starts_at->format('M j, Y g:i A') }}</p>
        </div>
    @endif

    {{-- ── Description ──────────────────────────────────────────────────────── --}}
    @if ($event->description)
        <div class="max-w-5xl mx-auto px-4 mt-4">
            <p class="text-zinc-400 text-sm">{{ $event->description }}</p>
        </div>
    @endif

    {{-- ── Settled badge ─────────────────────────────────────────────────────── --}}
    @if ($cityEvent->isSettled())
        <div class="max-w-5xl mx-auto px-4 mt-4">
            <div class="bg-zinc-800 border border-zinc-700 text-zinc-300 text-sm rounded px-4 py-3 text-center font-semibold uppercase tracking-wider">
                This event has been settled
            </div>
        </div>
    @endif

    {{-- ── Odds / Pick section ────────────────────────────────────────────────── --}}
    @php
        $taxRate = 0.15;
        $selectedOptionId = (int) request('option_id', old('betting_option_id', $options->first()?->id));
        $canBet = auth()->check()
            && ! auth()->user()->is_admin
            && ! $cityEvent->isSettled()
            && $event->status->value === 'open'
            && $event->starts_at->isFuture()
            && auth()->user()->city_id === $city->id;
    @endphp

    <div class="max-w-5xl mx-auto px-4 mt-6">
        <h2 class="font-display font-black text-sm uppercase tracking-widest text-zinc-500 mb-3">
            @auth Pick your bet @else Choose your winner @endauth
        </h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
            @forelse ($options as $option)
                @php $isSelected = $selectedOptionId === $option->id; @endphp
                @if ($canBet)
                    <button type="button"
                            onclick="selectOption({{ $option->id }}, {{ $option->odds }})"
                            data-option-id="{{ $option->id }}"
                            data-odds="{{ $option->odds }}"
                            data-name="{{ $option->name }}"
                            class="odds-btn text-center rounded-lg px-4 py-4 border transition-all cursor-pointer w-full
                                   {{ $isSelected
                                       ? 'bg-red-700 border-red-500 ring-2 ring-red-500/30'
                                       : 'bg-[#1a1a1a] border-[#2a2a2a] hover:bg-[#222] hover:border-red-700/60' }}">
                        <p class="text-xs text-zinc-300 mb-1">{{ $option->name }}</p>
                        <p class="font-display font-black text-2xl text-white">{{ number_format((float) $option->odds, 2) }}<span class="text-sm text-zinc-400">x</span></p>
                    </button>
                @elseif (auth()->check())
                    <div class="text-center rounded-lg px-4 py-4 border bg-[#1a1a1a] border-[#2a2a2a]">
                        <p class="text-xs text-zinc-500 mb-1">{{ $option->name }}</p>
                        <p class="font-display font-black text-2xl text-white">{{ number_format((float) $option->odds, 2) }}<span class="text-sm text-zinc-500">x</span></p>
                    </div>
                @else
                    <a href="{{ route('login') }}"
                       class="block text-center rounded-lg px-4 py-4 border bg-[#1a1a1a] border-[#2a2a2a] hover:bg-[#222] hover:border-red-700/60 transition-colors">
                        <p class="text-xs text-zinc-500 mb-1">{{ $option->name }}</p>
                        <p class="font-display font-black text-2xl text-white">{{ number_format((float) $option->odds, 2) }}<span class="text-sm text-zinc-500">x</span></p>
                        <p class="text-[10px] text-red-500 mt-1.5 uppercase tracking-wider font-bold">Login to bet →</p>
                    </a>
                @endif
            @empty
                <p class="col-span-3 text-zinc-500 text-sm">No options yet.</p>
            @endforelse
        </div>
    </div>

    {{-- ── Bet section / Bet slip ─────────────────────────────────────────────── --}}
    <div class="max-w-5xl mx-auto px-4 mt-4 mb-10" id="bet-section">
        @auth
            @if (! auth()->user()->is_admin && ! $cityEvent->isSettled() && $event->status->value === 'open' && $event->starts_at->isFuture())

                @if (auth()->user()->city_id !== $city->id)
                    <div class="bg-yellow-900/30 border border-yellow-700/50 text-yellow-400 text-sm rounded-xl px-4 py-3">
                        You can only bet in your registered city ({{ auth()->user()->city?->name }}).
                    </div>

                @elseif (bccomp($availableBalance, '0', 2) <= 0)
                    <div class="bg-[#1a1a1a] border border-[#2a2a2a] rounded-xl p-6 text-center">
                        <p class="text-zinc-400 text-sm mb-4">Your wallet is empty. Deposit funds to start betting.</p>
                        <a href="{{ route('wallet.show') }}"
                           class="inline-block bg-red-600 hover:bg-red-500 text-white font-display font-black uppercase tracking-widest text-sm px-6 py-3 rounded-lg transition-colors">
                            Request Deposit
                        </a>
                    </div>

                @else
                    {{-- ── Bet slip ── --}}
                    <div class="bg-[#1a1a1a] border border-[#2a2a2a] rounded-xl overflow-hidden">
                        {{-- Slip header --}}
                        <div class="bg-[#222] border-b border-[#2a2a2a] px-5 py-3 flex items-center justify-between">
                            <span class="font-display font-black text-sm uppercase tracking-widest text-zinc-300">Bet Slip</span>
                            <span class="text-xs text-zinc-500">Balance: <span class="text-white font-semibold">{{ number_format((float) $availableBalance, 2) }} ETB</span></span>
                        </div>

                        <form method="POST" action="{{ route('cities.events.bets.store', [$city, $event]) }}" id="bet-form" class="p-5 space-y-4">
                            @csrf

                            {{-- Selected pick display --}}
                            <div class="bg-[#111] border border-[#2a2a2a] rounded-lg px-4 py-3 flex items-center justify-between">
                                <div>
                                    <p class="text-[10px] uppercase tracking-widest text-zinc-500 mb-0.5">Your Pick</p>
                                    <p class="font-display font-bold text-sm text-white" id="slip-pick-name">
                                        {{ $options->firstWhere('id', $selectedOptionId)?->name ?? $options->first()?->name ?? '—' }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] uppercase tracking-widest text-zinc-500 mb-0.5">Odds</p>
                                    <p class="font-display font-black text-xl text-red-400" id="slip-odds-display">
                                        {{ number_format((float) ($options->firstWhere('id', $selectedOptionId)?->odds ?? $options->first()?->odds ?? 0), 2) }}x
                                    </p>
                                </div>
                            </div>

                            {{-- Hidden select keeps the real value --}}
                            <select name="betting_option_id" id="betting_option_id" class="sr-only" required>
                                @foreach ($options as $option)
                                    <option value="{{ $option->id }}"
                                            data-odds="{{ $option->odds }}"
                                            data-name="{{ $option->name }}"
                                            @selected($selectedOptionId === $option->id)>
                                        {{ $option->name }}
                                    </option>
                                @endforeach
                            </select>

                            {{-- Stake input --}}
                            <div>
                                <label for="stake" class="block text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-1.5">
                                    Stake Amount (ETB)
                                </label>
                                <input type="number" step="1" min="1"
                                       max="{{ floor((float) $availableBalance) }}"
                                       name="stake" id="stake"
                                       value="{{ old('stake') }}"
                                       placeholder="e.g. 100"
                                       oninput="updateSlip()"
                                       class="w-full bg-[#111] border border-[#2a2a2a] text-white placeholder-zinc-600 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-red-600 transition-colors font-display font-bold text-lg"
                                       required>
                            </div>

                            {{-- Live payout breakdown --}}
                            <div id="slip-breakdown" class="hidden bg-[#111] border border-[#2a2a2a] rounded-lg overflow-hidden">
                                <div class="px-4 py-3 space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-zinc-400">Stake</span>
                                        <span class="text-white font-semibold" id="slip-stake-val">—</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-zinc-400">Odds</span>
                                        <span class="text-white font-semibold" id="slip-odds-val">—</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-zinc-400">Gross Payout</span>
                                        <span class="text-white font-semibold" id="slip-gross">—</span>
                                    </div>
                                    <div class="border-t border-[#2a2a2a] pt-2 flex justify-between text-sm">
                                        <span class="text-zinc-400">Tax (15% on profit)</span>
                                        <span class="text-red-400 font-semibold" id="slip-tax">—</span>
                                    </div>
                                </div>
                                <div class="bg-[#1a1a1a] border-t border-[#2a2a2a] px-4 py-3 flex justify-between items-center">
                                    <span class="font-display font-black text-sm uppercase tracking-wider text-zinc-300">Net Payout</span>
                                    <span class="font-display font-black text-xl text-green-400" id="slip-net">—</span>
                                </div>
                            </div>

                            <button type="submit"
                                    id="place-bet-btn"
                                    class="w-full bg-red-600 hover:bg-red-500 active:bg-red-700 text-white font-display font-black uppercase tracking-widest py-3.5 rounded-lg transition-colors text-sm disabled:opacity-40 disabled:cursor-not-allowed"
                                    disabled>
                                Place Bet
                            </button>

                            <p class="text-[10px] text-center text-zinc-600">
                                Tax is applied on winnings above your stake. Stake is deducted immediately.
                            </p>
                        </form>
                    </div>
                @endif

            @endif
        @else
            <div class="bg-[#1a1a1a] border border-[#2a2a2a] rounded-xl p-6 text-center">
                <p class="text-zinc-400 text-sm mb-4">Login or register to place a bet on this fight.</p>
                <div class="flex gap-3 justify-center">
                    <a href="{{ route('login') }}"
                       class="bg-[#2a2a2a] hover:bg-zinc-700 text-white font-semibold text-sm px-5 py-2.5 rounded-lg transition-colors">
                        Login
                    </a>
                    <a href="{{ route('register') }}"
                       class="bg-red-600 hover:bg-red-500 text-white font-display font-black uppercase tracking-widest text-sm px-5 py-2.5 rounded-lg transition-colors">
                        Register
                    </a>
                </div>
            </div>
        @endauth
    </div>

    @if ($canBet)
        <script>
            const TAX_RATE = {{ $taxRate }};
            const MAX_BAL  = {{ (float) $availableBalance }};

            function fmt(n) {
                return n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            function selectOption(id, odds) {
                // Highlight odds buttons
                document.querySelectorAll('.odds-btn').forEach(function (btn) {
                    const active = parseInt(btn.dataset.optionId) === id;
                    btn.classList.toggle('bg-red-700',        active);
                    btn.classList.toggle('border-red-500',    active);
                    btn.classList.toggle('ring-2',            active);
                    btn.classList.toggle('ring-red-500/30',   active);
                    btn.classList.toggle('bg-[#1a1a1a]',      !active);
                    btn.classList.toggle('border-[#2a2a2a]',  !active);
                });

                // Update hidden select
                const sel = document.getElementById('betting_option_id');
                const opt = sel ? sel.querySelector('option[value="' + id + '"]') : null;
                if (sel) sel.value = id;

                // Update slip pick + odds display
                const name = opt ? opt.dataset.name : '';
                const oddsVal = opt ? parseFloat(opt.dataset.odds) : odds;
                document.getElementById('slip-pick-name').textContent = name;
                document.getElementById('slip-odds-display').textContent = oddsVal.toFixed(2) + 'x';

                updateSlip();

                // Scroll to bet section
                document.getElementById('bet-section')?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }

            function updateSlip() {
                const stakeInput = document.getElementById('stake');
                const stake = parseFloat(stakeInput?.value) || 0;
                const sel = document.getElementById('betting_option_id');
                const opt = sel ? sel.options[sel.selectedIndex] : null;
                const odds = opt ? parseFloat(opt.dataset.odds) : 1;

                const breakdown = document.getElementById('slip-breakdown');
                const btn = document.getElementById('place-bet-btn');

                if (stake <= 0 || stake > MAX_BAL) {
                    breakdown?.classList.add('hidden');
                    if (btn) btn.disabled = true;
                    return;
                }

                const gross  = stake * odds;
                const profit = gross - stake;
                const tax    = profit * TAX_RATE;
                const net    = gross - tax;

                document.getElementById('slip-stake-val').textContent = fmt(stake) + ' ETB';
                document.getElementById('slip-odds-val').textContent   = odds.toFixed(2) + 'x';
                document.getElementById('slip-gross').textContent      = fmt(gross) + ' ETB';
                document.getElementById('slip-tax').textContent        = '−' + fmt(tax) + ' ETB';
                document.getElementById('slip-net').textContent        = fmt(net) + ' ETB';

                breakdown?.classList.remove('hidden');
                if (btn) btn.disabled = false;
            }

            document.addEventListener('DOMContentLoaded', function () {
                const sel = document.getElementById('betting_option_id');
                if (sel && sel.value) {
                    const opt = sel.options[sel.selectedIndex];
                    selectOption(parseInt(sel.value), parseFloat(opt?.dataset.odds || 1));
                }
                // Re-run slip if stake was pre-filled (e.g. after validation error)
                const stake = document.getElementById('stake');
                if (stake?.value) updateSlip();
            });
        </script>
    @endif

@endsection
