<?php

namespace App\Http\Controllers;

use App\Models\Organisation;
use App\Models\Subscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SubscriberController extends Controller
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
        $subscribers = $organisation->subscribers()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total' => $organisation->subscribers()->count(),
            'verified' => $organisation->subscribers()->whereNotNull('phone_verified_at')->count(),
            'unsubscribed' => $organisation->subscribers()->whereNotNull('organisation_subscriber.unsubscribed_at')->count(),
        ];

        return view('subscribers.index', compact('organisation', 'subscribers', 'stats'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Subscriber $subscriber): View
    {
        $this->authorizeSubscriber($subscriber);
        $organisation = $this->getOrganisation();

        return view('subscribers.show', compact('organisation', 'subscriber'));
    }

    /**
     * Remove the subscriber from the organisation (unsubscribe).
     */
    public function destroy(Subscriber $subscriber): RedirectResponse
    {
        $this->authorizeSubscriber($subscriber);
        $organisation = $this->getOrganisation();

        // Mark as unsubscribed rather than deleting
        $organisation->subscribers()->updateExistingPivot($subscriber->id, [
            'unsubscribed_at' => now(),
        ]);

        return redirect()->route('subscribers.index')
            ->with('success', 'Subscriber has been unsubscribed.');
    }

    /**
     * Resubscribe a previously unsubscribed subscriber.
     */
    public function resubscribe(Subscriber $subscriber): RedirectResponse
    {
        $this->authorizeSubscriber($subscriber);
        $organisation = $this->getOrganisation();

        $organisation->subscribers()->updateExistingPivot($subscriber->id, [
            'unsubscribed_at' => null,
        ]);

        return redirect()->route('subscribers.show', $subscriber)
            ->with('success', 'Subscriber has been resubscribed.');
    }

    /**
     * Ensure the subscriber belongs to the user's organisation.
     */
    protected function authorizeSubscriber(Subscriber $subscriber): void
    {
        $organisation = $this->getOrganisation();

        if (! $organisation->subscribers()->where('subscribers.id', $subscriber->id)->exists()) {
            abort(403, 'This subscriber does not belong to your organisation.');
        }
    }
}
