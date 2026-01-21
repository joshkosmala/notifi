<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Organisation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * List subscriber's organisations.
     *
     * GET /api/v1/subscriptions
     */
    public function index(Request $request): JsonResponse
    {
        $subscriber = $request->user();

        $organisations = $subscriber->activeOrganisations()
            ->orderBy('name')
            ->get();

        return response()->json([
            'subscriptions' => $organisations->map(fn ($org) => [
                'id' => $org->id,
                'name' => $org->name,
                'url' => $org->url,
                'subscribed_at' => $org->pivot->created_at,
            ]),
        ]);
    }

    /**
     * Subscribe to an organisation by ID or code.
     *
     * POST /api/v1/subscriptions
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'organisation_id' => ['required_without:code', 'exists:organisations,id'],
            'code' => ['required_without:organisation_id', 'string', 'size:8'],
        ]);

        $subscriber = $request->user();

        // Find organisation by ID or code
        if ($request->has('code')) {
            $organisation = Organisation::where('subscribe_code', strtoupper($request->input('code')))->first();
            
            if (! $organisation) {
                return response()->json([
                    'message' => 'Invalid organisation code.',
                ], 404);
            }
        } else {
            $organisation = Organisation::findOrFail($request->input('organisation_id'));
        }

        // Check if organisation is verified
        if (! $organisation->verified_at) {
            return response()->json([
                'message' => 'Organisation is not available for subscriptions.',
            ], 422);
        }

        // Check if already subscribed
        $existing = $subscriber->organisations()
            ->where('organisation_id', $organisation->id)
            ->first();

        if ($existing) {
            // If previously unsubscribed, resubscribe
            if ($existing->pivot->unsubscribed_at) {
                $subscriber->organisations()->updateExistingPivot($organisation->id, [
                    'unsubscribed_at' => null,
                ]);

                return response()->json([
                    'message' => 'Resubscribed successfully.',
                ]);
            }

            return response()->json([
                'message' => 'Already subscribed to this organisation.',
            ], 422);
        }

        // Subscribe
        $subscriber->organisations()->attach($organisation->id);

        return response()->json([
            'message' => 'Subscribed successfully.',
        ], 201);
    }

    /**
     * Unsubscribe from an organisation.
     *
     * DELETE /api/v1/subscriptions/{organisation}
     */
    public function destroy(Request $request, Organisation $organisation): JsonResponse
    {
        $subscriber = $request->user();

        $existing = $subscriber->organisations()
            ->where('organisation_id', $organisation->id)
            ->whereNull('organisation_subscriber.unsubscribed_at')
            ->first();

        if (! $existing) {
            return response()->json([
                'message' => 'Not subscribed to this organisation.',
            ], 404);
        }

        // Soft unsubscribe
        $subscriber->organisations()->updateExistingPivot($organisation->id, [
            'unsubscribed_at' => now(),
        ]);

        return response()->json([
            'message' => 'Unsubscribed successfully.',
        ]);
    }
}
