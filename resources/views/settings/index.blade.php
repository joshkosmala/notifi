@extends('layouts.app')

@section('title', 'Organisation Settings')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Organisation Settings</h1>
                    <p class="text-muted mb-0">{{ $organisation->name }}</p>
                </div>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    ← Back to Dashboard
                </a>
            </div>

            {{-- Social Media Connections --}}
            <div class="card mb-4">
                <div class="card-header">
                    <strong>📣 Social Media Connections</strong>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">
                        Connect your organisation's social media accounts to automatically post notifications.
                    </p>

                    {{-- Facebook Page --}}
                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-primary rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="white" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </div>
                            <div>
                                <strong>Facebook Page</strong>
                                @if($organisation->hasFacebookPage())
                                    <div class="text-success small">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                                        </svg>
                                        Connected: {{ $organisation->facebook_page_name }}
                                    </div>
                                @else
                                    <div class="text-muted small">Not connected</div>
                                @endif
                            </div>
                        </div>
                        <div>
                            @if($organisation->hasFacebookPage())
                                <form action="{{ route('settings.facebook.disconnect') }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">Disconnect</button>
                                </form>
                            @else
                                <a href="{{ route('settings.facebook.redirect') }}" class="btn btn-primary btn-sm">
                                    Connect Facebook Page
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- X (Twitter) --}}
                    <div class="d-flex justify-content-between align-items-center py-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-dark rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="white" viewBox="0 0 24 24">
                                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                </svg>
                            </div>
                            <div>
                                <strong>X (Twitter)</strong>
                                @if($organisation->hasXAccount())
                                    <div class="text-success small">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                                        </svg>
                                        Connected: @{{ $organisation->x_username }}
                                    </div>
                                @else
                                    <div class="text-muted small">Not connected</div>
                                @endif
                            </div>
                        </div>
                        <div>
                            @if($organisation->hasXAccount())
                                <form action="{{ route('settings.x.disconnect') }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">Disconnect</button>
                                </form>
                            @else
                                <a href="{{ route('settings.x.redirect') }}" class="btn btn-dark btn-sm">
                                    Connect X Account
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Subscriber QR Code --}}
            <div class="card mb-4">
                <div class="card-header">
                    <strong>📱 Subscriber QR Code</strong>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        Share this QR code with people so they can subscribe to your notifications. Print it on flyers, posters, or display it at your venue.
                    </p>

                    <div class="row">
                        <div class="col-md-6 text-center mb-3 mb-md-0">
                            <img src="{{ route('subscribe.qr', $organisation->subscribe_code) }}" 
                                 alt="Subscribe QR Code" 
                                 class="img-fluid border rounded p-2"
                                 style="max-width: 200px;">
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label small text-muted">Subscribe URL</label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" 
                                           value="{{ $organisation->getSubscribeUrl() }}" 
                                           id="subscribeUrl" readonly>
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="copyUrl()">
                                        Copy
                                    </button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small text-muted">Code</label>
                                <input type="text" class="form-control form-control-sm" 
                                       value="{{ $organisation->subscribe_code }}" readonly>
                            </div>
                            <a href="{{ route('subscribe.qr', $organisation->subscribe_code) }}" 
                               download="subscribe-qr.svg" 
                               class="btn btn-outline-primary btn-sm">
                                Download QR Code
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Organisation Details --}}
            <div class="card">
                <div class="card-header">
                    <strong>🏢 Organisation Details</strong>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-3">Name</dt>
                        <dd class="col-sm-9">{{ $organisation->name }}</dd>

                        @if($organisation->address)
                            <dt class="col-sm-3">Address</dt>
                            <dd class="col-sm-9">{{ $organisation->address }}</dd>
                        @endif

                        @if($organisation->url)
                            <dt class="col-sm-3">Website</dt>
                            <dd class="col-sm-9">
                                <a href="{{ $organisation->url }}" target="_blank" rel="noopener">{{ $organisation->url }}</a>
                            </dd>
                        @endif

                        @if($organisation->email)
                            <dt class="col-sm-3">Email</dt>
                            <dd class="col-sm-9">{{ $organisation->email }}</dd>
                        @endif

                        @if($organisation->phone)
                            <dt class="col-sm-3">Phone</dt>
                            <dd class="col-sm-9">{{ $organisation->phone }}</dd>
                        @endif

                        <dt class="col-sm-3">Timezone</dt>
                        <dd class="col-sm-9">{{ $organisation->timezone ?? 'Pacific/Auckland' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function copyUrl() {
    const input = document.getElementById('subscribeUrl');
    input.select();
    navigator.clipboard.writeText(input.value);
    
    // Show feedback
    const btn = event.target;
    const originalText = btn.textContent;
    btn.textContent = 'Copied!';
    setTimeout(() => btn.textContent = originalText, 2000);
}
</script>
@endpush
