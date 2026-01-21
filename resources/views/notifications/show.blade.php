@extends('layouts.app')

@section('title', $notification->title)

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">{{ $notification->title }}</h1>
                    <p class="text-muted mb-0">
                        @if($notification->isSent())
                            Sent {{ $notification->formatInTimezone($notification->sent_at) }}
                        @elseif($notification->isScheduled())
                            Scheduled for {{ $notification->formatInTimezone($notification->scheduled_for) }}
                        @else
                            Draft
                        @endif
                    </p>
                </div>
                <div class="d-flex gap-2">
                    @if(!$notification->isSent())
                        <a href="{{ route('notifications.edit', $notification) }}" class="btn btn-primary">
                            Edit
                        </a>
                    @endif
                    <a href="{{ route('notifications.index') }}" class="btn btn-outline-secondary">
                        ← Back
                    </a>
                </div>
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
                            <a href="{{ $notification->link }}" target="_blank" rel="noopener">
                                {{ $notification->link }}
                            </a>
                        </p>
                    @endif
                </div>
                <div class="card-footer text-muted">
                    <div class="row">
                        <div class="col">
                            <small>Created: {{ $notification->formatInTimezone($notification->created_at) }}</small>
                        </div>
                        @if($notification->isScheduled())
                            <div class="col text-end">
                                <small>Scheduled: {{ $notification->formatInTimezone($notification->scheduled_for) }}</small>
                            </div>
                        @endif
                        @if($notification->isSent())
                            <div class="col text-end">
                                <small>Sent: {{ $notification->formatInTimezone($notification->sent_at) }}</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Preview Card --}}
            <div class="card mt-4">
                <div class="card-header">
                    <strong>📱 Mobile Preview</strong>
                </div>
                <div class="card-body bg-light">
                    <div class="mx-auto" style="max-width: 320px;">
                        <div class="bg-white rounded-3 shadow-sm p-3">
                            <div class="d-flex align-items-start gap-3">
                                <div class="bg-primary rounded-2 d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px; flex-shrink: 0;">
                                    <span class="text-white fw-bold">N</span>
                                </div>
                                <div class="flex-grow-1 min-width-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <strong class="d-block text-truncate">{{ $notification->title }}</strong>
                                        <small class="text-muted ms-2 flex-shrink-0">now</small>
                                    </div>
                                    <p class="mb-0 text-muted small" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                        {{ $notification->body }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Analytics Card (only for sent notifications) --}}
            @if($notification->isSent())
                @php $analytics = $notification->getAnalytics(); @endphp
                <div class="card mt-4">
                    <div class="card-header">
                        <strong>📊 Analytics</strong>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="{{ $notification->link ? 'col-md-4' : 'col-md-6' }} mb-3 mb-md-0">
                                <div class="border rounded p-3">
                                    <h3 class="mb-1">{{ $analytics['recipients'] }}</h3>
                                    <small class="text-muted">Recipients</small>
                                </div>
                            </div>
                            <div class="{{ $notification->link ? 'col-md-4' : 'col-md-6' }} mb-3 mb-md-0">
                                <div class="border rounded p-3">
                                    <h3 class="mb-1">{{ $analytics['opens'] }}</h3>
                                    <small class="text-muted">Opens ({{ $analytics['open_rate'] }}%)</small>
                                </div>
                            </div>
                            @if($notification->link)
                                <div class="col-md-4">
                                    <div class="border rounded p-3">
                                        <h3 class="mb-1">{{ $analytics['clicks'] }}</h3>
                                        <small class="text-muted">Link Clicks ({{ $analytics['click_rate'] }}%)</small>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection
