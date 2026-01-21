<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Organisation;
use App\Models\Subscriber;
use App\Models\User;
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
                'admins' => User::where('is_super_admin', false)->count(),
            ],
            'organisations' => Organisation::withCount(['subscribers', 'notifications'])
                ->latest()
                ->get(),
            'recentNotifications' => Notification::with('organisation')
                ->latest()
                ->take(10)
                ->get(),
        ]);
    }

    public function organisations(): View
    {
        $organisations = Organisation::withCount(['subscribers', 'notifications', 'administrators'])
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
}
