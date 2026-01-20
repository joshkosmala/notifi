<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Organisation;
use App\Models\Subscriber;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
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
}
