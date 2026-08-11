@extends('admin.layouts.app')

@section('title', 'Edit Banner')

@section('content')
    <p><a href="{{ route('admin.cities.show', $city) }}">&larr; {{ $city->name }}</a></p>
    <h1 class="h3 mb-4">Edit Banner</h1>

    <form method="POST" action="{{ route('admin.city-banners.update', $banner) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.city-banners._form', ['banner' => $banner])
        <button type="submit" class="btn btn-primary">Save</button>
        <a href="{{ route('admin.cities.show', $city) }}" class="btn btn-secondary">Cancel</a>
    </form>
@endsection
