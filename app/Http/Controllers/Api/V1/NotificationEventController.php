<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationEventController extends Controller
{
    /**
     * Record a notification event (open or link_click).
     *
     * POST /api/v1/notifications/{notification}/events
     */
    public function store(Request $request, Notification $notification): JsonResponse
    {
        $request->validate([
            'event_type' => ['required', 'in:open,link_click'],
        ]);

        $subscriber = $request->user();

        // Verify subscriber has access to this notification (is subscribed to the org)
        $isSubscribed = $subscriber->activeOrganisations()
            ->where('organisations.id', $notification->organisation_id)
            ->exists();

        if (! $isSubscribed) {
            return response()->json([
                'message' => 'You are not subscribed to this organisation.',
            ], 403);
        }

        // Record the event (unique per subscriber per event type)
        NotificationEvent::record(
            $notification->id,
            $subscriber->id,
            $request->input('event_type')
        );

        return response()->json([
            'message' => 'Event recorded.',
        ]);
    }
}
