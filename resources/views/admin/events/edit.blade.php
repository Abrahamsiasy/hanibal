@extends('admin.layouts.app')

@section('title', 'Edit Event')

@section('content')
    <h1 class="h3 mb-3">Edit Event</h1>

    <form method="POST" action="{{ route('admin.events.update', $event) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.events._form', ['event' => $event])
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
@endsection
