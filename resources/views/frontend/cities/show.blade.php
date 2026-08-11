@extends('frontend.layouts.app')

@section('title', $city->name . ' Fight Night')

@section('content')

    {{-- ── Event date strip ──────────────────────────────────────────────────── --}}
    <div class="bg-red-700 text-white text-center py-1.5 text-xs font-bold uppercase tracking-widest">
        ETFC Fight Night &nbsp;·&nbsp; September 5, 2026 &nbsp;·&nbsp; {{ $city->name }}
        @if ($city->venue)
            &nbsp;·&nbsp; {{ $city->venue }}
        @endif
    </div>

    {{-- ── Main Event Hero ───────────────────────────────────────────────────── --}}
    @if ($mainEvent)
        @php
            $me = $mainEvent->event;
            $bout = $me->bout;
            $red = $bout?->redFighter;
            $blue = $bout?->blueFighter;
            $redOpt = $mainEvent->bettingOptions->firstWhere('position', 1);
            $blueOpt = $mainEvent->bettingOptions->firstWhere('position', 2);
            $eventUrl = route('cities.events.show', [$city, $me]);
            $loginUrl = route('login');
        @endphp
        <div class="relative overflow-hidden bg-[#0d0d0d]">
            <div class="absolute inset-0 flex">
                <div class="w-1/2 bg-gradient-to-r from-red-900/30 to-transparent"></div>
                <div class="w-1/2 bg-gradient-to-l from-blue-900/30 to-transparent"></div>
            </div>

            <div class="relative max-w-5xl mx-auto px-4">
                <div class="text-center pt-6 pb-2">
                    <span class="inline-block bg-red-600 text-white text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded">
                        {{ $bout?->sport ?? 'Main Event' }} · Main Event
                    </span>
                </div>

                <div class="flex items-end justify-between gap-4 pt-4">
                    {{-- Red corner --}}
                    <div class="flex-1 flex flex-col items-center text-center">
                        <div class="relative">
                            @if ($red?->image_path)
                                <img src="{{ asset('storage/'.$red->image_path) }}"
                                     alt="{{ $red->name }}"
                                     class="w-36 h-36 sm:w-48 sm:h-48 object-cover object-top rounded-lg shadow-2xl shadow-red-900/50 border-2 border-red-700/50">
                            @else
                                <div class="w-36 h-36 sm:w-48 sm:h-48 rounded-lg bg-red-900/30 border-2 border-red-700/40 flex items-center justify-center">
                                    <span class="font-display font-black text-4xl text-red-400">
                                        {{ mb_substr($red?->name ?? '?', 0, 1) }}
                                    </span>
                                </div>
                            @endif
                            <div class="absolute -top-2 -left-2 bg-red-600 text-white text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded">Red</div>
                        </div>
                        <h2 class="font-display font-black text-xl sm:text-2xl uppercase tracking-wide mt-3 text-white leading-tight">
                            {{ $red?->name ?? '—' }}
                        </h2>
                        @if ($redOpt)
                            @auth
                                <button type="button"
                                        data-bs-ceid="{{ $mainEvent->id }}"
                                        data-bs-oid="{{ $redOpt->id }}"
                                        data-city-id="{{ $city->id }}"
                                        data-event-title="{{ $me->title }}"
                                        data-option-name="{{ $redOpt->name }}"
                                        data-odds="{{ (float) $redOpt->odds }}"
                                        onclick="BetSlip.addFromButton(this)"
                                        class="mt-2 inline-block bg-red-600 hover:bg-red-500 active:bg-red-700 text-white font-display font-black text-sm px-4 py-1.5 rounded transition-colors cursor-pointer">
                                    {{ number_format((float) $redOpt->odds, 2) }}x
                                </button>
                            @else
                                <a href="{{ $loginUrl }}"
                                   class="mt-2 inline-block bg-red-600 hover:bg-red-500 active:bg-red-700 text-white font-display font-black text-sm px-4 py-1.5 rounded transition-colors cursor-pointer">
                                    {{ number_format((float) $redOpt->odds, 2) }}x
                                </a>
                            @endauth
                        @endif
                    </div>

                    {{-- VS divider --}}
                    <div class="flex flex-col items-center gap-1 pb-12 shrink-0">
                        <div class="w-px h-8 bg-zinc-700"></div>
                        <span class="font-display font-black text-2xl text-zinc-500 tracking-widest">VS</span>
                        <div class="w-px h-8 bg-zinc-700"></div>
                    </div>

                    {{-- Blue corner --}}
                    <div class="flex-1 flex flex-col items-center text-center">
                        <div class="relative">
                            @if ($blue?->image_path)
                                <img src="{{ asset('storage/'.$blue->image_path) }}"
                                     alt="{{ $blue->name }}"
                                     class="w-36 h-36 sm:w-48 sm:h-48 object-cover object-top rounded-lg shadow-2xl shadow-blue-900/50 border-2 border-blue-700/50">
                            @else
                                <div class="w-36 h-36 sm:w-48 sm:h-48 rounded-lg bg-blue-900/30 border-2 border-blue-700/40 flex items-center justify-center">
                                    <span class="font-display font-black text-4xl text-blue-400">
                                        {{ mb_substr($blue?->name ?? '?', 0, 1) }}
                                    </span>
                                </div>
                            @endif
                            <div class="absolute -top-2 -right-2 bg-blue-700 text-white text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded">Blue</div>
                        </div>
                        <h2 class="font-display font-black text-xl sm:text-2xl uppercase tracking-wide mt-3 text-white leading-tight">
                            {{ $blue?->name ?? '—' }}
                        </h2>
                        @if ($blueOpt)
                            @auth
                                <button type="button"
                                        data-bs-ceid="{{ $mainEvent->id }}"
                                        data-bs-oid="{{ $blueOpt->id }}"
                                        data-city-id="{{ $city->id }}"
                                        data-event-title="{{ $me->title }}"
                                        data-option-name="{{ $blueOpt->name }}"
                                        data-odds="{{ (float) $blueOpt->odds }}"
                                        onclick="BetSlip.addFromButton(this)"
                                        class="mt-2 inline-block bg-blue-700 hover:bg-blue-600 active:bg-blue-800 text-white font-display font-black text-sm px-4 py-1.5 rounded transition-colors cursor-pointer">
                                    {{ number_format((float) $blueOpt->odds, 2) }}x
                                </button>
                            @else
                                <a href="{{ $loginUrl }}"
                                   class="mt-2 inline-block bg-blue-700 hover:bg-blue-600 active:bg-blue-800 text-white font-display font-black text-sm px-4 py-1.5 rounded transition-colors cursor-pointer">
                                    {{ number_format((float) $blueOpt->odds, 2) }}x
                                </a>
                            @endauth
                        @endif
                    </div>
                </div>

                {{-- Draw / extra odds --}}
                @php
                    $extraMainOpts = $mainEvent->bettingOptions->reject(fn ($o) => in_array($o->position, [1, 2]))->take(1);
                @endphp
                <div class="text-center py-5 flex flex-col items-center gap-3">
                    @foreach ($extraMainOpts as $opt)
                        @auth
                            <button type="button"
                                    data-bs-ceid="{{ $mainEvent->id }}"
                                    data-bs-oid="{{ $opt->id }}"
                                    data-city-id="{{ $city->id }}"
                                    data-event-title="{{ $me->title }}"
                                    data-option-name="{{ $opt->name }}"
                                    data-odds="{{ (float) $opt->odds }}"
                                    onclick="BetSlip.addFromButton(this)"
                                    class="inline-flex flex-col items-center bg-[#2a2a2a] hover:bg-zinc-700 border border-[#3a3a3a] hover:border-zinc-500 text-white rounded-lg px-5 py-2 transition-colors cursor-pointer">
                                <span class="text-[9px] text-zinc-400 uppercase tracking-wider">{{ $opt->name }}</span>
                                <span class="font-display font-black text-base">{{ number_format((float) $opt->odds, 2) }}x</span>
                            </button>
                        @else
                            <a href="{{ $loginUrl }}"
                               class="inline-flex flex-col items-center bg-[#2a2a2a] hover:bg-zinc-700 border border-[#3a3a3a] hover:border-zinc-500 text-white rounded-lg px-5 py-2 transition-colors">
                                <span class="text-[9px] text-zinc-400 uppercase tracking-wider">{{ $opt->name }}</span>
                                <span class="font-display font-black text-base">{{ number_format((float) $opt->odds, 2) }}x</span>
                            </a>
                        @endauth
                    @endforeach
                    <a href="{{ $eventUrl }}"
                       class="text-xs text-zinc-500 hover:text-zinc-300 transition-colors uppercase tracking-wider font-semibold">
                        View all options →
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- ── Full Fight Card ───────────────────────────────────────────────────── --}}
    <div class="max-w-5xl mx-auto px-4 py-8">

        <div class="flex items-center gap-3 mb-5">
            <div class="h-px flex-1 bg-[#2a2a2a]"></div>
            <h2 class="font-display font-black text-lg uppercase tracking-widest text-zinc-400">Full Fight Card</h2>
            <div class="h-px flex-1 bg-[#2a2a2a]"></div>
        </div>

        @forelse ($fightCard as $cityEvent)
            @php
                $event = $cityEvent->event;
                $bout = $event->bout;
                $red = $bout?->redFighter;
                $blue = $bout?->blueFighter;
                $eventUrl = route('cities.events.show', [$city, $event]);
                $loginUrl = route('login');
            @endphp
            <div class="group flex items-center gap-3 bg-[#1a1a1a] border border-[#2a2a2a] rounded-lg px-4 py-3 mb-3 hover:border-[#3a3a3a] transition-colors">

                {{-- Sport badge --}}
                <div class="shrink-0 w-14 text-center">
                    <span class="font-display font-bold text-[10px] uppercase tracking-wide text-zinc-500 bg-[#2a2a2a] px-2 py-0.5 rounded">
                        {{ $bout?->sport ?? '—' }}
                    </span>
                </div>

                {{-- Fighter names link to event --}}
                <a href="{{ $eventUrl }}"
                   class="flex-1 flex items-center gap-2 min-w-0 hover:text-red-400 transition-colors">
                    @if ($bout && ($red || $blue))
                        <div class="flex items-center gap-2 flex-1 min-w-0">
                            @if ($red?->image_path)
                                <img src="{{ asset('storage/'.$red->image_path) }}"
                                     alt="{{ $red->name }}"
                                     class="w-8 h-8 rounded object-cover object-top shrink-0 border border-red-700/40">
                            @else
                                <div class="w-8 h-8 rounded bg-red-900/30 border border-red-700/30 flex items-center justify-center shrink-0">
                                    <span class="font-display font-black text-xs text-red-400">{{ mb_substr($red?->name ?? '?', 0, 1) }}</span>
                                </div>
                            @endif
                            <span class="font-display font-bold text-sm uppercase tracking-wide text-white truncate">
                                {{ $red?->name ?? '—' }}
                            </span>
                        </div>

                        <span class="font-display font-black text-xs text-zinc-600 shrink-0">VS</span>

                        <div class="flex items-center gap-2 flex-1 min-w-0 justify-end">
                            <span class="font-display font-bold text-sm uppercase tracking-wide text-white truncate text-right">
                                {{ $blue?->name ?? '—' }}
                            </span>
                            @if ($blue?->image_path)
                                <img src="{{ asset('storage/'.$blue->image_path) }}"
                                     alt="{{ $blue->name }}"
                                     class="w-8 h-8 rounded object-cover object-top shrink-0 border border-blue-700/40">
                            @else
                                <div class="w-8 h-8 rounded bg-blue-900/30 border border-blue-700/30 flex items-center justify-center shrink-0">
                                    <span class="font-display font-black text-xs text-blue-400">{{ mb_substr($blue?->name ?? '?', 0, 1) }}</span>
                                </div>
                            @endif
                        </div>
                    @else
                        <span class="font-display font-bold text-sm uppercase tracking-wide text-white truncate">
                            {{ $event->title }}
                        </span>
                    @endif
                </a>

                {{-- Odds chips — max 3, then +N badge --}}
                @php
                    $visibleOpts = $cityEvent->bettingOptions->take(3);
                    $extraCount  = $cityEvent->bettingOptions->count() - $visibleOpts->count();
                @endphp
                <div class="shrink-0 flex flex-wrap justify-end gap-1.5 ml-2 max-w-[150px]">
                    @foreach ($visibleOpts as $option)
                        @php
                            $label = Str::before($option->name, ' Wins') ?: $option->name;
                        @endphp
                        @auth
                            <button type="button"
                                    data-bs-ceid="{{ $cityEvent->id }}"
                                    data-bs-oid="{{ $option->id }}"
                                    data-city-id="{{ $city->id }}"
                                    data-event-title="{{ $event->title }}"
                                    data-option-name="{{ $option->name }}"
                                    data-odds="{{ (float) $option->odds }}"
                                    onclick="BetSlip.addFromButton(this)"
                                    class="inline-flex flex-col items-center bg-[#2a2a2a] hover:bg-red-700 active:bg-red-800 border border-[#3a3a3a] hover:border-red-600 text-white rounded px-2 py-1 transition-colors text-center min-w-[48px] cursor-pointer">
                                <span class="text-[9px] text-zinc-400 leading-tight">{{ $label }}</span>
                                <span class="font-display font-black text-xs leading-tight">{{ number_format((float) $option->odds, 2) }}x</span>
                            </button>
                        @else
                            <a href="{{ $loginUrl }}"
                               class="inline-flex flex-col items-center bg-[#2a2a2a] hover:bg-red-700 active:bg-red-800 border border-[#3a3a3a] hover:border-red-600 text-white rounded px-2 py-1 transition-colors text-center min-w-[48px]">
                                <span class="text-[9px] text-zinc-400 leading-tight">{{ $label }}</span>
                                <span class="font-display font-black text-xs leading-tight">{{ number_format((float) $option->odds, 2) }}x</span>
                            </a>
                        @endauth
                    @endforeach
                    @if ($extraCount > 0)
                        <a href="{{ $eventUrl }}"
                           class="inline-flex items-center justify-center bg-[#2a2a2a] border border-[#3a3a3a] hover:border-red-600 text-zinc-500 hover:text-red-400 rounded px-2 py-1 text-[9px] font-bold transition-colors min-w-[40px]">
                            +{{ $extraCount }}
                        </a>
                    @endif
                </div>
            </div>
        @empty
            @if (!$mainEvent)
                <p class="text-center text-zinc-500 py-8">No open events right now. Check back soon.</p>
            @endif
        @endforelse

    </div>

@endsection
