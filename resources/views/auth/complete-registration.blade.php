@extends('layouts.guest')

@section('title', 'Complete Registration')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Complete Your Registration</h4>
                    <small>Just one more step - add your organisation details</small>
                </div>
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if(session('info'))
                        <div class="alert alert-info">{{ session('info') }}</div>
                    @endif

                    <div class="alert alert-light border mb-4">
                        <strong>Welcome, {{ auth()->user()->name }}!</strong><br>
                        <small class="text-muted">Signed in as {{ auth()->user()->email }}</small>
                    </div>

                    <form method="POST" action="{{ route('register.complete.store') }}">
                        @csrf

                        <h5 class="mb-3 text-muted">🏢 Organisation Details</h5>

                        <div class="mb-3">
                            <label for="organisation_name" class="form-label">Organisation Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('organisation_name') is-invalid @enderror"
                                   id="organisation_name" name="organisation_name" value="{{ old('organisation_name') }}" required autofocus>
                            @error('organisation_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="organisation_email" class="form-label">Billing Email <small class="text-muted">(optional, defaults to {{ auth()->user()->email }})</small></label>
                            <input type="email" class="form-control @error('organisation_email') is-invalid @enderror"
                                   id="organisation_email" name="organisation_email" value="{{ old('organisation_email') }}">
                            @error('organisation_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="organisation_phone" class="form-label">Phone <small class="text-muted">(optional)</small></label>
                            <input type="tel" class="form-control @error('organisation_phone') is-invalid @enderror"
                                   id="organisation_phone" name="organisation_phone" value="{{ old('organisation_phone') }}"
                                   data-phone-input>
                            @error('organisation_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="organisation_address" class="form-label">Address <small class="text-muted">(optional)</small></label>
                            <input type="text" class="form-control @error('organisation_address') is-invalid @enderror"
                                   id="organisation_address" name="organisation_address" value="{{ old('organisation_address') }}"
                                   data-address-input autocomplete="off">
                            <input type="hidden" id="organisation_latitude" name="organisation_latitude" value="{{ old('organisation_latitude') }}">
                            <input type="hidden" id="organisation_longitude" name="organisation_longitude" value="{{ old('organisation_longitude') }}">
                            @error('organisation_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="organisation_url" class="form-label">Website <small class="text-muted">(optional)</small></label>
                            <input type="url" class="form-control @error('organisation_url') is-invalid @enderror"
                                   id="organisation_url" name="organisation_url" value="{{ old('organisation_url') }}">
                            @error('organisation_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                Complete Registration
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
