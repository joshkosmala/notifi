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
        </div>
    </div>
@endsection
