<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Notifi') }} - @yield('title', 'Welcome')</title>

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body class="bg-light">
    <div class="min-vh-100 d-flex flex-column justify-content-center py-5">
        <div class="container">
            <div class="text-center mb-4">
                <a href="{{ url('/') }}" class="text-decoration-none">
                    <h1 class="h2 text-dark"><span class="text-warning">📢</span> Notifi</h1>
                </a>
            </div>

            @yield('content')

            <div class="text-center mt-4">
                <small class="text-muted">&copy; {{ date('Y') }} Notifi</small>
            </div>
        </div>
    </div>
</body>
</html>
