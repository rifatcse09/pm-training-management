<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Services\v1\OrganizerService;
use App\Http\Resources\OrganizerResource;
use Illuminate\Http\Request;
use App\Helpers\HttpStatus;
use Illuminate\Http\JsonResponse;

class OrganizerController extends Controller
{
    protected $organizerService;

    public function __construct(OrganizerService $organizerService)
    {
        $this->organizerService = $organizerService;
    }

    public function index(): JsonResponse
    {
        $organizers = $this->organizerService->getAllOrganizers();
        return response()->json([
            'success' => true,
            'data' => OrganizerResource::collection($organizers),
        ], HttpStatus::OK);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_project' => 'required|boolean',
        ]);

        $organizer = $this->organizerService->createOrganizer($validated);
        return response()->json([
            'success' => true,
            'data' => new OrganizerResource($organizer),
        ], HttpStatus::CREATED);
    }

    public function show($id): JsonResponse
    {
        $organizer = $this->organizerService->getOrganizerById($id);
        return response()->json([
            'success' => true,
            'data' => new OrganizerResource($organizer),
        ], HttpStatus::OK);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_project' => 'required|boolean',
        ]);

        $organizer = $this->organizerService->updateOrganizer($id, $validated);
        return response()->json([
            'success' => true,
            'data' => new OrganizerResource($organizer),
        ], HttpStatus::OK);
    }

    public function destroy($id): JsonResponse
    {
        $this->organizerService->deleteOrganizer($id);
        return response()->json([
            'success' => true,
            'message' => 'Organizer deleted successfully.',
        ], HttpStatus::OK);
    }
}
