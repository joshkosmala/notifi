<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Organisation;
use App\Models\User;
use App\Services\TimezoneService;
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

    public function store(RegisterRequest $request)
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
}
