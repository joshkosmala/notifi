@extends('layouts.admin')

@section('title', 'All Organisations')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">All Organisations</h1>
            <p class="text-muted mb-0">{{ $organisations->total() }} organisations</p>
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
                        <th>Organisation</th>
                        <th>Status</th>
                        <th class="text-center">Admins</th>
                        <th class="text-center">Subscribers</th>
                        <th class="text-center">Notifications</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($organisations as $org)
                        <tr>
                            <td>
                                <a href="{{ route('admin.organisations.show', $org) }}" class="link-dark text-decoration-none">
                                    <strong>{{ $org->name }}</strong>
                                </a>
                                @if($org->email)
                                    <br><small class="text-muted">{{ $org->email }}</small>
                                @endif
                            </td>
                            <td>
                                @if($org->isVerified())
                                    <span class="badge bg-success">Verified</span>
                                @else
                                    <span class="badge bg-secondary">Unverified</span>
                                @endif
                            </td>
                            <td class="text-center">{{ $org->administrators_count }}</td>
                            <td class="text-center">{{ $org->subscribers_count }}</td>
                            <td class="text-center">{{ $org->notifications_count }}</td>
                            <td>
                                <small class="text-muted">{{ $org->created_at->format('d M Y') }}</small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-muted text-center py-4">No organisations yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $organisations->links() }}
    </div>
@endsection
