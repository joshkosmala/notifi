<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    /**
     * Supported OAuth providers.
     */
    protected array $providers = ['google', 'microsoft'];

    /**
     * Redirect to the OAuth provider.
     */
    public function redirect(string $provider): RedirectResponse
    {
        if (! in_array($provider, $this->providers)) {
            abort(404);
        }

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle the OAuth callback.
     */
    public function callback(string $provider): RedirectResponse
    {
        if (! in_array($provider, $this->providers)) {
            abort(404);
        }

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Unable to authenticate. Please try again.');
        }

        // Find existing user by email or OAuth provider ID
        $user = User::where('email', $socialUser->getEmail())->first();

        if ($user) {
            // Update OAuth details if not set
            if (! $user->oauth_provider) {
                $user->update([
                    'oauth_provider' => $provider,
                    'oauth_id' => $socialUser->getId(),
                ]);
            }

            Auth::login($user, remember: true);

            if ($user->isSuperAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            // Check if user has an organisation
            if ($user->organisations()->exists()) {
                return redirect()->route('dashboard');
            }

            // User exists but has no organisation - send to complete registration
            return redirect()->route('register.complete')
                ->with('info', 'Please complete your registration by adding your organisation details.');
        }

        // Create new user - they'll need to complete registration with org details
        $user = User::create([
            'name' => $socialUser->getName(),
            'email' => $socialUser->getEmail(),
            'oauth_provider' => $provider,
            'oauth_id' => $socialUser->getId(),
            'password' => null, // No password for OAuth users
        ]);

        Auth::login($user, remember: true);

        return redirect()->route('register.complete')
            ->with('success', 'Account created! Please add your organisation details to continue.');
    }
}
