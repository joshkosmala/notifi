<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * List notifications from subscribed organisations.
     *
     * GET /api/v1/notifications
     */
    public function index(Request $request): JsonResponse
    {
        $subscriber = $request->user();

        // Get IDs of active subscriptions
        $organisationIds = $subscriber->activeOrganisations()->pluck('organisations.id');

        $notifications = Notification::query()
            ->whereIn('organisation_id', $organisationIds)
            ->whereNotNull('sent_at')
            ->with('organisation:id,name')
            ->latest('sent_at')
            ->paginate(20);

        return response()->json([
            'notifications' => $notifications->through(fn ($notification) => [
                'id' => $notification->id,
                'title' => $notification->title,
                'body' => $notification->body,
                'link' => $notification->link,
                'sent_at' => $notification->sent_at,
                'organisation' => [
                    'id' => $notification->organisation->id,
                    'name' => $notification->organisation->name,
                ],
            ]),
        ]);
    }

    /**
     * Get a single notification.
     *
     * GET /api/v1/notifications/{notification}
     */
    public function show(Request $request, Notification $notification): JsonResponse
    {
        $subscriber = $request->user();

        // Check subscriber is subscribed to the notification's organisation
        $isSubscribed = $subscriber->activeOrganisations()
            ->where('organisations.id', $notification->organisation_id)
            ->exists();

        if (! $isSubscribed || ! $notification->sent_at) {
            abort(404);
        }

        return response()->json([
            'notification' => [
                'id' => $notification->id,
                'title' => $notification->title,
                'body' => $notification->body,
                'link' => $notification->link,
                'sent_at' => $notification->sent_at,
                'organisation' => [
                    'id' => $notification->organisation->id,
                    'name' => $notification->organisation->name,
                ],
            ],
        ]);
    }
}
