<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    /** @use HasFactory<\Database\Factories\NotificationFactory> */
    use HasFactory;

    protected $fillable = [
        'organisation_id',
        'title',
        'body',
        'link',
        'scheduled_for',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'scheduled_for' => 'datetime',
        ];
    }

    /**
     * The organisation that created this notification.
     */
    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    /**
     * Check if the notification has been sent.
     */
    public function isSent(): bool
    {
        return $this->sent_at !== null;
    }

    /**
     * Check if the notification is scheduled for future delivery.
     */
    public function isScheduled(): bool
    {
        return $this->scheduled_for !== null && $this->sent_at === null;
    }

    /**
     * Check if the notification is a draft (not sent and not scheduled).
     */
    public function isDraft(): bool
    {
        return $this->sent_at === null && $this->scheduled_for === null;
    }

    /**
     * Get the organisation's timezone.
     */
    public function getTimezone(): string
    {
        return $this->organisation->timezone ?? 'UTC';
    }

    /**
     * Format a datetime in the organisation's timezone.
     */
    public function formatInTimezone(?\Carbon\Carbon $datetime, string $format = 'd M Y, H:i'): ?string
    {
        if ($datetime === null) {
            return null;
        }

        $tz = $this->getTimezone();
        $formatted = $datetime->setTimezone($tz)->format($format);
        $abbr = $datetime->setTimezone($tz)->format('T');

        return "{$formatted} ({$abbr})";
    }

    /**
     * Mark the notification as sent.
     */
    public function markAsSent(): bool
    {
        return $this->forceFill([
            'sent_at' => $this->freshTimestamp(),
        ])->save();
    }
}
