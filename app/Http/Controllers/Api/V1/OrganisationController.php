<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Organisation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrganisationController extends Controller
{
    /**
     * Search/list verified organisations.
     *
     * GET /api/v1/organisations
     */
    public function index(Request $request): JsonResponse
    {
        $query = Organisation::query()
            ->whereNotNull('verified_at');

        // Search by name
        if ($search = $request->input('search')) {
            $query->where('name', 'ilike', "%{$search}%");
        }

        $organisations = $query
            ->orderBy('name')
            ->paginate(20);

        return response()->json([
            'organisations' => $organisations->through(fn ($org) => [
                'id' => $org->id,
                'name' => $org->name,
                'url' => $org->url,
            ]),
        ]);
    }

    /**
     * Get a single organisation.
     *
     * GET /api/v1/organisations/{organisation}
     */
    public function show(Organisation $organisation): JsonResponse
    {
        if (! $organisation->verified_at) {
            abort(404);
        }

        return response()->json([
            'organisation' => [
                'id' => $organisation->id,
                'name' => $organisation->name,
                'url' => $organisation->url,
                'address' => $organisation->address,
            ],
        ]);
    }
}
