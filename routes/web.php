<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrganisationSettingsController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\SubscribeController;
use App\Http\Controllers\SubscriberController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return view('welcome');
});

// Public subscribe page (for QR codes and links)
Route::get('/s/{code}', [SubscribeController::class, 'show'])->name('subscribe.show');
Route::get('/s/{code}/qr', [SubscribeController::class, 'qrCode'])->name('subscribe.qr');

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

    // Organisation Settings
    Route::get('/settings', [OrganisationSettingsController::class, 'index'])->name('settings.index');

    // Social Media OAuth for Organisation
    Route::get('/settings/facebook/redirect', [SocialAuthController::class, 'facebookRedirect'])->name('settings.facebook.redirect');
    Route::get('/settings/facebook/callback', [SocialAuthController::class, 'facebookCallback'])->name('settings.facebook.callback');
    Route::delete('/settings/facebook', [OrganisationSettingsController::class, 'disconnectFacebook'])->name('settings.facebook.disconnect');

    Route::get('/settings/x/redirect', [SocialAuthController::class, 'xRedirect'])->name('settings.x.redirect');
    Route::get('/settings/x/callback', [SocialAuthController::class, 'xCallback'])->name('settings.x.callback');
    Route::delete('/settings/x', [OrganisationSettingsController::class, 'disconnectX'])->name('settings.x.disconnect');
});

// Super Admin routes (requires auth + super admin)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'super.admin'])->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/organisations', [AdminDashboardController::class, 'organisations'])->name('organisations.index');
    Route::get('/organisations/{organisation}', [AdminDashboardController::class, 'showOrganisation'])->name('organisations.show');
    Route::get('/notifications/{notification}', [AdminDashboardController::class, 'showNotification'])->name('notifications.show');
});
