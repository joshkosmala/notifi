@extends('layouts.admin')

@section('title', $organisation->name)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">{{ $organisation->name }}</h1>
            <p class="text-muted mb-0">
                @if($organisation->isVerified())
                    <span class="badge bg-success">Verified</span>
                @else
                    <span class="badge bg-secondary">Unverified</span>
                @endif
                <span class="ms-2">{{ $organisation->email }}</span>
            </p>
        </div>
        <a href="{{ route('admin.organisations.index') }}" class="btn btn-outline-secondary">
            ← Back to Organisations
        </a>
    </div>

    <div class="row g-4">
        {{-- Organisation Details --}}
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <strong>Organisation Details</strong>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Name</dt>
                        <dd class="col-sm-8">{{ $organisation->name }}</dd>

                        @if($organisation->address)
                            <dt class="col-sm-4">Address</dt>
                            <dd class="col-sm-8">{{ $organisation->address }}</dd>
                        @endif

                        @if($organisation->url)
                            <dt class="col-sm-4">Website</dt>
                            <dd class="col-sm-8">
                                <a href="{{ $organisation->url }}" target="_blank" rel="noopener" class="link-dark">{{ $organisation->url }}</a>
                            </dd>
                        @endif

                        @if($organisation->email)
                            <dt class="col-sm-4">Email</dt>
                            <dd class="col-sm-8">{{ $organisation->email }}</dd>
                        @endif

                        @if($organisation->phone)
                            <dt class="col-sm-4">Phone</dt>
                            <dd class="col-sm-8">{{ $organisation->phone }}</dd>
                        @endif

                        <dt class="col-sm-4">Timezone</dt>
                        <dd class="col-sm-8">{{ $organisation->timezone ?? 'Pacific/Auckland' }}</dd>

                        <dt class="col-sm-4">Created</dt>
                        <dd class="col-sm-8">{{ $organisation->created_at->format('d M Y H:i') }}</dd>

                        @if($organisation->verified_at)
                            <dt class="col-sm-4">Verified</dt>
                            <dd class="col-sm-8">{{ $organisation->verified_at->format('d M Y H:i') }}</dd>
                        @endif
                    </dl>
                </div>
            </div>

            {{-- Stats --}}
            <div class="row g-3 mt-2">
                <div class="col-4">
                    <div class="card text-bg-info">
                        <div class="card-body text-center">
                            <div class="display-6">{{ $organisation->administrators_count }}</div>
                            <small>Admins</small>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card text-bg-success">
                        <div class="card-body text-center">
                            <div class="display-6">{{ $organisation->subscribers_count }}</div>
                            <small>Subscribers</small>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card text-bg-secondary">
                        <div class="card-body text-center">
                            <div class="display-6">{{ $organisation->notifications_count }}</div>
                            <small>Notifications</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Social Connections --}}
            <div class="card mt-4">
                <div class="card-header">
                    <strong>Social Connections</strong>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-4">
                        <div>
                            @if($organisation->hasFacebookPage())
                                <span class="text-success">✓</span> Facebook: {{ $organisation->facebook_page_name }}
                            @else
                                <span class="text-muted">✗ Facebook not connected</span>
                            @endif
                        </div>
                        <div>
                            @if($organisation->hasXAccount())
                                <span class="text-success">✓</span> X: @{{ $organisation->x_username }}
                            @else
                                <span class="text-muted">✗ X not connected</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Administrators --}}
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <strong>Administrators</strong>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($organisation->administrators as $admin)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $admin->name }}</strong>
                                <br><small class="text-muted">{{ $admin->email }}</small>
                            </div>
                            <span class="badge bg-secondary">{{ $admin->pivot->role ?? 'admin' }}</span>
                        </div>
                    @empty
                        <div class="list-group-item text-muted">No administrators.</div>
                    @endforelse
                </div>
            </div>

            {{-- Recent Notifications --}}
            <div class="card mt-4">
                <div class="card-header">
                    <strong>Recent Notifications</strong>
                </div>
                <div class="list-group list-group-flush" style="max-height: 300px; overflow-y: auto;">
                    @forelse($organisation->notifications as $notification)
                        <a href="{{ route('admin.notifications.show', $notification) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong class="text-dark">{{ $notification->title }}</strong>
                                    @if($notification->isSent())
                                        <span class="badge bg-success ms-2">Sent</span>
                                    @elseif($notification->scheduled_for)
                                        <span class="badge bg-warning ms-2">Scheduled</span>
                                    @else
                                        <span class="badge bg-secondary ms-2">Draft</span>
                                    @endif
                                </div>
                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                        </a>
                    @empty
                        <div class="list-group-item text-muted">No notifications yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
