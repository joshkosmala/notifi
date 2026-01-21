@extends('layouts.app')

@section('title', $organisation->name . ' - Dashboard')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">{{ $organisation->name }}</h1>
            <p class="text-muted mb-0">Organisation Dashboard</p>
        </div>
        <a href="{{ url('/notifications/create') }}" class="btn btn-primary">
            + New Notification
        </a>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-md-3 col-6">
            <div class="card text-bg-success">
                <div class="card-body">
                    <h5 class="card-title">Subscribers</h5>
                    <p class="card-text display-6">{{ $stats['subscribers'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card text-bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Notifications Sent</h5>
                    <p class="card-text display-6">{{ $stats['sent'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">📖 Total Opens</h5>
                    <p class="card-text display-6">{{ $analytics['total_opens'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">🔗 Link Clicks</h5>
                    <p class="card-text display-6">{{ $analytics['total_clicks'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Recent Notifications --}}
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <a href="{{ url('/notifications') }}" class="text-decoration-none"><strong>Recent Notifications</strong></a>
                    <a href="{{ url('/notifications') }}" class="btn btn-sm btn-outline-secondary">View All</a>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($recentNotifications as $notification)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <a href="{{ route('notifications.show', $notification) }}" class="text-decoration-none">
                                        <strong>{{ $notification->title }}</strong>
                                    </a>
                                    @if($notification->isSent())
                                        <span class="badge bg-success ms-2">Sent</span>
                                    @elseif($notification->isScheduled())
                                        <span class="badge bg-warning ms-2">Scheduled</span>
                                    @else
                                        <span class="badge bg-secondary ms-2">Draft</span>
                                    @endif
                                </div>
                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-0 mt-2 small text-truncate" style="max-width: 100%;">
                                {{ $notification->body }}
                            </p>
                            @if($notification->link)
                                <small class="text-primary">🔗 {{ $notification->link }}</small>
                            @endif
                        </div>
                    @empty
                        <div class="list-group-item text-muted text-center py-4">
                            <p class="mb-2">No notifications yet.</p>
                            <a href="{{ url('/notifications/create') }}" class="btn btn-primary btn-sm">Send your first notification</a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Recent Subscribers --}}
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <a href="{{ url('/subscribers') }}" class="text-decoration-none"><strong>Recent Subscribers</strong></a>
                    <a href="{{ url('/subscribers') }}" class="btn btn-sm btn-outline-secondary">View All</a>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($recentSubscribers as $subscriber)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $subscriber->name ?? 'Anonymous' }}</strong>
                                @if($subscriber->isPhoneVerified())
                                    <span class="badge bg-success ms-1">✓</span>
                                @endif
                                <br>
                                <small class="text-muted">{{ $subscriber->phone }}</small>
                            </div>
                            <small class="text-muted">{{ $subscriber->created_at->diffForHumans() }}</small>
                        </div>
                    @empty
                        <div class="list-group-item text-muted text-center py-4">
                            <p class="mb-2">No subscribers yet.</p>
                            <small>Share your QR code to get subscribers!</small>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
