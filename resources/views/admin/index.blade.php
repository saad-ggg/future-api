@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
<h2 class="mb-4">Dashboard</h2>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6>Total Users</h6>
                <h3>{{ $usersCount ?? 0 }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6>Active Users</h6>
                <h3>{{ $activeUsers ?? 0 }}</h3>
            </div>
        </div>
    </div>
</div>
@endsection
