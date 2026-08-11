@extends('admin.layouts.app')

@section('title', 'Wallet Requests')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Wallet Requests</h1>
        <div class="btn-group">
            <a href="{{ route('admin.wallet-requests.index') }}" class="btn btn-sm {{ $currentStatus === 'all' ? 'btn-primary' : 'btn-outline-primary' }}">All</a>
            <a href="{{ route('admin.wallet-requests.index', ['status' => 'pending']) }}" class="btn btn-sm {{ $currentStatus === 'pending' ? 'btn-primary' : 'btn-outline-primary' }}">Pending</a>
            <a href="{{ route('admin.wallet-requests.index', ['status' => 'approved']) }}" class="btn btn-sm {{ $currentStatus === 'approved' ? 'btn-primary' : 'btn-outline-primary' }}">Approved</a>
            <a href="{{ route('admin.wallet-requests.index', ['status' => 'rejected']) }}" class="btn btn-sm {{ $currentStatus === 'rejected' ? 'btn-primary' : 'btn-outline-primary' }}">Rejected</a>
        </div>
    </div>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Customer</th>
            <th>Phone</th>
            <th>Type</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Note</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($requests as $walletRequest)
            <tr>
                <td>{{ $walletRequest->user->name }}</td>
                <td>{{ $walletRequest->user->phone }}</td>
                <td>{{ $walletRequest->type->label() }}</td>
                <td>{{ number_format((float) $walletRequest->amount, 2) }}</td>
                <td><span class="badge text-bg-secondary">{{ $walletRequest->status->label() }}</span></td>
                <td>{{ $walletRequest->note }}</td>
                <td>{{ $walletRequest->created_at->format('M j, Y g:i A') }}</td>
                <td>
                    @if ($walletRequest->isPending())
                        <form method="POST" action="{{ route('admin.wallet-requests.approve', $walletRequest) }}" class="d-inline">
                            @csrf
                            <input type="text" name="admin_note" class="form-control form-control-sm d-inline-block w-auto mb-1" placeholder="Admin note">
                            <button type="submit" class="btn btn-sm btn-success">Approve</button>
                        </form>
                        <form method="POST" action="{{ route('admin.wallet-requests.reject', $walletRequest) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                        </form>
                    @else
                        {{ $walletRequest->admin_note }}
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8">No wallet requests found.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    {{ $requests->links() }}
@endsection
