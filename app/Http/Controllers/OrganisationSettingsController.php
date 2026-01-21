<?php

namespace App\Http\Controllers;

use App\Models\Organisation;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrganisationSettingsController extends Controller
{
    /**
     * Get the current user's organisation.
     */
    protected function getOrganisation(): Organisation
    {
        return auth()->user()->organisations()->firstOrFail();
    }

    /**
     * Display organisation settings.
     */
    public function index(): View
    {
        $organisation = $this->getOrganisation();

        return view('settings.index', compact('organisation'));
    }

    /**
     * Disconnect Facebook Page.
     */
    public function disconnectFacebook(): RedirectResponse
    {
        $organisation = $this->getOrganisation();
        $organisation->disconnectFacebook();

        return redirect()->route('settings.index')
            ->with('success', 'Facebook Page disconnected.');
    }

    /**
     * Disconnect X account.
     */
    public function disconnectX(): RedirectResponse
    {
        $organisation = $this->getOrganisation();
        $organisation->disconnectX();

        return redirect()->route('settings.index')
            ->with('success', 'X account disconnected.');
    }
}
