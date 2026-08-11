@extends('admin.layouts.app')

@section('title', 'Edit Betting Option')

@section('content')
    <h1 class="h3 mb-2">Edit Betting Option</h1>
    <p class="mb-1"><strong>Event:</strong> {{ $bettingOption->cityEvent->event->title }}</p>
    <p class="mb-4"><strong>City:</strong> {{ $bettingOption->cityEvent->city->name }}</p>

    <form method="POST" action="{{ route('admin.betting-options.update', $bettingOption) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" id="name" value="{{ old('name', $bettingOption->name) }}" class="form-control @error('name') is-invalid @enderror" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="odds" class="form-label">Odds</label>
            <input type="number" step="0.01" min="1.01" name="odds" id="odds" value="{{ old('odds', $bettingOption->odds) }}" class="form-control @error('odds') is-invalid @enderror" required>
            @error('odds')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="position" class="form-label">Position</label>
            <input type="number" min="0" name="position" id="position" value="{{ old('position', $bettingOption->position) }}" class="form-control @error('position') is-invalid @enderror">
            @error('position')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" name="active" id="active" value="1" class="form-check-input" @checked(old('active', $bettingOption->active))>
            <label for="active" class="form-check-label">Active</label>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('admin.city-events.options.index', $bettingOption->city_event_id) }}" class="btn btn-secondary">Cancel</a>
    </form>
@endsection
