@extends('admin.layouts.app')

@section('title', 'Events')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Events</h1>
        <a href="{{ route('admin.events.create') }}" class="btn btn-primary">Create Event</a>
    </div>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Title</th>
            <th>Start Time</th>
            <th>Status</th>
            <th>Cities</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($events as $event)
            <tr>
                <td>{{ $event->title }}</td>
                <td>{{ $event->starts_at->format('M j, Y g:i A') }}</td>
                <td><span class="badge text-bg-secondary">{{ $event->status->label() }}</span></td>
                <td>{{ $event->cities_count }}</td>
                <td>
                    <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                    <a href="{{ route('admin.events.cities.edit', $event) }}" class="btn btn-sm btn-outline-secondary">Cities / Odds</a>
                    <form action="{{ route('admin.events.destroy', $event) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this event?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5">No events yet.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    {{ $events->links() }}
@endsection
