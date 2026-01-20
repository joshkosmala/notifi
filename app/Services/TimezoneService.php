<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TimezoneService
{
    /**
     * Get timezone from coordinates using Mapbox API.
     */
    public static function fromCoordinates(?float $latitude, ?float $longitude): string
    {
        if ($latitude === null || $longitude === null) {
            return 'UTC';
        }

        // Use Google's timezone API (free tier) or fallback to approximate
        try {
            $timestamp = now()->timestamp;
            $response = Http::get('https://maps.googleapis.com/maps/api/timezone/json', [
                'location' => "{$latitude},{$longitude}",
                'timestamp' => $timestamp,
                'key' => config('services.google.timezone_api_key'),
            ]);

            if ($response->successful() && $response->json('status') === 'OK') {
                return $response->json('timeZoneId');
            }
        } catch (\Exception $e) {
            // Fall through to approximation
        }

        // Fallback: Approximate timezone from longitude
        return self::approximateFromCoordinates($latitude, $longitude);
    }

    /**
     * Approximate timezone based on coordinates.
     * This is a simplified approach for major regions.
     */
    public static function approximateFromCoordinates(float $latitude, float $longitude): string
    {
        // New Zealand (rough bounds)
        if ($latitude >= -47.5 && $latitude <= -34.0 && $longitude >= 166.0 && $longitude <= 179.0) {
            return 'Pacific/Auckland';
        }

        // Australia regions (simplified)
        if ($latitude >= -44.0 && $latitude <= -10.0 && $longitude >= 113.0 && $longitude <= 154.0) {
            // Western Australia
            if ($longitude < 129.0) {
                return 'Australia/Perth';
            }
            // Northern Territory / South Australia
            if ($longitude < 138.0) {
                return $latitude > -26.0 ? 'Australia/Darwin' : 'Australia/Adelaide';
            }
            // Queensland / NSW / Victoria / Tasmania
            if ($latitude > -29.0) {
                return 'Australia/Brisbane'; // QLD (no DST)
            }

            return 'Australia/Sydney'; // NSW/VIC/TAS
        }

        // United States (simplified by longitude)
        if ($latitude >= 24.0 && $latitude <= 50.0 && $longitude >= -125.0 && $longitude <= -66.0) {
            if ($longitude < -115.0) {
                return 'America/Los_Angeles'; // Pacific
            }
            if ($longitude < -102.0) {
                return 'America/Denver'; // Mountain
            }
            if ($longitude < -87.0) {
                return 'America/Chicago'; // Central
            }

            return 'America/New_York'; // Eastern
        }

        // United Kingdom
        if ($latitude >= 49.0 && $latitude <= 61.0 && $longitude >= -8.0 && $longitude <= 2.0) {
            return 'Europe/London';
        }

        // Default to UTC
        return 'UTC';
    }
}
