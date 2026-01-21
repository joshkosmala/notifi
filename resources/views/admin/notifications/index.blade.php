@extends('layouts.admin')

@section('title', 'All Notifications')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">All Notifications</h1>
            <p class="text-muted mb-0">{{ $notifications->total() }} {{ Str::plural('notification', $notifications->total()) }}</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
            ← Back to Dashboard
        </a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Organisation</th>
                        <th>Status</th>
                        <th class="text-center">Opens</th>
                        <th class="text-center">Clicks</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notifications as $notification)
                        <tr>
                            <td>
                                <a href="{{ route('admin.notifications.show', $notification) }}" class="link-dark text-decoration-none">
                                    <strong>{{ $notification->title }}</strong>
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('admin.organisations.show', $notification->organisation) }}" class="text-muted text-decoration-none">
                                    {{ $notification->organisation->name }}
                                </a>
                            </td>
                            <td>
                                @if($notification->isSent())
                                    <span class="badge bg-success">Sent</span>
                                @elseif($notification->isScheduled())
                                    <span class="badge bg-warning text-dark">Scheduled</span>
                                @else
                                    <span class="badge bg-secondary">Draft</span>
                                @endif
                            </td>
                            @php
                                $opens = $notification->events->where('event_type', 'open')->count();
                                $clicks = $notification->events->where('event_type', 'link_click')->count();
                            @endphp
                            <td class="text-center">{{ $notification->isSent() ? $opens : '—' }}</td>
                            <td class="text-center">{{ $notification->isSent() && $notification->link ? $clicks : '—' }}</td>
                            <td>
                                <small class="text-muted">{{ $notification->created_at->format('d M Y') }}</small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-muted text-center py-4">No notifications yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $notifications->links() }}
    </div>
@endsection
