<?php

namespace App\Http\Controllers;

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

        return view('dashboard.index', [
            'organisation' => $organisation,
            'stats' => [
                'subscribers' => $organisation->subscribers()->count(),
                'notifications' => $organisation->notifications()->count(),
                'sent' => $organisation->notifications()->whereNotNull('sent_at')->count(),
                'scheduled' => $organisation->notifications()->whereNotNull('scheduled_for')->whereNull('sent_at')->count(),
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
