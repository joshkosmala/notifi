<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Sanctum\HasApiTokens;

class Subscriber extends Model
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'phone',
        'name',
        'email',
        'device_token',
        'device_platform',
        'verification_code',
        'verification_code_expires_at',
    ];

    protected $hidden = [
        'verification_code',
        'verification_code_expires_at',
    ];

    protected function casts(): array
    {
        return [
            'phone_verified_at' => 'datetime',
            'verification_code_expires_at' => 'datetime',
        ];
    }

    /**
     * Organisations this subscriber follows.
     */
    public function organisations(): BelongsToMany
    {
        return $this->belongsToMany(Organisation::class)
            ->withPivot('unsubscribed_at')
            ->withTimestamps();
    }

    /**
     * Get active (not unsubscribed) organisations.
     */
    public function activeOrganisations(): BelongsToMany
    {
        return $this->organisations()->whereNull('organisation_subscriber.unsubscribed_at');
    }

    /**
     * Check if the subscriber's phone is verified.
     */
    public function isPhoneVerified(): bool
    {
        return $this->phone_verified_at !== null;
    }

    /**
     * Mark the phone as verified.
     */
    public function markPhoneAsVerified(): bool
    {
        return $this->forceFill([
            'phone_verified_at' => $this->freshTimestamp(),
            'verification_code' => null,
            'verification_code_expires_at' => null,
        ])->save();
    }

    /**
     * Generate a new verification code.
     */
    public function generateVerificationCode(): string
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $this->forceFill([
            'verification_code' => $code,
            'verification_code_expires_at' => now()->addMinutes(10),
        ])->save();

        return $code;
    }

    /**
     * Check if a verification code is valid.
     */
    public function isValidVerificationCode(string $code): bool
    {
        return $this->verification_code === $code
            && $this->verification_code_expires_at
            && $this->verification_code_expires_at->isFuture();
    }
}
