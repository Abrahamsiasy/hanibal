@extends('admin.layouts.app')

@section('title', 'Settle Event')

@section('content')
    <h1 class="h3 mb-3">Settle Event</h1>

    <p><strong>Event:</strong> {{ $cityEvent->event->title }}</p>
    <p><strong>City:</strong> {{ $cityEvent->city->name }}</p>
    <p><strong>Pending bets:</strong> {{ $pendingBetsCount }}</p>

    @if ($cityEvent->isSettled())
        <div class="alert alert-info">
            Already settled.
            @if ($cityEvent->winningBettingOption)
                Winner: {{ $cityEvent->winningBettingOption->name }}
            @endif
        </div>
        <a href="{{ route('admin.events.cities.edit', $cityEvent->event) }}" class="btn btn-secondary">Back</a>
    @else
        <div class="alert alert-warning">
            Settling will mark matching pending bets as won (and pay out) and all other pending bets as lost.
        </div>

        <form method="POST" action="{{ route('admin.city-events.settle.update', $cityEvent) }}">
            @csrf
            <div class="mb-3">
                <label for="winning_betting_option_id" class="form-label">Winning Option</label>
                <select name="winning_betting_option_id" id="winning_betting_option_id" class="form-select" required>
                    @foreach ($cityEvent->bettingOptions as $option)
                        <option value="{{ $option->id }}">{{ $option->name }} ({{ number_format((float) $option->odds, 2) }})</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-danger" onclick="return confirm('Settle this city event now?')">Settle</button>
            <a href="{{ route('admin.events.cities.edit', $cityEvent->event) }}" class="btn btn-secondary">Cancel</a>
        </form>
    @endif
@endsection
