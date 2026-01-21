<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SocialAuthController extends Controller
{
    /**
     * Get the current user's organisation.
     */
    protected function getOrganisation()
    {
        return auth()->user()->organisations()->firstOrFail();
    }

    /**
     * Redirect to Facebook for Page authorization.
     */
    public function facebookRedirect(): RedirectResponse
    {
        $clientId = config('services.facebook.client_id');
        $redirectUri = route('settings.facebook.callback');

        // Request pages_manage_posts and pages_read_engagement for posting to Pages
        $scopes = 'pages_show_list,pages_read_engagement,pages_manage_posts';

        $url = 'https://www.facebook.com/v18.0/dialog/oauth?'.http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'scope' => $scopes,
            'response_type' => 'code',
            'state' => csrf_token(),
        ]);

        return redirect($url);
    }

    /**
     * Handle Facebook callback and get Page access token.
     */
    public function facebookCallback(): RedirectResponse
    {
        $code = request('code');

        if (! $code) {
            return redirect()->route('settings.index')
                ->with('error', 'Facebook authorization was cancelled.');
        }

        try {
            // Exchange code for user access token
            $response = Http::get('https://graph.facebook.com/v18.0/oauth/access_token', [
                'client_id' => config('services.facebook.client_id'),
                'client_secret' => config('services.facebook.client_secret'),
                'redirect_uri' => route('settings.facebook.callback'),
                'code' => $code,
            ]);

            if (! $response->successful()) {
                throw new \Exception('Failed to get access token');
            }

            $userAccessToken = $response->json('access_token');

            // Get list of pages the user manages
            $pagesResponse = Http::get('https://graph.facebook.com/v18.0/me/accounts', [
                'access_token' => $userAccessToken,
            ]);

            if (! $pagesResponse->successful()) {
                throw new \Exception('Failed to get pages');
            }

            $pages = $pagesResponse->json('data', []);

            if (empty($pages)) {
                return redirect()->route('settings.index')
                    ->with('error', 'No Facebook Pages found. Please make sure you manage at least one Facebook Page.');
            }

            // For now, use the first page. In the future, let user choose.
            $page = $pages[0];

            $organisation = $this->getOrganisation();
            $organisation->update([
                'facebook_page_id' => $page['id'],
                'facebook_page_name' => $page['name'],
                'facebook_page_token' => $page['access_token'],
            ]);

            return redirect()->route('settings.index')
                ->with('success', "Facebook Page '{$page['name']}' connected successfully!");

        } catch (\Exception $e) {
            Log::error('Facebook OAuth error: '.$e->getMessage());

            return redirect()->route('settings.index')
                ->with('error', 'Failed to connect Facebook Page. Please try again.');
        }
    }

    /**
     * Redirect to X (Twitter) for authorization.
     */
    public function xRedirect(): RedirectResponse
    {
        // X uses OAuth 2.0 with PKCE
        $clientId = config('services.x.client_id');
        $redirectUri = route('settings.x.callback');

        // Generate PKCE code verifier and challenge
        $codeVerifier = bin2hex(random_bytes(32));
        $codeChallenge = rtrim(strtr(base64_encode(hash('sha256', $codeVerifier, true)), '+/', '-_'), '=');

        // Store verifier in session for callback
        session(['x_code_verifier' => $codeVerifier]);

        // X OAuth 2.0 scopes for posting tweets
        $scopes = 'tweet.read tweet.write users.read offline.access';

        $url = 'https://twitter.com/i/oauth2/authorize?'.http_build_query([
            'response_type' => 'code',
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'scope' => $scopes,
            'state' => csrf_token(),
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => 'S256',
        ]);

        return redirect($url);
    }

    /**
     * Handle X (Twitter) callback.
     */
    public function xCallback(): RedirectResponse
    {
        $code = request('code');
        $codeVerifier = session('x_code_verifier');

        if (! $code || ! $codeVerifier) {
            return redirect()->route('settings.index')
                ->with('error', 'X authorization was cancelled.');
        }

        try {
            // Exchange code for access token
            $response = Http::withBasicAuth(
                config('services.x.client_id'),
                config('services.x.client_secret')
            )->asForm()->post('https://api.twitter.com/2/oauth2/token', [
                'code' => $code,
                'grant_type' => 'authorization_code',
                'redirect_uri' => route('settings.x.callback'),
                'code_verifier' => $codeVerifier,
            ]);

            if (! $response->successful()) {
                throw new \Exception('Failed to get access token: '.$response->body());
            }

            $tokenData = $response->json();
            $accessToken = $tokenData['access_token'];
            $refreshToken = $tokenData['refresh_token'] ?? null;

            // Get user info
            $userResponse = Http::withToken($accessToken)
                ->get('https://api.twitter.com/2/users/me');

            if (! $userResponse->successful()) {
                throw new \Exception('Failed to get user info');
            }

            $userData = $userResponse->json('data');

            $organisation = $this->getOrganisation();
            $organisation->update([
                'x_user_id' => $userData['id'],
                'x_username' => $userData['username'],
                'x_access_token' => $accessToken,
                'x_refresh_token' => $refreshToken,
            ]);

            // Clear session
            session()->forget('x_code_verifier');

            return redirect()->route('settings.index')
                ->with('success', "X account @{$userData['username']} connected successfully!");

        } catch (\Exception $e) {
            Log::error('X OAuth error: '.$e->getMessage());

            return redirect()->route('settings.index')
                ->with('error', 'Failed to connect X account. Please try again.');
        }
    }
}
