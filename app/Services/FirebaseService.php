<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    private string $projectId;

    private ?string $credentialsPath;

    private ?string $accessToken = null;

    public function __construct()
    {
        $this->projectId = config('firebase.project_id', '');
        $this->credentialsPath = config('firebase.credentials_path');
    }

    /**
     * Check if Firebase is configured.
     */
    public function isConfigured(): bool
    {
        return ! empty($this->projectId) && $this->credentialsPath && file_exists($this->credentialsPath);
    }

    /**
     * Get an OAuth2 access token for FCM.
     */
    private function getAccessToken(): ?string
    {
        if ($this->accessToken) {
            return $this->accessToken;
        }

        if (! $this->isConfigured()) {
            Log::warning('Firebase is not configured');

            return null;
        }

        try {
            $client = new GoogleClient;
            $client->setAuthConfig($this->credentialsPath);
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

            $token = $client->fetchAccessTokenWithAssertion();
            $this->accessToken = $token['access_token'] ?? null;

            return $this->accessToken;
        } catch (\Exception $e) {
            Log::error('Failed to get Firebase access token: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Send a push notification to a single device.
     *
     * @param  array<string, mixed>  $data
     */
    public function sendToDevice(string $fcmToken, string $title, string $body, array $data = []): bool
    {
        $accessToken = $this->getAccessToken();

        if (! $accessToken) {
            return false;
        }

        $message = [
            'message' => [
                'token' => $fcmToken,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => array_map('strval', $data),
                'android' => [
                    'priority' => 'high',
                    'notification' => [
                        'channel_id' => 'notifi_default',
                        'sound' => 'default',
                    ],
                ],
                'apns' => [
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                            'badge' => 1,
                        ],
                    ],
                ],
            ],
        ];

        try {
            $response = Http::withToken($accessToken)
                ->post(
                    "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send",
                    $message
                );

            if ($response->successful()) {
                Log::info('FCM notification sent successfully', ['token' => substr($fcmToken, 0, 20).'...']);

                return true;
            }

            Log::error('FCM notification failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('FCM notification exception: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Send a push notification to multiple devices.
     *
     * @param  array<string>  $fcmTokens
     * @param  array<string, mixed>  $data
     * @return array<string, int>
     */
    public function sendToDevices(array $fcmTokens, string $title, string $body, array $data = []): array
    {
        $success = 0;
        $failure = 0;

        foreach ($fcmTokens as $token) {
            if ($this->sendToDevice($token, $title, $body, $data)) {
                $success++;
            } else {
                $failure++;
            }
        }

        return [
            'success' => $success,
            'failure' => $failure,
        ];
    }
}
