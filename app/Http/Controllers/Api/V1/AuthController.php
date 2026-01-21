<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Request a verification code (send SMS).
     *
     * POST /api/v1/auth/request-code
     */
    public function requestCode(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => ['required', 'string', 'regex:/^\+[1-9]\d{6,14}$/'],
        ]);

        $phone = $request->input('phone');

        // Find or create subscriber
        $subscriber = Subscriber::firstOrCreate(
            ['phone' => $phone],
            ['phone' => $phone]
        );

        // Generate verification code
        $code = $subscriber->generateVerificationCode();

        // TODO: Send SMS via Twilio
        // For now, log the code in development
        Log::info("Verification code for {$phone}: {$code}");

        return response()->json([
            'message' => 'Verification code sent.',
            'expires_in' => 600, // 10 minutes
            // Include code in development for testing
            'code' => app()->environment('local') ? $code : null,
        ]);
    }

    /**
     * Verify the code and return an auth token.
     *
     * POST /api/v1/auth/verify
     */
    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => ['required', 'string', 'regex:/^\+[1-9]\d{6,14}$/'],
            'code' => ['required', 'string', 'size:6'],
        ]);

        $subscriber = Subscriber::where('phone', $request->input('phone'))->first();

        if (! $subscriber) {
            return response()->json([
                'message' => 'Phone number not found. Please request a new code.',
            ], 404);
        }

        if (! $subscriber->isValidVerificationCode($request->input('code'))) {
            return response()->json([
                'message' => 'Invalid or expired verification code.',
            ], 422);
        }

        // Mark phone as verified
        $subscriber->markPhoneAsVerified();

        // Create API token
        $token = $subscriber->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'message' => 'Phone verified successfully.',
            'token' => $token,
            'subscriber' => [
                'id' => $subscriber->id,
                'phone' => $subscriber->phone,
                'name' => $subscriber->name,
                'email' => $subscriber->email,
            ],
        ]);
    }

    /**
     * Get the authenticated subscriber.
     *
     * GET /api/v1/auth/me
     */
    public function me(Request $request): JsonResponse
    {
        $subscriber = $request->user();

        return response()->json([
            'subscriber' => [
                'id' => $subscriber->id,
                'phone' => $subscriber->phone,
                'name' => $subscriber->name,
                'email' => $subscriber->email,
                'phone_verified_at' => $subscriber->phone_verified_at,
            ],
        ]);
    }

    /**
     * Update the authenticated subscriber's profile.
     *
     * PUT /api/v1/auth/me
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        $subscriber = $request->user();
        $subscriber->update($request->only(['name', 'email']));

        return response()->json([
            'message' => 'Profile updated.',
            'subscriber' => [
                'id' => $subscriber->id,
                'phone' => $subscriber->phone,
                'name' => $subscriber->name,
                'email' => $subscriber->email,
            ],
        ]);
    }

    /**
     * Logout (revoke current token).
     *
     * POST /api/v1/auth/logout
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }
}
