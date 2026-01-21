@extends('layouts.admin')

@section('title', $notification->title)

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">{{ $notification->title }}</h1>
                    <p class="text-muted mb-0">
                        @if($notification->isSent())
                            Sent {{ $notification->sent_at->format('d M Y H:i') }}
                        @elseif($notification->isScheduled())
                            Scheduled for {{ $notification->scheduled_for->format('d M Y H:i') }}
                        @else
                            Draft
                        @endif
                    </p>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                    ← Back to Dashboard
                </a>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>Notification Details</strong>
                    @if($notification->isSent())
                        <span class="badge bg-success">Sent</span>
                    @elseif($notification->scheduled_for)
                        <span class="badge bg-warning text-dark">Scheduled</span>
                    @else
                        <span class="badge bg-secondary">Draft</span>
                    @endif
                </div>
                <div class="card-body">
                    <p class="card-text" style="white-space: pre-wrap;">{{ $notification->body }}</p>
                    
                    @if($notification->link)
                        <hr>
                        <p class="mb-0">
                            <strong>Link:</strong> 
                            <a href="{{ $notification->link }}" target="_blank" rel="noopener" class="link-dark">
                                {{ $notification->link }}
                            </a>
                        </p>
                    @endif
                </div>
                <div class="card-footer text-muted">
                    <div class="row">
                        <div class="col">
                            <small>
                                <strong>Organisation:</strong> 
                                <a href="{{ route('admin.organisations.show', $notification->organisation) }}" class="link-dark">
                                    {{ $notification->organisation->name }}
                                </a>
                            </small>
                        </div>
                        <div class="col text-end">
                            <small>Created: {{ $notification->created_at->format('d M Y H:i') }}</small>
                        </div>
                    </div>
                </div>
            </div>

            @if($notification->isSent())
                @php $analytics = $notification->getAnalytics(); @endphp
                <div class="card mt-4">
                    <div class="card-header">
                        <strong>📊 Analytics</strong>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <h4 class="mb-0">{{ $analytics['recipients'] }}</h4>
                                <small class="text-muted">Recipients</small>
                            </div>
                            <div class="col-md-4">
                                <h4 class="mb-0">{{ $analytics['opens'] }} <small class="text-muted">({{ number_format($analytics['open_rate'], 1) }}%)</small></h4>
                                <small class="text-muted">📖 Opens</small>
                            </div>
                            @if($notification->link)
                                <div class="col-md-4">
                                    <h4 class="mb-0">{{ $analytics['clicks'] }} <small class="text-muted">({{ number_format($analytics['click_rate'], 1) }}%)</small></h4>
                                    <small class="text-muted">🔗 Link Clicks</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
