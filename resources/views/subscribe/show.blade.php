@extends('layouts.app')

@section('title', 'Subscribe to ' . $organisation->name)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <!-- Organisation Info -->
            <h1 class="mb-3">{{ $organisation->name }}</h1>
            <p class="text-muted mb-4">Subscribe to receive notifications from this organisation.</p>

            <!-- Mobile Detection - Show App Link -->
            <div id="mobile-prompt" class="d-none">
                <div class="card shadow-sm mb-4">
                    <div class="card-body py-4">
                        <p class="mb-3">Open in the Notifi app to subscribe:</p>
                        <a href="{{ $deepLink }}" class="btn btn-primary btn-lg w-100 mb-3">
                            Open Notifi App
                        </a>
                        <p class="text-muted small mb-0">
                            Don't have the app? 
                            <a href="#" data-bs-toggle="modal" data-bs-target="#downloadModal">Download it here</a>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Desktop - Show QR Code -->
            <div id="desktop-prompt">
                <div class="card shadow-sm mb-4">
                    <div class="card-body py-4">
                        <p class="mb-3">Scan this QR code with your phone to subscribe:</p>
                        <div class="d-flex justify-content-center mb-3">
                            <img src="{{ route('subscribe.qr', $organisation->subscribe_code) }}" 
                                 alt="QR Code" 
                                 class="img-fluid" 
                                 style="max-width: 250px;">
                        </div>
                        <p class="text-muted small mb-0">
                            Or visit: <code>{{ url()->current() }}</code>
                        </p>
                    </div>
                </div>
            </div>

            @if($organisation->url)
                <p class="mt-4">
                    <a href="{{ $organisation->url }}" target="_blank" class="text-decoration-none">
                        Visit {{ $organisation->name }}'s website →
                    </a>
                </p>
            @endif
        </div>
    </div>
</div>

<!-- Download App Modal -->
<div class="modal fade" id="downloadModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Download Notifi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <p>Get the Notifi app to subscribe and receive notifications:</p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="#" class="btn btn-dark">
                        <i class="bi bi-apple me-2"></i>App Store
                    </a>
                    <a href="#" class="btn btn-success">
                        <i class="bi bi-google-play me-2"></i>Google Play
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Detect mobile and show appropriate prompt
    const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
    
    if (isMobile) {
        document.getElementById('mobile-prompt').classList.remove('d-none');
        document.getElementById('desktop-prompt').classList.add('d-none');
        
        // Try to open app automatically after a short delay
        setTimeout(function() {
            window.location.href = '{{ $deepLink }}';
        }, 500);
    }
</script>
@endpush
@endsection
