<?php

namespace App\Http\Controllers;

use App\Models\Organisation;
use Illuminate\Http\Request;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SubscribeController extends Controller
{
    /**
     * Show the public subscribe page for an organisation.
     * This page detects mobile and offers app deep link or download.
     */
    public function show(string $code): View
    {
        $organisation = Organisation::where('subscribe_code', $code)
            ->whereNotNull('verified_at')
            ->firstOrFail();

        return view('subscribe.show', [
            'organisation' => $organisation,
            'deepLink' => $organisation->getDeepLinkUrl(),
        ]);
    }

    /**
     * Generate QR code image for an organisation's subscribe link.
     */
    public function qrCode(string $code)
    {
        $organisation = Organisation::where('subscribe_code', $code)
            ->whereNotNull('verified_at')
            ->firstOrFail();

        $qrCode = QrCode::format('svg')
            ->size(300)
            ->margin(2)
            ->generate($organisation->getSubscribeUrl());

        return response($qrCode)->header('Content-Type', 'image/svg+xml');
    }
}
