<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Organisation;
use App\Models\User;
use App\Services\TimezoneService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Determine timezone from coordinates
        $latitude = $request->input('organisation_latitude');
        $longitude = $request->input('organisation_longitude');
        $timezone = TimezoneService::fromCoordinates(
            $latitude ? (float) $latitude : null,
            $longitude ? (float) $longitude : null
        );

        $user = DB::transaction(function () use ($validated, $latitude, $longitude, $timezone) {
            // Create the user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            // Create the organisation
            $organisation = Organisation::create([
                'name' => $validated['organisation_name'],
                'email' => $validated['organisation_email'] ?? $validated['email'],
                'phone' => $validated['organisation_phone'] ?? null,
                'address' => $validated['organisation_address'] ?? null,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'timezone' => $timezone,
                'url' => $validated['organisation_url'] ?? null,
            ]);

            // Attach user as owner
            $user->organisations()->attach($organisation, ['role' => 'owner']);

            return $user;
        });

        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', 'Welcome to Notifi! Your organisation has been created.');
    }

    /**
     * Show the complete registration form for OAuth users.
     */
    public function complete(): View|RedirectResponse
    {
        // If user already has an organisation, redirect to dashboard
        if (auth()->user()->organisations()->exists()) {
            return redirect()->route('dashboard');
        }

        return view('auth.complete-registration');
    }

    /**
     * Store organisation for OAuth users completing registration.
     */
    public function storeOrganisation(Request $request): RedirectResponse
    {
        $user = auth()->user();

        // If user already has an organisation, redirect to dashboard
        if ($user->organisations()->exists()) {
            return redirect()->route('dashboard');
        }

        $validated = $request->validate([
            'organisation_name' => ['required', 'string', 'max:255'],
            'organisation_email' => ['nullable', 'email', 'max:255'],
            'organisation_phone' => ['nullable', 'string', 'max:50'],
            'organisation_address' => ['nullable', 'string', 'max:500'],
            'organisation_url' => ['nullable', 'url', 'max:255'],
        ]);

        // Determine timezone from coordinates
        $latitude = $request->input('organisation_latitude');
        $longitude = $request->input('organisation_longitude');
        $timezone = TimezoneService::fromCoordinates(
            $latitude ? (float) $latitude : null,
            $longitude ? (float) $longitude : null
        );

        $organisation = Organisation::create([
            'name' => $validated['organisation_name'],
            'email' => $validated['organisation_email'] ?? $user->email,
            'phone' => $validated['organisation_phone'] ?? null,
            'address' => $validated['organisation_address'] ?? null,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'timezone' => $timezone,
            'url' => $validated['organisation_url'] ?? null,
        ]);

        $user->organisations()->attach($organisation, ['role' => 'owner']);

        return redirect()->route('dashboard')
            ->with('success', 'Welcome to Notifi! Your organisation has been created.');
    }
}
