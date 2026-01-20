<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SubscriberController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return view('welcome');
});

// OAuth routes (publicly accessible for redirect flow)
Route::get('/auth/{provider}/redirect', [SocialiteController::class, 'redirect'])->name('socialite.redirect');
Route::get('/auth/{provider}/callback', [SocialiteController::class, 'callback'])->name('socialite.callback');

// Guest routes (only for non-authenticated users)
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);

    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

// Complete registration (for OAuth users without organisation)
Route::get('/register/complete', [RegisterController::class, 'complete'])->name('register.complete')->middleware('auth');
Route::post('/register/complete', [RegisterController::class, 'storeOrganisation'])->name('register.complete.store')->middleware('auth');

// Logout (requires auth)
Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// Org Admin routes (requires auth)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('notifications', NotificationController::class);

    Route::resource('subscribers', SubscriberController::class)->only(['index', 'show', 'destroy']);
    Route::post('/subscribers/{subscriber}/resubscribe', [SubscriberController::class, 'resubscribe'])->name('subscribers.resubscribe');
});

// Super Admin routes (requires auth + super admin)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'super.admin'])->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/organisations', function () {
        return redirect('/admin'); // Placeholder
    })->name('organisations.index');
});
