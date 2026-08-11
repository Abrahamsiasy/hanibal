@extends('admin.layouts.app')

@section('title', 'Event Cities')

@section('content')
    <h1 class="h3 mb-3">Cities for {{ $event->title }}</h1>

    <div class="alert alert-warning">
        Removing a city will also remove its betting options for this event.
    </div>

    <form method="POST" action="{{ route('admin.events.cities.update', $event) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            @foreach ($cities as $city)
                <div class="form-check">
                    <input
                        type="checkbox"
                        class="form-check-input"
                        name="cities[]"
                        id="city_{{ $city->id }}"
                        value="{{ $city->id }}"
                        @checked(in_array($city->id, old('cities', $selectedCityIds), true))
                    >
                    <label class="form-check-label" for="city_{{ $city->id }}">{{ $city->name }}</label>
                </div>
            @endforeach
        </div>

        <button type="submit" class="btn btn-primary">Save Cities</button>
        <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">Back</a>
    </form>

    @if ($cityEvents->isNotEmpty())
        <hr class="my-4">
        <h2 class="h5">Configure Odds</h2>
        <ul>
            @foreach ($cityEvents as $cityEvent)
                <li>
                    {{ $cityEvent->city->name }}
                    —
                    <a href="{{ route('admin.city-events.options.index', $cityEvent) }}">Manage Odds</a>
                    —
                    <a href="{{ route('admin.city-events.settle.edit', $cityEvent) }}">Settle</a>
                    @if ($cityEvent->isSettled())
                        <span class="badge text-bg-success">Settled</span>
                    @endif
                </li>
            @endforeach
        </ul>
    @endif
@endsection
