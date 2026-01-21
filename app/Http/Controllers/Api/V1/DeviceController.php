<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    /**
     * Register device for push notifications.
     *
     * POST /api/v1/device
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
            'platform' => ['required', 'in:ios,android'],
        ]);

        $subscriber = $request->user();

        $subscriber->update([
            'fcm_token' => $request->input('token'),
            'device_platform' => $request->input('platform'),
        ]);

        return response()->json([
            'message' => 'Device registered for push notifications.',
        ]);
    }

    /**
     * Remove device token (disable push notifications).
     *
     * DELETE /api/v1/device
     */
    public function destroy(Request $request): JsonResponse
    {
        $subscriber = $request->user();

        $subscriber->update([
            'fcm_token' => null,
            'device_platform' => null,
        ]);

        return response()->json([
            'message' => 'Device unregistered from push notifications.',
        ]);
    }
}
