<?php

namespace App\Http\Controllers;

use App\Models\NotificationEvent;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $organisation = $user->organisations()->first();

        if (! $organisation) {
            return view('dashboard.no-organisation');
        }

        // Get notification IDs for this organisation
        $notificationIds = $organisation->notifications()->pluck('id');

        // Calculate aggregate analytics
        $totalOpens = NotificationEvent::whereIn('notification_id', $notificationIds)
            ->where('event_type', 'open')
            ->count();
        $totalClicks = NotificationEvent::whereIn('notification_id', $notificationIds)
            ->where('event_type', 'link_click')
            ->count();

        return view('dashboard.index', [
            'organisation' => $organisation,
            'stats' => [
                'subscribers' => $organisation->subscribers()->count(),
                'notifications' => $organisation->notifications()->count(),
                'sent' => $organisation->notifications()->whereNotNull('sent_at')->count(),
                'scheduled' => $organisation->notifications()->whereNotNull('scheduled_for')->whereNull('sent_at')->count(),
            ],
            'analytics' => [
                'total_opens' => $totalOpens,
                'total_clicks' => $totalClicks,
            ],
            'recentNotifications' => $organisation->notifications()
                ->latest()
                ->take(5)
                ->get(),
            'recentSubscribers' => $organisation->subscribers()
                ->latest()
                ->take(5)
                ->get(),
        ]);
    }
}
