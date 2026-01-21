<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationEvent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'notification_id',
        'subscriber_id',
        'event_type',
    ];

    /**
     * The notification this event belongs to.
     */
    public function notification(): BelongsTo
    {
        return $this->belongsTo(Notification::class);
    }

    /**
     * The subscriber who triggered this event.
     */
    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(Subscriber::class);
    }

    /**
     * Record an event (unique per subscriber per event type).
     */
    public static function record(int $notificationId, int $subscriberId, string $eventType): ?self
    {
        return static::firstOrCreate([
            'notification_id' => $notificationId,
            'subscriber_id' => $subscriberId,
            'event_type' => $eventType,
        ]);
    }
}
