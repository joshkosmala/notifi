@extends('layouts.app')

@section('title', 'No Organisation')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card text-center py-5">
                <div class="card-body">
                    <h1 class="display-1 mb-3">🏢</h1>
                    <h3 class="card-title">No Organisation Found</h3>
                    <p class="card-text text-muted mb-4">
                        Your account is not associated with any organisation yet.
                    </p>
                    <p class="text-muted small">
                        If you believe this is an error, please contact support.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
