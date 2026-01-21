<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organisation extends Model
{
    /** @use HasFactory<\Database\Factories\OrganisationFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'latitude',
        'longitude',
        'timezone',
        'url',
        'phone',
        'email',
        'facebook_page_id',
        'facebook_page_name',
        'facebook_page_token',
        'x_user_id',
        'x_username',
        'x_access_token',
        'x_refresh_token',
    ];

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'facebook_page_token' => 'encrypted',
            'x_access_token' => 'encrypted',
            'x_refresh_token' => 'encrypted',
        ];
    }

    /**
     * Administrators who manage this organisation.
     */
    public function administrators(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Subscribers who receive notifications from this organisation.
     */
    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(Subscriber::class)
            ->withPivot('unsubscribed_at')
            ->withTimestamps();
    }

    /**
     * Notifications sent by this organisation.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Check if the organisation is verified.
     */
    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }

    /**
     * Check if Facebook Page is connected.
     */
    public function hasFacebookPage(): bool
    {
        return $this->facebook_page_id !== null && $this->facebook_page_token !== null;
    }

    /**
     * Check if X account is connected.
     */
    public function hasXAccount(): bool
    {
        return $this->x_user_id !== null && $this->x_access_token !== null;
    }

    /**
     * Disconnect Facebook Page.
     */
    public function disconnectFacebook(): void
    {
        $this->update([
            'facebook_page_id' => null,
            'facebook_page_name' => null,
            'facebook_page_token' => null,
        ]);
    }

    /**
     * Disconnect X account.
     */
    public function disconnectX(): void
    {
        $this->update([
            'x_user_id' => null,
            'x_username' => null,
            'x_access_token' => null,
            'x_refresh_token' => null,
        ]);
    }
}
