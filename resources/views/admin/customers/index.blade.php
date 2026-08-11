@extends('admin.layouts.app')

@section('title', 'Customers')

@section('content')
    <h1 class="h3 mb-4">Customers</h1>

    <table class="table table-bordered table-hover">
        <thead class="table-light">
        <tr>
            <th>Name</th>
            <th>Phone</th>
            <th>City</th>
            <th>Balance</th>
            <th>Bets</th>
            <th>Joined</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @forelse ($customers as $customer)
            <tr>
                <td>{{ $customer->name }}</td>
                <td>{{ $customer->phone }}</td>
                <td>{{ $customer->city?->name ?? '—' }}</td>
                <td>{{ $customer->wallet ? number_format((float) $customer->wallet->balance, 2) : '0.00' }}</td>
                <td>{{ $customer->bets_count }}</td>
                <td>{{ $customer->created_at->format('M j, Y') }}</td>
                <td><a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-sm btn-outline-primary">View</a></td>
            </tr>
        @empty
            <tr><td colspan="7" class="text-muted text-center">No customers yet.</td></tr>
        @endforelse
        </tbody>
    </table>

    {{ $customers->links() }}
@endsection
