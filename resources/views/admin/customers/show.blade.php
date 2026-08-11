@extends('admin.layouts.app')

@section('title', $user->name)

@section('content')
    <p><a href="{{ route('admin.customers.index') }}">&larr; Customers</a></p>

    <h1 class="h3 mb-1">{{ $user->name }}</h1>
    <p class="text-muted mb-4">{{ $user->phone }} · {{ $user->city?->name ?? 'No city' }} · Joined {{ $user->created_at->format('M j, Y') }}</p>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="h4 mb-0">{{ number_format((float) $wallet->balance, 2) }}</div>
                    <div class="text-muted small">Balance</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="h4 mb-0">{{ number_format((float) $wallet->availableBalance(), 2) }}</div>
                    <div class="text-muted small">Available</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="h4 mb-0">{{ $bets->total() }}</div>
                    <div class="text-muted small">Total Bets</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Pending wallet requests for this user --}}
    @php $pendingRequests = $requests->filter(fn($r) => $r->isPending()); @endphp
    @if ($pendingRequests->count())
        <div class="alert alert-warning">{{ $pendingRequests->count() }} pending wallet request(s) — <a href="{{ route('admin.wallet-requests.index', ['status' => 'pending']) }}">Review</a></div>
    @endif

    <h2 class="h5 mt-4 mb-2">Bets</h2>
    <table class="table table-sm table-bordered">
        <thead class="table-light">
        <tr><th>Event</th><th>Option</th><th>Stake</th><th>Odds</th><th>Payout</th><th>Status</th><th>Date</th></tr>
        </thead>
        <tbody>
        @forelse ($bets as $bet)
            <tr>
                <td>{{ $bet->cityEvent->event->title }}</td>
                <td>{{ $bet->bettingOption->name }}</td>
                <td>{{ number_format((float) $bet->stake, 2) }}</td>
                <td>{{ number_format((float) $bet->odds, 2) }}</td>
                <td>{{ number_format((float) $bet->potential_payout, 2) }}</td>
                <td><span class="badge text-bg-secondary">{{ $bet->status->label() }}</span></td>
                <td>{{ $bet->created_at->format('M j, Y') }}</td>
            </tr>
        @empty
            <tr><td colspan="7" class="text-muted">No bets.</td></tr>
        @endforelse
        </tbody>
    </table>
    {{ $bets->links() }}

    <h2 class="h5 mt-4 mb-2">Wallet Requests</h2>
    <table class="table table-sm table-bordered">
        <thead class="table-light">
        <tr><th>Type</th><th>Amount</th><th>Status</th><th>Note</th><th>Date</th></tr>
        </thead>
        <tbody>
        @forelse ($requests as $req)
            <tr>
                <td>{{ $req->type->label() }}</td>
                <td>{{ number_format((float) $req->amount, 2) }}</td>
                <td><span class="badge text-bg-secondary">{{ $req->status->label() }}</span></td>
                <td>{{ $req->note }}</td>
                <td>{{ $req->created_at->format('M j, Y') }}</td>
            </tr>
        @empty
            <tr><td colspan="5" class="text-muted">No requests.</td></tr>
        @endforelse
        </tbody>
    </table>
    {{ $requests->links() }}

    <h2 class="h5 mt-4 mb-2">Transactions</h2>
    <table class="table table-sm table-bordered">
        <thead class="table-light">
        <tr><th>Type</th><th>Amount</th><th>Balance After</th><th>Description</th><th>Date</th></tr>
        </thead>
        <tbody>
        @forelse ($transactions as $tx)
            <tr>
                <td>{{ $tx->type->label() }}</td>
                <td>{{ $tx->type->isCredit() ? '+' : '-' }}{{ number_format((float) $tx->amount, 2) }}</td>
                <td>{{ number_format((float) $tx->balance_after, 2) }}</td>
                <td>{{ $tx->description }}</td>
                <td>{{ $tx->created_at->format('M j, Y g:i A') }}</td>
            </tr>
        @empty
            <tr><td colspan="5" class="text-muted">No transactions.</td></tr>
        @endforelse
        </tbody>
    </table>
    {{ $transactions->links() }}
@endsection
