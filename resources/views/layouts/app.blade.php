<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Notifi') }} - @yield('title', 'Dashboard')</title>

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <script>
        // Prevent flash of wrong theme
        (function() {
            const saved = localStorage.getItem('theme');
            const prefers = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            document.documentElement.setAttribute('data-bs-theme', saved || prefers);
        })();
    </script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ url('/dashboard') }}">
                <span class="text-warning">📢</span> Notifi
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}" href="{{ route('notifications.index') }}">Notifications</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('subscribers.*') ? 'active' : '' }}" href="{{ route('subscribers.index') }}">Subscribers</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    @auth
                        @if(auth()->user()->isSuperAdmin())
                            <li class="nav-item">
                                <a class="nav-link text-warning" href="{{ route('admin.dashboard') }}">🔐 Admin</a>
                            </li>
                        @endif
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" id="themeDropdown">
                                <i class="bi bi-circle-half"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <button type="button" class="dropdown-item d-flex align-items-center" onclick="setTheme('light')">
                                        <i class="bi bi-sun me-2"></i> Light
                                        <i class="bi bi-check2 ms-auto theme-check" data-theme="light"></i>
                                    </button>
                                </li>
                                <li>
                                    <button type="button" class="dropdown-item d-flex align-items-center" onclick="setTheme('dark')">
                                        <i class="bi bi-moon-stars me-2"></i> Dark
                                        <i class="bi bi-check2 ms-auto theme-check" data-theme="dark"></i>
                                    </button>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <button type="button" class="dropdown-item d-flex align-items-center" onclick="setTheme('auto')">
                                        <i class="bi bi-circle-half me-2"></i> Auto
                                        <i class="bi bi-check2 ms-auto theme-check" data-theme="auto"></i>
                                    </button>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('settings.index') }}">
                                        ⚙️ Organisation Settings
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Log Out</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4">
        <div class="container">
            @yield('content')
        </div>
    </main>

    <footer class="py-4 mt-auto bg-body-tertiary">
        <div class="container text-center text-body-secondary">
            <small>&copy; {{ date('Y') }} Notifi. Built with Laravel.</small>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
