@extends('layouts.app')

@section('title', ($subscriber->name ?? 'Subscriber') . ' - Details')

@section('content')
    <div class="mb-4">
        <a href="{{ route('subscribers.index') }}" class="text-decoration-none">
            ← Back to Subscribers
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @php
        $pivot = $subscriber->organisations()->where('organisation_id', $organisation->id)->first()?->pivot;
        $isUnsubscribed = $pivot?->unsubscribed_at !== null;
    @endphp

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>Subscriber Details</strong>
                    @if($isUnsubscribed)
                        <span class="badge bg-secondary">Unsubscribed</span>
                    @elseif($subscriber->isPhoneVerified())
                        <span class="badge bg-success">Verified</span>
                    @else
                        <span class="badge bg-warning text-dark">Pending Verification</span>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-3 text-muted">Name</div>
                        <div class="col-sm-9">
                            <strong>{{ $subscriber->name ?? 'Not provided' }}</strong>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-3 text-muted">Phone</div>
                        <div class="col-sm-9">
                            <code class="fs-5">{{ $subscriber->phone }}</code>
                            @if($subscriber->isPhoneVerified())
                                <span class="text-success ms-2">✓ Verified</span>
                            @endif
                        </div>
                    </div>

                    @if($subscriber->email)
                        <div class="row mb-3">
                            <div class="col-sm-3 text-muted">Email</div>
                            <div class="col-sm-9">{{ $subscriber->email }}</div>
                        </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-sm-3 text-muted">Subscribed</div>
                        <div class="col-sm-9">
                            {{ $pivot?->created_at?->format('d M Y \a\t g:ia') ?? 'Unknown' }}
                            <span class="text-muted">({{ $pivot?->created_at?->diffForHumans() }})</span>
                        </div>
                    </div>

                    @if($subscriber->phone_verified_at)
                        <div class="row mb-3">
                            <div class="col-sm-3 text-muted">Verified</div>
                            <div class="col-sm-9">
                                {{ $subscriber->phone_verified_at->format('d M Y \a\t g:ia') }}
                            </div>
                        </div>
                    @endif

                    @if($isUnsubscribed)
                        <div class="row mb-3">
                            <div class="col-sm-3 text-muted">Unsubscribed</div>
                            <div class="col-sm-9 text-danger">
                                {{ $pivot->unsubscribed_at->format('d M Y \a\t g:ia') }}
                            </div>
                        </div>
                    @endif

                    @if($subscriber->device_platform)
                        <div class="row mb-3">
                            <div class="col-sm-3 text-muted">Device</div>
                            <div class="col-sm-9">
                                @if($subscriber->device_platform === 'ios')
                                    <span class="badge bg-dark">iOS</span>
                                @else
                                    <span class="badge bg-success">Android</span>
                                @endif
                                <span class="text-muted ms-1">Push notifications enabled</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <strong>Actions</strong>
                </div>
                <div class="card-body">
                    @if($isUnsubscribed)
                        <form action="{{ route('subscribers.resubscribe', $subscriber) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100 mb-2">
                                Resubscribe
                            </button>
                        </form>
                        <p class="text-muted small mb-0">
                            This subscriber has unsubscribed. You can resubscribe them if they request it.
                        </p>
                    @else
                        <form action="{{ route('subscribers.destroy', $subscriber) }}" method="POST"
                              onsubmit="return confirm('Are you sure you want to unsubscribe this person? They will no longer receive notifications.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                Unsubscribe
                            </button>
                        </form>
                        <p class="text-muted small mt-2 mb-0">
                            Unsubscribing will stop all notifications to this subscriber.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
