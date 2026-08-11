@extends('admin.layouts.app')

@section('title', 'Add Banner')

@section('content')
    <p><a href="{{ route('admin.cities.show', $city) }}">&larr; {{ $city->name }}</a></p>
    <h1 class="h3 mb-4">Add Banner — {{ $city->name }}</h1>

    <form method="POST" action="{{ route('admin.cities.banners.store', $city) }}" enctype="multipart/form-data">
        @csrf
        @include('admin.city-banners._form', ['banner' => null])
        <button type="submit" class="btn btn-primary">Create Banner</button>
        <a href="{{ route('admin.cities.show', $city) }}" class="btn btn-secondary">Cancel</a>
    </form>
@endsection
