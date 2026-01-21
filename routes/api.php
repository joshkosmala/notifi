<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DeviceController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\OrganisationController;
use App\Http\Controllers\Api\V1\SubscriptionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API V1 Routes
|--------------------------------------------------------------------------
|
| Mobile app API endpoints for subscribers.
|
*/

Route::prefix('v1')->group(function () {
    // Public auth routes
    Route::post('/auth/request-code', [AuthController::class, 'requestCode']);
    Route::post('/auth/verify', [AuthController::class, 'verify']);

    // Public organisation listing
    Route::get('/organisations', [OrganisationController::class, 'index']);
    Route::get('/organisations/{organisation}', [OrganisationController::class, 'show']);

    // Protected routes (require Sanctum token)
    Route::middleware('auth:sanctum')->group(function () {
        // Auth
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::put('/auth/me', [AuthController::class, 'update']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        // Subscriptions
        Route::get('/subscriptions', [SubscriptionController::class, 'index']);
        Route::post('/subscriptions', [SubscriptionController::class, 'store']);
        Route::delete('/subscriptions/{organisation}', [SubscriptionController::class, 'destroy']);

        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::get('/notifications/{notification}', [NotificationController::class, 'show']);

        // Device (push notifications)
        Route::post('/device', [DeviceController::class, 'store']);
        Route::delete('/device', [DeviceController::class, 'destroy']);
    });
});
