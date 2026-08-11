@extends('frontend.layouts.app')

@section('title', 'Choose Your City — ' . config('app.name'))

@section('content')
    {{-- Hero strip --}}
    <div class="bg-[#111] border-b border-[#2a2a2a] py-14 text-center px-4">
        <p class="font-display text-red-600 text-sm font-bold uppercase tracking-widest mb-2">ETFC Fight Night · Sep 5</p>
        <h1 class="font-display font-black text-4xl sm:text-5xl uppercase tracking-tight leading-none mb-3">
            SELECT YOUR CITY
        </h1>
        <p class="text-zinc-400 text-sm max-w-xs mx-auto">
            Bet on live fight-night events with city-specific odds.
        </p>
    </div>

    {{-- City grid --}}
    <div class="max-w-5xl mx-auto px-4 py-10">
        @if ($cities->isEmpty())
            <p class="text-center text-zinc-500">No cities available right now.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($cities as $city)
                    <a href="{{ route('cities.show', $city) }}"
                       class="group block bg-[#1a1a1a] border border-[#2a2a2a] rounded-lg overflow-hidden hover:border-red-600 transition-colors">
                        {{-- Image or gradient banner --}}
                        @if ($city->hero_image)
                            <div class="h-32 overflow-hidden">
                                <img src="{{ asset('storage/'.$city->hero_image) }}" alt="{{ $city->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            </div>
                        @else
                            <div class="h-32 bg-gradient-to-br from-red-900/40 to-[#111] flex items-center justify-center">
                                <svg class="w-12 h-12 text-red-700/50" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                        @endif
                        <div class="p-4">
                            <h2 class="font-display font-bold text-xl uppercase tracking-wide text-white group-hover:text-red-500 transition-colors leading-tight">
                                {{ $city->name }}
                            </h2>
                            @if ($city->venue)
                                <p class="text-xs text-zinc-500 mt-0.5">📍 {{ $city->venue }}</p>
                            @endif
                            @if ($city->hero_subtitle)
                                <p class="text-zinc-400 text-sm mt-1">{{ $city->hero_subtitle }}</p>
                            @endif
                            <span class="mt-3 inline-block text-xs font-bold text-red-500 uppercase tracking-wider">
                                View Fights →
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
@endsection
