@extends('layouts.admin')

@section('title', 'Super Admin Dashboard')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Super Admin Dashboard</h1>
            <p class="text-muted mb-0">System-wide overview</p>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-md-2">
            <div class="card text-bg-primary">
                <div class="card-body">
                    <h6 class="card-title">Organisations</h6>
                    <p class="card-text display-6">{{ $stats['organisations'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-bg-info">
                <div class="card-body">
                    <h6 class="card-title">Admins</h6>
                    <p class="card-text display-6">{{ $stats['admins'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-bg-success">
                <div class="card-body">
                    <h6 class="card-title">Subscribers</h6>
                    <p class="card-text display-6">{{ $stats['subscribers'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-bg-secondary">
                <div class="card-body">
                    <h6 class="card-title">Notifications</h6>
                    <p class="card-text display-6">{{ $stats['notifications'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-bg-warning">
                <div class="card-body">
                    <h6 class="card-title">Sent</h6>
                    <p class="card-text display-6">{{ $stats['sent'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- All Organisations --}}
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>All Organisations</strong>
                    <span class="badge bg-primary">{{ $stats['organisations'] }} total</span>
                </div>
                <div class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
                    @forelse($organisations as $org)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $org->name }}</strong>
                                @if($org->isVerified())
                                    <span class="badge bg-success ms-2">Verified</span>
                                @else
                                    <span class="badge bg-secondary ms-2">Unverified</span>
                                @endif
                                <br>
                                <small class="text-muted">{{ $org->email }}</small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-secondary">{{ $org->subscribers_count }} subscribers</span>
                                <span class="badge bg-info">{{ $org->notifications_count }} notifications</span>
                            </div>
                        </div>
                    @empty
                        <div class="list-group-item text-muted">No organisations yet.</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Recent Notifications (System-wide) --}}
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>Recent Notifications (All Orgs)</strong>
                </div>
                <div class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
                    @forelse($recentNotifications as $notification)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong>{{ $notification->title }}</strong>
                                    @if($notification->isSent())
                                        <span class="badge bg-success ms-2">Sent</span>
                                    @elseif($notification->scheduled_for)
                                        <span class="badge bg-warning ms-2">Scheduled</span>
                                    @else
                                        <span class="badge bg-secondary ms-2">Draft</span>
                                    @endif
                                    <br>
                                    <small class="text-muted">{{ $notification->organisation->name }}</small>
                                </div>
                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-0 mt-2 small text-truncate">{{ $notification->body }}</p>
                        </div>
                    @empty
                        <div class="list-group-item text-muted">No notifications yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
