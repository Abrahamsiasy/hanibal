@extends('admin.layouts.app')

@section('title', 'Create Event')

@section('content')
    <h1 class="h3 mb-3">Create Event</h1>

    <form method="POST" action="{{ route('admin.events.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.events._form')
        <button type="submit" class="btn btn-primary">Save</button>
        <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
@endsection
