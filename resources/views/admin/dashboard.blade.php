@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h1 class="h3 mb-4">Dashboard</h1>

    <div class="row g-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="h2 mb-0">{{ $totalCities }}</div>
                    <div class="text-muted small">Cities</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="h2 mb-0">{{ $totalCustomers }}</div>
                    <div class="text-muted small">Customers</div>
                    <a href="{{ route('admin.customers.index') }}" class="small">View all</a>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="h2 mb-0">{{ $openEvents }}</div>
                    <div class="text-muted small">Open Events</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="h2 mb-0">{{ $totalBets }}</div>
                    <div class="text-muted small">Total Bets</div>
                    <div class="text-muted small">{{ number_format((float) $totalStaked, 2) }} staked</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card border-warning">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="h4 mb-0">{{ $pendingDeposits }}</div>
                        <div class="text-muted small">Pending Deposits</div>
                    </div>
                    <a href="{{ route('admin.wallet-requests.index', ['status' => 'pending']) }}" class="btn btn-warning btn-sm">Review</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-info">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="h4 mb-0">{{ $pendingWithdrawals }}</div>
                        <div class="text-muted small">Pending Withdrawals</div>
                    </div>
                    <a href="{{ route('admin.wallet-requests.index', ['status' => 'pending']) }}" class="btn btn-info btn-sm">Review</a>
                </div>
            </div>
        </div>
    </div>
@endsection
