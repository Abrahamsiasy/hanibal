<!DOCTYPE html>
<html lang="en" class="bg-[#0d0d0d]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#0d0d0d] text-white min-h-screen font-sans antialiased pb-16 lg:pb-0">

    {{-- Top Nav --}}
    <nav class="sticky top-0 z-50 bg-[#111] border-b border-[#2a2a2a]">
        <div class="max-w-5xl mx-auto px-4 h-14 flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="font-display font-black text-xl uppercase tracking-wider text-white">
                {{ config('app.name') }}
            </a>

            {{-- Desktop nav --}}
            <div class="hidden lg:flex items-center gap-6 text-sm font-medium">
                @auth
                    @if (! auth()->user()->is_admin && auth()->user()->city)
                        <a href="{{ route('cities.show', auth()->user()->city) }}"
                           class="text-zinc-400 hover:text-white transition-colors">Fight Card</a>
                    @endif
                    <a href="{{ route('wallet.show') }}" class="text-zinc-400 hover:text-white transition-colors">Wallet</a>
                    <a href="{{ route('bets.index') }}" class="text-zinc-400 hover:text-white transition-colors">My Bets</a>
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="text-zinc-500 hover:text-white transition-colors text-sm">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-zinc-400 hover:text-white transition-colors">Login</a>
                    <a href="{{ route('register') }}"
                       class="bg-red-600 hover:bg-red-500 text-white font-semibold px-4 py-1.5 rounded transition-colors text-sm">
                        Register
                    </a>
                @endauth
            </div>

            {{-- Mobile header right --}}
            @auth
                <div class="lg:hidden flex items-center gap-3">
                    <a href="{{ route('wallet.show') }}" class="text-xs text-zinc-400">Wallet</a>
                </div>
            @else
                <div class="lg:hidden flex items-center gap-2">
                    <a href="{{ route('login') }}" class="text-sm text-zinc-400">Login</a>
                    <a href="{{ route('register') }}"
                       class="bg-red-600 text-white text-xs font-bold px-3 py-1.5 rounded">Register</a>
                </div>
            @endauth
        </div>
    </nav>

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="max-w-5xl mx-auto px-4 pt-4">
            <div class="bg-green-900/50 border border-green-700 text-green-300 rounded px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="max-w-5xl mx-auto px-4 pt-4">
            <div class="bg-red-900/50 border border-red-700 text-red-300 rounded px-4 py-3 text-sm">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- Page content --}}
    <main>
        @yield('content')
    </main>

    {{-- Mobile bottom nav --}}
    <nav class="lg:hidden fixed bottom-0 inset-x-0 z-50 bg-[#111] border-t border-[#2a2a2a] flex">
        @auth
            @if (! auth()->user()->is_admin && auth()->user()->city)
                <a href="{{ route('cities.show', auth()->user()->city) }}"
                   class="flex-1 flex flex-col items-center justify-center py-2 text-zinc-400 hover:text-white transition-colors">
                    <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span class="text-[10px] uppercase tracking-wide font-semibold">Home</span>
                </a>
            @endif
            <a href="{{ route('bets.index') }}"
               class="flex-1 flex flex-col items-center justify-center py-2 text-zinc-400 hover:text-white transition-colors">
                <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span class="text-[10px] uppercase tracking-wide font-semibold">My Bets</span>
            </a>
            <a href="{{ route('wallet.show') }}"
               class="flex-1 flex flex-col items-center justify-center py-2 text-zinc-400 hover:text-white transition-colors">
                <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                <span class="text-[10px] uppercase tracking-wide font-semibold">Wallet</span>
            </a>
            <form method="POST" action="{{ route('logout') }}" class="flex-1">
                @csrf
                <button type="submit"
                        class="w-full h-full flex flex-col items-center justify-center py-2 text-zinc-500 hover:text-white transition-colors">
                    <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span class="text-[10px] uppercase tracking-wide font-semibold">Logout</span>
                </button>
            </form>
        @else
            <a href="{{ route('login') }}"
               class="flex-1 flex flex-col items-center justify-center py-2 text-zinc-400 hover:text-white transition-colors">
                <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                <span class="text-[10px] uppercase tracking-wide font-semibold">Login</span>
            </a>
            <a href="{{ route('register') }}"
               class="flex-1 flex flex-col items-center justify-center py-2 bg-red-600 text-white">
                <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                <span class="text-[10px] uppercase tracking-wide font-semibold">Register</span>
            </a>
        @endauth
    </nav>

    {{-- ── Floating Bet Slip (logged-in, non-admin customers only) ────────────── --}}
    {{--
        Children stack top→bottom. The div is bottom-anchored (fixed bottom:64px).
        ORDER: body → footer → toggle tab
        So the tab (last child) sits at the viewport bottom; body grows upward.
    --}}
    @auth
        @if (! auth()->user()->is_admin)
        <div id="bet-slip"
             style="position:fixed;bottom:64px;left:0;right:0;z-index:9999;pointer-events:none;">
            {{-- Inner container: flex column, capped height so it never covers the top nav --}}
            <div style="max-width:26rem;margin-left:auto;pointer-events:auto;
                        display:flex;flex-direction:column;max-height:calc(100vh - 130px);overflow:hidden;">

                {{-- ① Rows body — scrollable list of selections --}}
                <div id="bs-body"
                     style="display:none;flex:1;min-height:0;background:#111;border:1px solid #2a2a2a;
                            border-bottom:none;overflow-y:auto;box-shadow:0 -8px 32px rgba(0,0,0,.8);">
                    <div id="bs-rows"></div>
                    {{-- Empty state --}}
                    <div id="bs-empty"
                         style="padding:2rem 1rem;text-align:center;color:#71717a;font-size:.85rem;line-height:1.6;">
                        No selections yet.<br>
                        <span style="font-size:.75rem;color:#52525b;">Tap any odds to add a bet.</span>
                    </div>
                </div>

                {{-- ② Footer — single stake input + totals + submit --}}
                <div id="bs-footer"
                     style="display:none;flex-shrink:0;background:#1a1a1a;border:1px solid #2a2a2a;
                            border-bottom:none;border-top:1px solid #3a3a3a;padding:.75rem 1rem;
                            box-shadow:0 -4px 16px rgba(0,0,0,.6);overflow-y:auto;max-height:55vh;">

                    {{-- Stake per bet input --}}
                    <div style="margin-bottom:.625rem;">
                        <label style="display:block;font-size:.6rem;text-transform:uppercase;letter-spacing:.1em;color:#71717a;margin-bottom:.3rem;">
                            Stake per bet (ETB)
                        </label>
                        <input id="bs-stake-input"
                               type="number" min="1" step="1" placeholder="e.g. 100"
                               oninput="window.BetSlip.setStake(this.value)"
                               style="width:100%;box-sizing:border-box;background:#0d0d0d;border:1px solid #3a3a3a;
                                      color:#fff;border-radius:.375rem;padding:.5rem .75rem;font-size:1rem;
                                      font-weight:700;outline:none;"
                               onfocus="this.style.borderColor='#dc2626'"
                               onblur="this.style.borderColor='#3a3a3a'">
                    </div>

                    {{-- Singles totals summary --}}
                    <div style="background:#111;border:1px solid #2a2a2a;border-radius:.5rem;overflow:hidden;margin-bottom:.625rem;">
                        <div style="padding:.4rem .75rem;display:flex;justify-content:space-between;border-bottom:1px solid #2a2a2a;">
                            <span style="font-size:.65rem;color:#71717a;text-transform:uppercase;letter-spacing:.06em;">Total Bets</span>
                            <span id="bs-total-bets" style="font-size:.75rem;font-weight:700;color:#e4e4e7;">0</span>
                        </div>
                        <div style="padding:.4rem .75rem;display:flex;justify-content:space-between;border-bottom:1px solid #2a2a2a;">
                            <span style="font-size:.65rem;color:#71717a;text-transform:uppercase;letter-spacing:.06em;">Total Stake</span>
                            <span id="bs-total-stake" style="font-size:.75rem;font-weight:700;color:#e4e4e7;">0 ETB</span>
                        </div>
                        <div style="padding:.4rem .75rem;display:flex;justify-content:space-between;border-bottom:1px solid #2a2a2a;">
                            <span style="font-size:.65rem;color:#71717a;text-transform:uppercase;letter-spacing:.06em;">Total Tax (15%)</span>
                            <span id="bs-total-tax" style="font-size:.75rem;font-weight:700;color:#f87171;">−0 ETB</span>
                        </div>
                        <div style="padding:.5rem .75rem;display:flex;justify-content:space-between;background:#0d0d0d;">
                            <span style="font-size:.7rem;color:#a1a1aa;text-transform:uppercase;letter-spacing:.06em;font-weight:700;">Est. Total Net</span>
                            <span id="bs-total-net" style="font-size:.9rem;font-weight:900;color:#4ade80;">0 ETB</span>
                        </div>
                    </div>

                    {{-- Multi / Parlay combined odds (shown when 2+ bets selected) --}}
                    <div id="bs-parlay-box"
                         style="display:none;background:#1c1400;border:1px solid #78350f;border-radius:.5rem;
                                overflow:hidden;margin-bottom:.625rem;">
                        <div style="padding:.4rem .75rem;display:flex;align-items:center;gap:.5rem;border-bottom:1px solid #78350f;">
                            <svg style="width:.85rem;height:.85rem;flex-shrink:0;" fill="none" stroke="#fbbf24" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            <span style="font-size:.65rem;text-transform:uppercase;letter-spacing:.1em;color:#fbbf24;font-weight:900;">Combo Multiplier</span>
                        </div>
                        <div style="padding:.4rem .75rem;display:flex;justify-content:space-between;border-bottom:1px solid #78350f;">
                            <span style="font-size:.65rem;color:#92400e;text-transform:uppercase;letter-spacing:.06em;">Combined Odds</span>
                            <span id="bs-combined-odds" style="font-size:.8rem;font-weight:900;color:#fbbf24;">1.00x</span>
                        </div>
                        <div style="padding:.5rem .75rem;display:flex;justify-content:space-between;background:#0d0d0d;">
                            <span style="font-size:.65rem;color:#92400e;text-transform:uppercase;letter-spacing:.06em;">If ALL Win (net)</span>
                            <span id="bs-combined-net" style="font-size:.85rem;font-weight:900;color:#fbbf24;">— ETB</span>
                        </div>
                    </div>

                    <button onclick="window.BetSlip.submit()" id="bs-submit"
                            style="width:100%;background:#dc2626;color:#fff;border:none;border-radius:.5rem;
                                   padding:.75rem 1rem;font-size:.8rem;font-weight:900;letter-spacing:.12em;
                                   text-transform:uppercase;cursor:pointer;">
                        Place Bets
                    </button>
                    <div id="bs-msg" style="display:none;margin-top:.5rem;font-size:.75rem;text-align:center;"></div>
                </div>

                {{-- ③ Toggle tab — LAST child, always visible at the bottom --}}
                <button onclick="window.BetSlip.toggle()"
                        style="flex-shrink:0;width:100%;display:flex;align-items:center;justify-content:space-between;
                               padding:.875rem 1.25rem;background:#991b1b;border:none;cursor:pointer;
                               box-shadow:0 -4px 24px rgba(0,0,0,.7);">
                    <div style="display:flex;align-items:center;gap:.625rem;">
                        <svg style="width:1.1rem;height:1.1rem;flex-shrink:0;" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                        <span style="color:#fff;font-size:.8rem;font-weight:900;letter-spacing:.12em;text-transform:uppercase;">Bet Slip</span>
                        <span id="bs-badge"
                              style="background:#fff;color:#991b1b;font-size:.7rem;font-weight:900;border-radius:9999px;
                                     min-width:1.25rem;height:1.25rem;display:inline-flex;align-items:center;
                                     justify-content:center;padding:0 .3rem;line-height:1;">
                            0
                        </span>
                    </div>
                    <div style="display:flex;align-items:center;gap:.5rem;">
                        <span id="bs-combo-badge" style="display:none;background:#fbbf24;color:#000;font-size:.6rem;
                              font-weight:900;border-radius:9999px;padding:.15rem .5rem;letter-spacing:.06em;
                              text-transform:uppercase;">COMBO</span>
                        <svg id="bs-chevron"
                             style="width:1rem;height:1rem;flex-shrink:0;transition:transform .25s;transform:rotate(180deg);"
                             fill="none" stroke="rgba(255,255,255,.75)" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/>
                        </svg>
                    </div>
                </button>
            </div>
        </div>

        <script>
        window.BetSlip = (function () {
            var TAX    = 0.15;
            var _sel   = {};      // { cityEventId: { cityEventId, cityId, eventTitle, optionId, optionName, odds } }
            var _stake = '';      // single stake applied to every bet
            var _exp   = {};      // { cityEventId: true } — expanded rows
            var _open  = false;

            /* ── helpers ──────────────────────────────────────────────── */
            function _fmt(n) {
                return n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
            function _esc(s) {
                return String(s)
                    .replace(/&/g,'&amp;').replace(/</g,'&lt;')
                    .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
            }
            function _count() { return Object.keys(_sel).length; }
            function _stakeNum() { return Math.max(0, parseFloat(_stake) || 0); }
            function _gross(odds) { return _stakeNum() * odds; }
            function _tax(odds)   { var g = _gross(odds); return (g - _stakeNum()) * TAX; }
            function _net(odds)   { return _gross(odds) - _tax(odds); }

            /* ── badge ────────────────────────────────────────────────── */
            function _badge() {
                var el = document.getElementById('bs-badge');
                if (el) el.textContent = _count();
            }

            /* ── totals in footer ─────────────────────────────────────── */
            function _totals() {
                var n     = _count();
                var stake = _stakeNum();
                var ts    = stake * n;
                var ttax  = 0, tnet = 0;
                var combinedOdds = 1;
                Object.values(_sel).forEach(function (s) {
                    ttax += _tax(s.odds);
                    tnet += _net(s.odds);
                    combinedOdds *= s.odds;
                });

                var betsEl  = document.getElementById('bs-total-bets');
                var stakeEl = document.getElementById('bs-total-stake');
                var taxEl   = document.getElementById('bs-total-tax');
                var netEl   = document.getElementById('bs-total-net');
                var btnEl   = document.getElementById('bs-submit');

                if (betsEl)  betsEl.textContent  = n + ' bet' + (n !== 1 ? 's' : '');
                if (stakeEl) stakeEl.textContent  = _fmt(ts) + ' ETB';
                if (taxEl)   taxEl.textContent    = '\u2212' + _fmt(ttax) + ' ETB';
                if (netEl)   netEl.textContent    = _fmt(tnet) + ' ETB';
                if (btnEl)   btnEl.textContent    = 'Place ' + n + ' Bet' + (n !== 1 ? 's' : '');

                /* ── combo / parlay display ───────────────────────────── */
                var parlayBox   = document.getElementById('bs-parlay-box');
                var combOddsEl  = document.getElementById('bs-combined-odds');
                var combNetEl   = document.getElementById('bs-combined-net');
                var comboBadge  = document.getElementById('bs-combo-badge');

                if (n >= 2) {
                    var comboGross  = stake * combinedOdds;
                    var comboProfit = comboGross - stake;
                    var comboTax    = comboProfit * TAX;
                    var comboNet    = comboGross - comboTax;

                    if (parlayBox)  parlayBox.style.display  = 'block';
                    if (comboBadge) comboBadge.style.display = 'inline-flex';
                    if (combOddsEl) combOddsEl.textContent   = combinedOdds.toFixed(2) + 'x';
                    if (combNetEl)  combNetEl.textContent    = stake > 0 ? _fmt(comboNet) + ' ETB' : '\u2014 ETB';
                } else {
                    if (parlayBox)  parlayBox.style.display  = 'none';
                    if (comboBadge) comboBadge.style.display = 'none';
                }
            }

            /* ── render rows ──────────────────────────────────────────── */
            function _renderRows() {
                var rows   = document.getElementById('bs-rows');
                var empty  = document.getElementById('bs-empty');
                var footer = document.getElementById('bs-footer');
                if (!rows) return;

                var keys = Object.keys(_sel);

                if (keys.length === 0) {
                    rows.innerHTML = '';
                    if (empty)  empty.style.display  = 'block';
                    if (footer) footer.style.display = 'none';
                    _totals();
                    return;
                }

                if (empty)  empty.style.display  = 'none';
                if (footer) footer.style.display = 'block';

                var stake     = _stakeNum();
                var hasStake  = stake > 0;

                rows.innerHTML = keys.map(function (key) {
                    var s         = _sel[key];
                    var isExp     = !!_exp[key];
                    var gross     = _gross(s.odds);
                    var tax       = _tax(s.odds);
                    var net       = _net(s.odds);
                    var chevron   = isExp ? '&#9650;' : '&#9660;';   /* ▲ ▼ */

                    var breakdown = isExp
                        ? '<div style="background:#0d0d0d;border-top:1px solid #2a2a2a;padding:.6rem .9rem;font-size:.75rem;">'
                        +   '<div style="display:flex;justify-content:space-between;margin-bottom:.3rem;">'
                        +     '<span style="color:#71717a;">Stake</span>'
                        +     '<span style="color:#e4e4e7;font-weight:600;">' + (hasStake ? _fmt(stake) + ' ETB' : '\u2014') + '</span>'
                        +   '</div>'
                        +   '<div style="display:flex;justify-content:space-between;margin-bottom:.3rem;">'
                        +     '<span style="color:#71717a;">Odds</span>'
                        +     '<span style="color:#fca5a5;font-weight:700;">' + s.odds.toFixed(2) + 'x</span>'
                        +   '</div>'
                        +   '<div style="display:flex;justify-content:space-between;margin-bottom:.3rem;">'
                        +     '<span style="color:#71717a;">Gross Payout</span>'
                        +     '<span style="color:#e4e4e7;font-weight:600;">' + (hasStake ? _fmt(gross) + ' ETB' : '\u2014') + '</span>'
                        +   '</div>'
                        +   '<div style="display:flex;justify-content:space-between;margin-bottom:.4rem;">'
                        +     '<span style="color:#71717a;">Tax (15% on profit)</span>'
                        +     '<span style="color:#f87171;font-weight:600;">' + (hasStake ? '\u2212' + _fmt(tax) + ' ETB' : '\u2014') + '</span>'
                        +   '</div>'
                        +   '<div style="display:flex;justify-content:space-between;border-top:1px solid #2a2a2a;padding-top:.4rem;">'
                        +     '<span style="color:#a1a1aa;font-weight:700;text-transform:uppercase;font-size:.65rem;letter-spacing:.06em;">Net Payout</span>'
                        +     '<span style="color:#4ade80;font-weight:900;font-size:.9rem;">' + (hasStake ? _fmt(net) + ' ETB' : '\u2014') + '</span>'
                        +   '</div>'
                        + '</div>'
                        : '';

                    return '<div style="border-bottom:1px solid #2a2a2a;">'
                        /* clickable row header */
                        +   '<div onclick="window.BetSlip.toggleRow(' + s.cityEventId + ')"'
                        +       ' style="display:flex;align-items:center;padding:.65rem .9rem;cursor:pointer;gap:.5rem;"'
                        +       ' onmouseover="this.style.background=\'#1a1a1a\'" onmouseout="this.style.background=\'\'">'
                        +     '<div style="flex:1;min-width:0;">'
                        +       '<p style="font-size:.6rem;text-transform:uppercase;letter-spacing:.08em;color:#71717a;margin:0 0 .15rem;">'
                        +         _esc(s.eventTitle)
                        +       '</p>'
                        +       '<p style="font-size:.875rem;font-weight:700;color:#fff;margin:0;">'
                        +         _esc(s.optionName)
                        +         '<span style="font-weight:900;color:#fca5a5;margin-left:.4rem;">' + s.odds.toFixed(2) + 'x</span>'
                        +       '</p>'
                        +     '</div>'
                        +     '<span style="font-size:.65rem;color:#71717a;margin-right:.25rem;">' + chevron + '</span>'
                        +     '<button onclick="event.stopPropagation();window.BetSlip.remove(' + s.cityEventId + ')"'
                        +             ' style="background:none;border:none;cursor:pointer;color:#52525b;padding:.2rem;font-size:0;flex-shrink:0;"'
                        +             ' title="Remove">'
                        +       '<svg style="width:.8rem;height:.8rem;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">'
                        +         '<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>'
                        +       '</svg>'
                        +     '</button>'
                        +   '</div>'
                        /* expanded breakdown */
                        +   breakdown
                        + '</div>';
                }).join('');

                _totals();
            }

            /* ── button highlight on page ─────────────────────────────── */
            function _markButtons(cityEventId, selectedOptionId) {
                document.querySelectorAll('[data-bs-ceid="' + cityEventId + '"]').forEach(function (btn) {
                    var sel = selectedOptionId !== null && String(btn.dataset.bsOid) === String(selectedOptionId);
                    btn.style.background    = sel ? '#dc2626' : '';
                    btn.style.borderColor   = sel ? '#dc2626' : '';
                    btn.style.color         = sel ? '#fff'    : '';
                    btn.style.outline       = sel ? '2px solid rgba(252,165,165,.6)' : '';
                    btn.style.outlineOffset = sel ? '-2px' : '';
                });
            }

            /* ── expand / collapse panel ──────────────────────────────── */
            function _expand() {
                _open = true;
                var body    = document.getElementById('bs-body');
                var chevron = document.getElementById('bs-chevron');
                if (body)    body.style.display    = 'block';
                if (chevron) chevron.style.transform = 'rotate(0deg)';
            }
            function _collapse() {
                _open = false;
                var body    = document.getElementById('bs-body');
                var footer  = document.getElementById('bs-footer');
                var chevron = document.getElementById('bs-chevron');
                if (body)    body.style.display    = 'none';
                if (footer)  footer.style.display  = 'none';
                if (chevron) chevron.style.transform = 'rotate(180deg)';
            }

            /* ── public API ───────────────────────────────────────────── */
            return {

                addFromButton: function (btn) {
                    this.add(
                        parseInt(btn.dataset.bsCeid),
                        parseInt(btn.dataset.cityId),
                        btn.dataset.eventTitle,
                        parseInt(btn.dataset.bsOid),
                        btn.dataset.optionName,
                        parseFloat(btn.dataset.odds)
                    );
                },

                add: function (cityEventId, cityId, eventTitle, optionId, optionName, odds) {
                    _sel[cityEventId] = {
                        cityEventId : cityEventId,
                        cityId      : cityId,
                        eventTitle  : eventTitle,
                        optionId    : optionId,
                        optionName  : optionName,
                        odds        : parseFloat(odds),
                    };
                    _badge();
                    _renderRows();
                    _markButtons(cityEventId, optionId);
                    if (!_open) _expand();
                },

                remove: function (cityEventId) {
                    _markButtons(cityEventId, null);
                    delete _sel[cityEventId];
                    delete _exp[cityEventId];
                    _badge();
                    _renderRows();
                    if (_count() === 0) _collapse();
                },

                toggleRow: function (cityEventId) {
                    if (_exp[cityEventId]) delete _exp[cityEventId];
                    else _exp[cityEventId] = true;
                    _renderRows();
                },

                setStake: function (value) {
                    _stake = value;
                    _renderRows();   // re-renders breakdowns with new stake
                },

                expand  : function () { _expand(); },
                collapse: function () { _collapse(); },
                toggle  : function () { if (_open) _collapse(); else _expand(); },

                showMsg: function (text, type) {
                    var el = document.getElementById('bs-msg');
                    if (!el) return;
                    el.textContent   = text;
                    el.style.color   = type === 'error' ? '#f87171' : '#4ade80';
                    el.style.display = 'block';
                    clearTimeout(el._t);
                    el._t = setTimeout(function () { el.style.display = 'none'; }, 5000);
                },

                submit: async function () {
                    var items = Object.values(_sel);
                    if (!items.length) return;

                    var stake = parseFloat(_stake) || 0;
                    if (stake <= 0) {
                        this.showMsg('Enter a stake amount first.', 'error');
                        document.getElementById('bs-stake-input')?.focus();
                        return;
                    }

                    var btn = document.getElementById('bs-submit');
                    if (btn) { btn.disabled = true; btn.style.opacity = '.6'; btn.textContent = 'Placing\u2026'; }

                    try {
                        var res = await fetch('{{ route("bets.multi") }}', {
                            method : 'POST',
                            headers: {
                                'Content-Type' : 'application/json',
                                'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').content,
                                'Accept'       : 'application/json',
                            },
                            body: JSON.stringify({
                                bets: items.map(function (s) {
                                    return {
                                        city_event_id     : s.cityEventId,
                                        betting_option_id : s.optionId,
                                        stake             : stake,
                                    };
                                }),
                            }),
                        });

                        var data = await res.json();

                        if (res.ok) {
                            _sel = {}; _exp = {}; _stake = '';
                            _badge();
                            _collapse();
                            window.location.href = '{{ route("bets.index") }}?placed=' + data.placed;
                        } else {
                            this.showMsg(data.error || 'Something went wrong.', 'error');
                            if (btn) { btn.disabled = false; btn.style.opacity = '1'; btn.textContent = 'Place Bets'; }
                        }
                    } catch (e) {
                        this.showMsg('Network error. Please try again.', 'error');
                        if (btn) { btn.disabled = false; btn.style.opacity = '1'; btn.textContent = 'Place Bets'; }
                    }
                },
            };
        })();
        </script>
        @endif
    @endauth

</body>
</html>
