<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNotificationRequest;
use App\Http\Requests\UpdateNotificationRequest;
use App\Models\Notification;
use App\Models\Organisation;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Get the current user's organisation.
     */
    protected function getOrganisation(): Organisation
    {
        return auth()->user()->organisations()->firstOrFail();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $organisation = $this->getOrganisation();
        $notifications = $organisation->notifications()
            ->latest()
            ->paginate(15);

        return view('notifications.index', compact('organisation', 'notifications'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $organisation = $this->getOrganisation();

        return view('notifications.create', compact('organisation'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNotificationRequest $request): RedirectResponse
    {
        $organisation = $this->getOrganisation();

        $notification = $organisation->notifications()->create($request->validated());

        if ($request->has('send_now')) {
            $notification->markAsSent();

            return redirect()->route('notifications.index')
                ->with('success', 'Notification sent successfully!');
        }

        return redirect()->route('notifications.index')
            ->with('success', 'Notification saved as draft.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Notification $notification): View
    {
        $this->authorizeNotification($notification);

        return view('notifications.show', compact('notification'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notification $notification): View
    {
        $this->authorizeNotification($notification);
        $organisation = $this->getOrganisation();

        return view('notifications.edit', compact('organisation', 'notification'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNotificationRequest $request, Notification $notification): RedirectResponse
    {
        $this->authorizeNotification($notification);

        $notification->update($request->validated());

        if ($request->has('send_now') && ! $notification->isSent()) {
            $notification->markAsSent();

            return redirect()->route('notifications.index')
                ->with('success', 'Notification updated and sent!');
        }

        return redirect()->route('notifications.index')
            ->with('success', 'Notification updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notification $notification): RedirectResponse
    {
        $this->authorizeNotification($notification);

        $notification->delete();

        return redirect()->route('notifications.index')
            ->with('success', 'Notification deleted.');
    }

    /**
     * Ensure the notification belongs to the user's organisation.
     */
    protected function authorizeNotification(Notification $notification): void
    {
        $organisation = $this->getOrganisation();

        abort_unless($notification->organisation_id === $organisation->id, 403);
    }
}
