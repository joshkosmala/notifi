<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNotificationRequest;
use App\Http\Requests\UpdateNotificationRequest;
use App\Mail\NotificationEmail;
use App\Models\Notification;
use App\Models\Organisation;
use App\Services\SocialPostingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function __construct(protected SocialPostingService $socialPostingService) {}

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

        // If scheduled for the future, don't send now
        if ($notification->scheduled_for && $notification->scheduled_for->isFuture()) {
            return redirect()->route('notifications.index')
                ->with('success', 'Notification scheduled for '.$notification->formatInTimezone($notification->scheduled_for).'.');
        }

        if ($request->has('send_now')) {
            $notification->markAsSent();

            // Send emails if requested
            $emailCount = 0;
            if ($request->boolean('send_email')) {
                $emailCount = $this->sendEmailNotifications($notification, $organisation);
            }

            // Post to social media if requested
            $socialResults = [];
            $platforms = [];

            if ($request->boolean('post_to_facebook')) {
                $platforms[] = 'facebook';
            }
            if ($request->boolean('post_to_x')) {
                $platforms[] = 'x';
            }

            if (! empty($platforms)) {
                $socialResults = $this->socialPostingService->postToAll($notification, $platforms);
            }

            // Build success message
            $message = 'Notification sent successfully!';

            if ($emailCount > 0) {
                $message .= " Emailed to {$emailCount} subscriber".($emailCount === 1 ? '' : 's').'.';
            }

            if (! empty($socialResults)) {
                $posted = [];
                $failed = [];
                foreach ($socialResults as $platform => $success) {
                    if ($success) {
                        $posted[] = $platform === 'x' ? 'X' : 'Facebook';
                    } else {
                        $failed[] = $platform === 'x' ? 'X' : 'Facebook';
                    }
                }
                if (! empty($posted)) {
                    $message .= ' Posted to: '.implode(', ', $posted).'.';
                }
                if (! empty($failed)) {
                    $message .= ' Failed to post to: '.implode(', ', $failed).'.';
                }
            }

            return redirect()->route('notifications.index')
                ->with('success', $message);
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

            // Post to social media if requested
            $socialResults = [];
            $platforms = [];

            if ($request->boolean('post_to_facebook')) {
                $platforms[] = 'facebook';
            }
            if ($request->boolean('post_to_x')) {
                $platforms[] = 'x';
            }

            if (! empty($platforms)) {
                $socialResults = $this->socialPostingService->postToAll($notification, $platforms);
            }

            // Build success message with social posting results
            $message = 'Notification updated and sent!';
            if (! empty($socialResults)) {
                $posted = [];
                $failed = [];
                foreach ($socialResults as $platform => $success) {
                    if ($success) {
                        $posted[] = $platform === 'x' ? 'X' : 'Facebook';
                    } else {
                        $failed[] = $platform === 'x' ? 'X' : 'Facebook';
                    }
                }
                if (! empty($posted)) {
                    $message .= ' Posted to: '.implode(', ', $posted).'.';
                }
                if (! empty($failed)) {
                    $message .= ' Failed to post to: '.implode(', ', $failed).'.';
                }
            }

            return redirect()->route('notifications.index')
                ->with('success', $message);
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

    /**
     * Send email notifications to subscribers with email addresses.
     */
    protected function sendEmailNotifications(Notification $notification, Organisation $organisation): int
    {
        $subscribers = $organisation->subscribers()
            ->whereNull('organisation_subscriber.unsubscribed_at')
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->get();

        foreach ($subscribers as $subscriber) {
            Mail::to($subscriber->email)->queue(new NotificationEmail($notification, $organisation));
        }

        return $subscribers->count();
    }
}
