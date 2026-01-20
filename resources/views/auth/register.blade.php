@extends('layouts.guest')

@section('title', 'Register')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Create Your Account</h4>
                    <small>Register yourself and your organisation in one step</small>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        {{-- Your Details --}}
                        <h5 class="mb-3 text-muted">👤 Your Details</h5>

                        <div class="mb-3">
                            <label for="name" class="form-label">Your Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name') }}" required autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       id="password" name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control"
                                       id="password_confirmation" name="password_confirmation" required>
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- Organisation Details --}}
                        <h5 class="mb-3 text-muted">🏢 Organisation Details</h5>

                        <div class="mb-3">
                            <label for="organisation_name" class="form-label">Organisation Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('organisation_name') is-invalid @enderror"
                                   id="organisation_name" name="organisation_name" value="{{ old('organisation_name') }}" required>
                            @error('organisation_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="organisation_email" class="form-label">Billing Email <small class="text-muted">(optional, defaults to your email)</small></label>
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
                                Create Account & Organisation
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center bg-light">
                    Already have an account? <a href="{{ route('login') }}">Log in</a>
                </div>
            </div>
        </div>
    </div>
@endsection
