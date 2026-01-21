<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Organisation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SocialPostingService
{
    /**
     * Post a notification to Facebook Page.
     */
    public function postToFacebook(Notification $notification): bool
    {
        $organisation = $notification->organisation;

        if (! $organisation->hasFacebookPage()) {
            Log::warning("Cannot post to Facebook: Organisation {$organisation->id} has no Facebook Page connected.");

            return false;
        }

        try {
            $message = $notification->title;

            if ($notification->body) {
                $message .= "\n\n".$notification->body;
            }

            $params = [
                'message' => $message,
                'access_token' => $organisation->facebook_page_token,
            ];

            // If there's a link, add it
            if ($notification->link) {
                $params['link'] = $notification->link;
            }

            $response = Http::post(
                "https://graph.facebook.com/v18.0/{$organisation->facebook_page_id}/feed",
                $params
            );

            if ($response->successful()) {
                Log::info("Posted notification {$notification->id} to Facebook Page {$organisation->facebook_page_name}");

                return true;
            }

            Log::error('Failed to post to Facebook: '.$response->body());

            return false;

        } catch (\Exception $e) {
            Log::error('Facebook posting error: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Post a notification to X (Twitter).
     */
    public function postToX(Notification $notification): bool
    {
        $organisation = $notification->organisation;

        if (! $organisation->hasXAccount()) {
            Log::warning("Cannot post to X: Organisation {$organisation->id} has no X account connected.");

            return false;
        }

        try {
            // Build tweet text (max 280 characters)
            $tweetText = $notification->title;

            // Add link if present
            if ($notification->link) {
                // Reserve ~25 chars for t.co shortened link
                $maxTextLength = 280 - 25 - 1; // -1 for space
                if (strlen($tweetText) > $maxTextLength) {
                    $tweetText = substr($tweetText, 0, $maxTextLength - 3).'...';
                }
                $tweetText .= ' '.$notification->link;
            } else {
                if (strlen($tweetText) > 280) {
                    $tweetText = substr($tweetText, 0, 277).'...';
                }
            }

            $response = Http::withToken($organisation->x_access_token)
                ->post('https://api.twitter.com/2/tweets', [
                    'text' => $tweetText,
                ]);

            if ($response->successful()) {
                Log::info("Posted notification {$notification->id} to X @{$organisation->x_username}");

                return true;
            }

            // Check if token expired and try to refresh
            if ($response->status() === 401 && $organisation->x_refresh_token) {
                if ($this->refreshXToken($organisation)) {
                    // Retry with new token
                    return $this->postToX($notification);
                }
            }

            Log::error('Failed to post to X: '.$response->body());

            return false;

        } catch (\Exception $e) {
            Log::error('X posting error: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Refresh X access token using refresh token.
     */
    protected function refreshXToken(Organisation $organisation): bool
    {
        try {
            $response = Http::withBasicAuth(
                config('services.x.client_id'),
                config('services.x.client_secret')
            )->asForm()->post('https://api.twitter.com/2/oauth2/token', [
                'grant_type' => 'refresh_token',
                'refresh_token' => $organisation->x_refresh_token,
            ]);

            if ($response->successful()) {
                $tokenData = $response->json();

                $organisation->update([
                    'x_access_token' => $tokenData['access_token'],
                    'x_refresh_token' => $tokenData['refresh_token'] ?? $organisation->x_refresh_token,
                ]);

                Log::info("Refreshed X token for organisation {$organisation->id}");

                return true;
            }

            Log::error('Failed to refresh X token: '.$response->body());

            return false;

        } catch (\Exception $e) {
            Log::error('X token refresh error: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Post notification to all connected social platforms.
     *
     * @return array<string, bool> Results for each platform
     */
    public function postToAll(Notification $notification, array $platforms = ['facebook', 'x']): array
    {
        $results = [];

        if (in_array('facebook', $platforms)) {
            $results['facebook'] = $this->postToFacebook($notification);
        }

        if (in_array('x', $platforms)) {
            $results['x'] = $this->postToX($notification);
        }

        return $results;
    }
}
