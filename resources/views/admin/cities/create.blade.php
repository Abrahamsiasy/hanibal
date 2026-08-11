@extends('admin.layouts.app')

@section('title', 'Create City')

@section('content')
    <h1 class="h3 mb-3">Create City</h1>

    <form method="POST" action="{{ route('admin.cities.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.cities._form')
        <button type="submit" class="btn btn-primary">Save</button>
        <a href="{{ route('admin.cities.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
@endsection
