@extends('admin.layouts.app')

@section('title', 'Edit City')

@section('content')
    <h1 class="h3 mb-3">Edit City</h1>

    <form method="POST" action="{{ route('admin.cities.update', $city) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.cities._form', ['city' => $city])
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('admin.cities.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
@endsection
