@extends('admin.layouts.app')

@section('title', 'Betting Options')

@section('content')
    <h1 class="h3 mb-2">Betting Options</h1>
    <p class="mb-1"><strong>Event:</strong> {{ $cityEvent->event->title }}</p>
    <p class="mb-4"><strong>City:</strong> {{ $cityEvent->city->name }}</p>

    <table class="table table-bordered mb-4">
        <thead>
        <tr>
            <th>Option</th>
            <th>Odds</th>
            <th>Active</th>
            <th>Position</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($options as $option)
            <tr>
                <td>{{ $option->name }}</td>
                <td>{{ number_format((float) $option->odds, 2) }}</td>
                <td>{{ $option->active ? 'Yes' : 'No' }}</td>
                <td>{{ $option->position }}</td>
                <td>
                    <a href="{{ route('admin.betting-options.edit', $option) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                    <form action="{{ route('admin.betting-options.destroy', $option) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this option?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5">No betting options yet.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div class="card">
        <div class="card-body">
            <h2 class="h5 mb-3">Add Option</h2>
            <form method="POST" action="{{ route('admin.city-events.options.store', $cityEvent) }}">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="odds" class="form-label">Odds</label>
                    <input type="number" step="0.01" min="1.01" name="odds" id="odds" value="{{ old('odds') }}" class="form-control @error('odds') is-invalid @enderror" required>
                    @error('odds')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="position" class="form-label">Position</label>
                    <input type="number" min="0" name="position" id="position" value="{{ old('position', 0) }}" class="form-control @error('position') is-invalid @enderror">
                    @error('position')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" name="active" id="active" value="1" class="form-check-input" @checked(old('active', true))>
                    <label for="active" class="form-check-label">Active</label>
                </div>
                <button type="submit" class="btn btn-primary">Add Option</button>
                <a href="{{ route('admin.events.cities.edit', $cityEvent->event_id) }}" class="btn btn-secondary">Back to Cities</a>
            </form>
        </div>
    </div>
@endsection
