@extends('admin.layouts.app')

@section('title', 'Cities')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Cities</h1>
        <a href="{{ route('admin.cities.create') }}" class="btn btn-primary">Create City</a>
    </div>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Name</th>
            <th>Slug</th>
            <th>Active</th>
            <th>Events</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($cities as $city)
            <tr>
                <td>{{ $city->name }}</td>
                <td>{{ $city->slug }}</td>
                <td>
                    @if ($city->active)
                        <span class="badge text-bg-success">Yes</span>
                    @else
                        <span class="badge text-bg-secondary">No</span>
                    @endif
                </td>
                <td>{{ $city->events_count }}</td>
                <td>
                    <a href="{{ route('admin.cities.edit', $city) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                    <form action="{{ route('admin.cities.destroy', $city) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this city?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5">No cities yet.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    {{ $cities->links() }}
@endsection
