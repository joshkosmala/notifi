<?php

namespace App\Models;

use App\Services\FirebaseService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

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
     * Events (opens, clicks) for this notification.
     */
    public function events(): HasMany
    {
        return $this->hasMany(NotificationEvent::class);
    }

    /**
     * Get unique open count.
     */
    public function getOpenCount(): int
    {
        return $this->events()->where('event_type', 'open')->count();
    }

    /**
     * Get unique link click count.
     */
    public function getClickCount(): int
    {
        return $this->events()->where('event_type', 'link_click')->count();
    }

    /**
     * Get subscriber count at time notification was sent.
     */
    public function getRecipientCount(): int
    {
        return $this->organisation
            ->subscribers()
            ->whereNull('organisation_subscriber.unsubscribed_at')
            ->count();
    }

    /**
     * Get analytics summary for this notification.
     *
     * @return array{opens: int, clicks: int, recipients: int, open_rate: float, click_rate: float}
     */
    public function getAnalytics(): array
    {
        $opens = $this->getOpenCount();
        $clicks = $this->getClickCount();
        $recipients = $this->getRecipientCount();

        return [
            'opens' => $opens,
            'clicks' => $clicks,
            'recipients' => $recipients,
            'open_rate' => $recipients > 0 ? round(($opens / $recipients) * 100, 1) : 0,
            'click_rate' => $recipients > 0 ? round(($clicks / $recipients) * 100, 1) : 0,
        ];
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
     * Mark the notification as sent and send push notifications.
     */
    public function markAsSent(): bool
    {
        $saved = $this->forceFill([
            'sent_at' => $this->freshTimestamp(),
        ])->save();

        if ($saved) {
            $this->sendPushNotifications();
        }

        return $saved;
    }

    /**
     * Send push notifications to all subscribers with FCM tokens.
     */
    protected function sendPushNotifications(): void
    {
        $firebase = app(FirebaseService::class);

        if (! $firebase->isConfigured()) {
            Log::info('Firebase not configured, skipping push notifications');

            return;
        }

        $subscribers = $this->organisation
            ->subscribers()
            ->whereNull('organisation_subscriber.unsubscribed_at')
            ->whereNotNull('fcm_token')
            ->get();

        if ($subscribers->isEmpty()) {
            Log::info('No subscribers with FCM tokens for notification', ['notification_id' => $this->id]);

            return;
        }

        $tokens = $subscribers->pluck('fcm_token')->toArray();

        $result = $firebase->sendToDevices(
            $tokens,
            $this->title,
            $this->body,
            [
                'notification_id' => (string) $this->id,
                'organisation_id' => (string) $this->organisation_id,
                'link' => $this->link ?? '',
            ]
        );

        Log::info('Push notifications sent', [
            'notification_id' => $this->id,
            'success' => $result['success'],
            'failure' => $result['failure'],
        ]);
    }
}
