@extends('admin.layouts.app')

@section('title', $city->name)

@section('content')
    <p><a href="{{ route('admin.cities.index') }}">&larr; Cities</a></p>
    <h1 class="h3 mb-3">{{ $city->name }}</h1>

    <table class="table table-bordered mb-3">
        <tr>
            <th>Slug</th>
            <td>{{ $city->slug }}</td>
        </tr>
        <tr>
            <th>Hero Title</th>
            <td>{{ $city->hero_title }}</td>
        </tr>
        <tr>
            <th>Hero Subtitle</th>
            <td>{{ $city->hero_subtitle }}</td>
        </tr>
        <tr>
            <th>Active</th>
            <td>{{ $city->active ? 'Yes' : 'No' }}</td>
        </tr>
        <tr>
            <th>Events</th>
            <td>{{ $city->events_count }}</td>
        </tr>
    </table>

    <a href="{{ route('admin.cities.edit', $city) }}" class="btn btn-primary">Edit City</a>
    <a href="{{ route('admin.cities.index') }}" class="btn btn-secondary ms-1">Back</a>

    <hr class="my-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 mb-0">Banners</h2>
        <a href="{{ route('admin.cities.banners.create', $city) }}" class="btn btn-sm btn-success">+ Add Banner</a>
    </div>

    @if ($city->banners->isEmpty())
        <p class="text-muted">No banners yet.</p>
    @else
        <table class="table table-sm table-bordered">
            <thead class="table-light">
            <tr>
                <th>Image</th>
                <th>Title</th>
                <th>Subtitle</th>
                <th>Position</th>
                <th>Active</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach ($city->banners as $banner)
                <tr>
                    <td>
                        @if ($banner->image)
                            <img src="{{ asset('storage/'.$banner->image) }}" alt="" style="height:40px">
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>{{ $banner->title }}</td>
                    <td>{{ $banner->subtitle ?? '—' }}</td>
                    <td>{{ $banner->position }}</td>
                    <td>{{ $banner->active ? 'Yes' : 'No' }}</td>
                    <td>
                        <a href="{{ route('admin.city-banners.edit', $banner) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form method="POST" action="{{ route('admin.city-banners.destroy', $banner) }}" class="d-inline" onsubmit="return confirm('Delete this banner?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
@endsection
