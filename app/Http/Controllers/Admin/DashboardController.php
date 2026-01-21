<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationEvent;
use App\Models\Organisation;
use App\Models\Subscriber;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'organisations' => Organisation::count(),
                'subscribers' => Subscriber::count(),
                'notifications' => Notification::count(),
                'sent' => Notification::whereNotNull('sent_at')->count(),
                'opens' => NotificationEvent::where('event_type', 'open')->count(),
                'link_clicks' => NotificationEvent::where('event_type', 'link_click')->count(),
            ],
            'organisations' => Organisation::withCount(['subscribers', 'notifications'])
                ->with(['notifications' => fn ($q) => $q->whereNotNull('sent_at')->with('events')])
                ->latest()
                ->get(),
            'recentNotifications' => Notification::with(['organisation', 'events'])
                ->whereNotNull('sent_at')
                ->latest()
                ->take(10)
                ->get(),
        ]);
    }

    public function organisations(): View
    {
        $organisations = Organisation::withCount(['subscribers', 'notifications', 'administrators'])
            ->with(['notifications' => fn ($q) => $q->whereNotNull('sent_at')->with('events')])
            ->latest()
            ->paginate(25);

        return view('admin.organisations.index', compact('organisations'));
    }

    public function showOrganisation(Organisation $organisation): View
    {
        $organisation->loadCount(['subscribers', 'notifications', 'administrators']);
        $organisation->load(['administrators', 'notifications' => fn ($q) => $q->latest()->take(10)]);

        return view('admin.organisations.show', compact('organisation'));
    }

    public function showNotification(Notification $notification): View
    {
        $notification->load('organisation');

        return view('admin.notifications.show', compact('notification'));
    }

    public function verifyOrganisation(Organisation $organisation)
    {
        $organisation->update(['verified_at' => now()]);

        return back()->with('success', 'Organisation has been verified.');
    }

    public function unverifyOrganisation(Organisation $organisation)
    {
        $organisation->update(['verified_at' => null]);

        return back()->with('success', 'Organisation verification has been removed.');
    }
}
