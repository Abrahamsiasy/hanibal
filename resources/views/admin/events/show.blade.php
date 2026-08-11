@extends('admin.layouts.app')

@section('title', $event->title)

@section('content')
    <h1 class="h3 mb-3">{{ $event->title }}</h1>

    <table class="table table-bordered mb-4">
        <tr>
            <th>Start Time</th>
            <td>{{ $event->starts_at->format('M j, Y g:i A') }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td>{{ $event->status->label() }}</td>
        </tr>
        <tr>
            <th>Description</th>
            <td>{{ $event->description }}</td>
        </tr>
    </table>

    <h2 class="h5">Cities</h2>
    <ul>
        @forelse ($event->cityEvents as $cityEvent)
            <li>
                {{ $cityEvent->city->name }}
                —
                <a href="{{ route('admin.city-events.options.index', $cityEvent) }}">Manage Odds</a>
            </li>
        @empty
            <li>No cities assigned.</li>
        @endforelse
    </ul>

    <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-primary">Edit</a>
    <a href="{{ route('admin.events.cities.edit', $event) }}" class="btn btn-secondary">Cities / Odds</a>
    <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary">Back</a>
@endsection
