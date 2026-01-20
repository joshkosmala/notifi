@extends('layouts.app')

@section('title', 'Subscribers')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Subscribers</h1>
            <p class="text-muted mb-0">People subscribed to {{ $organisation->name }}</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card text-bg-primary">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-white-50 small">Total Subscribers</div>
                            <div class="h4 mb-0">{{ $stats['total'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-bg-success">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-white-50 small">Verified</div>
                            <div class="h4 mb-0">{{ $stats['verified'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-bg-secondary">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-white-50 small">Unsubscribed</div>
                            <div class="h4 mb-0">{{ $stats['unsubscribed'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Subscriber</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscribers as $subscriber)
                        @php
                            $isUnsubscribed = $subscriber->pivot->unsubscribed_at !== null;
                        @endphp
                        <tr class="{{ $isUnsubscribed ? 'table-secondary' : '' }}">
                            <td>
                                <a href="{{ route('subscribers.show', $subscriber) }}" class="text-decoration-none">
                                    <strong>{{ $subscriber->name ?? 'Anonymous' }}</strong>
                                </a>
                                @if($subscriber->email)
                                    <br><small class="text-muted">{{ $subscriber->email }}</small>
                                @endif
                            </td>
                            <td>
                                <code>{{ $subscriber->phone }}</code>
                            </td>
                            <td>
                                @if($isUnsubscribed)
                                    <span class="badge bg-secondary">Unsubscribed</span>
                                @elseif($subscriber->isPhoneVerified())
                                    <span class="badge bg-success">Verified</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $subscriber->pivot->created_at->format('d M Y') }}</small>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('subscribers.show', $subscriber) }}" class="btn btn-sm btn-outline-secondary">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <p class="mb-2">No subscribers yet.</p>
                                <small>Share your QR code or subscribe link to get started!</small>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($subscribers->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $subscribers->links() }}
        </div>
    @endif
@endsection
