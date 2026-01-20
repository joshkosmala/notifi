@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Notifications</h1>
            <p class="text-muted mb-0">Manage your notifications to subscribers</p>
        </div>
        <a href="{{ route('notifications.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> New Notification
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notifications as $notification)
                        <tr>
                            <td>
                                <a href="{{ route('notifications.show', $notification) }}" class="text-decoration-none">
                                    <strong>{{ $notification->title }}</strong>
                                </a>
                                <br>
                                <small class="text-muted text-truncate d-inline-block" style="max-width: 400px;">
                                    {{ $notification->body }}
                                </small>
                                @if($notification->link)
                                    <br><small class="text-primary">🔗 {{ Str::limit($notification->link, 40) }}</small>
                                @endif
                            </td>
                            <td>
                                @if($notification->isSent())
                                    <span class="badge bg-success">Sent</span>
                                    <br><small class="text-muted">{{ $notification->formatInTimezone($notification->sent_at) }}</small>
                                @elseif($notification->isScheduled())
                                    <span class="badge bg-warning text-dark">Scheduled</span>
                                    <br><small class="text-muted">{{ $notification->formatInTimezone($notification->scheduled_for) }}</small>
                                @else
                                    <span class="badge bg-secondary">Draft</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $notification->created_at->format('d M Y') }}</small>
                            </td>
                            <td class="text-end">
                                @if(!$notification->isSent())
                                    <a href="{{ route('notifications.edit', $notification) }}" class="btn btn-sm btn-outline-primary">
                                        Edit
                                    </a>
                                @endif
                                <a href="{{ route('notifications.show', $notification) }}" class="btn btn-sm btn-outline-secondary">
                                    View
                                </a>
                                @if(!$notification->isSent())
                                    <form action="{{ route('notifications.destroy', $notification) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this notification?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <p class="text-muted mb-3">No notifications yet.</p>
                                <a href="{{ route('notifications.create') }}" class="btn btn-primary">
                                    Create your first notification
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($notifications->hasPages())
            <div class="card-footer">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
@endsection
