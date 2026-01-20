Notifi - Project Summary
Purpose
Notifi is a cross-platform application with:

Mobile apps (iOS/Android) built with React Native
Web-based dashboard built with Laravel + Bootstrap
Scalable API backend to support millions of users
SMS/Email push notification integration


Architecture
Backend: Laravel (PHP 8.3)

Location: ~/Sites/notifi
RESTful API with Laravel Sanctum for token-based authentication
PostgreSQL database for scalability
Redis for queues and caching
Ready for Twilio (SMS) and email provider integration

Mobile: React Native

Location: ~/Sites/NotifiMobile
Single codebase for iOS and Android
Communicates with Laravel via API

Web Dashboard: Laravel Blade + Bootstrap 5

Server-rendered views within the Laravel project
Shared authentication with API


Current Configuration
Laravel .env essentials
DB_CONNECTION=pgsql
DB_DATABASE=notifi
QUEUE_CONNECTION=redis
CACHE_STORE=redis
macOS dev environment

PHP 8.3 via Homebrew
PostgreSQL 16 (running via brew services)
Redis (running via brew services)
Node.js for asset compilation
Android Studio + SDK (API 36)
Xcode pending installation


What's Been Built

Laravel API authentication - Register, login, logout endpoints with Sanctum tokens
API route structure - Protected routes ready for expansion
Queue system - Redis-backed, ready for notifications
WelcomeNotification - Example queued email notification
Bootstrap assets - Configured via Vite


Key Files
~/Sites/notifi/
├── routes/api.php              # API endpoints
├── app/Http/Controllers/Api/
│   ├── AuthController.php      # Register/login/logout
│   └── UserController.php      # User endpoints
├── app/Notifications/
│   └── WelcomeNotification.php # Example notification
├── resources/sass/app.scss     # Bootstrap styles
└── .env                        # Environment config

Next Steps

Define your data models - What entities does Notifi manage? Create migrations and models
Build out API endpoints - CRUD operations for your core features
Set up API versioning - Prefix routes with /api/v1/
Configure mail provider - Add SendGrid/Mailgun/SES credentials to .env
Set up Twilio - For SMS notifications
Build dashboard views - Blade templates for web admin
Connect React Native to API - Axios calls to http://localhost:8000/api


Running Locally
Terminal 1 - Laravel API:
bashcd ~/Sites/notifi
php artisan serve
Terminal 2 - Queue worker (when testing notifications):
bashcd ~/Sites/notifi
php artisan queue:work
Terminal 3 - React Native (once set up):
bashcd ~/Sites/NotifiMobile
npx react-native run-android
