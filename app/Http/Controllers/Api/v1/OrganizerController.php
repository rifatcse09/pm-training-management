<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Services\v1\OrganizerService;
use App\Http\Resources\OrganizerResource;
use App\Http\Requests\OrganizerRequest;
use App\Helpers\HttpStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrganizerController extends Controller
{
    protected $organizerService;

    public function __construct(OrganizerService $organizerService)
    {
        $this->organizerService = $organizerService;
    }

    public function index(Request $request): JsonResponse
    {
        $search = $request->query('search', null);
        $organizers = $this->organizerService->getAllOrganizers(
            $request->query('page', 1),
            $request->query('per_page', 10),
            $search
        );

        return response()->json([
            'success' => true,
            'data' => OrganizerResource::collection($organizers), // Pass the paginated collection directly
            'meta' => [
                'current_page' => $organizers->currentPage(),
                'last_page' => $organizers->lastPage(),
                'per_page' => $organizers->perPage(),
                'total' => $organizers->total(),
            ],
        ], HttpStatus::OK);
    }

    public function store(OrganizerRequest $request): JsonResponse
    {
        $organizer = $this->organizerService->createOrganizer($request->validated());
        return response()->json([
            'success' => true,
            'data' => new OrganizerResource($organizer),
        ], HttpStatus::CREATED);
    }

    public function show($id): JsonResponse
    {
        try {
            $organizer = $this->organizerService->getOrganizerById($id);
            return response()->json([
                'success' => true,
                'data' => new OrganizerResource($organizer),
            ], HttpStatus::OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Organizer not found.',
            ], HttpStatus::NOT_FOUND);
        }
    }

    public function update(OrganizerRequest $request, $id): JsonResponse
    {
        try {
            $organizer = $this->organizerService->updateOrganizer($id, $request->validated());
            return response()->json([
                'success' => true,
                'data' => new OrganizerResource($organizer),
            ], HttpStatus::OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Organizer not found or could not be updated.',
            ], HttpStatus::NOT_FOUND);
        }
    }

    public function destroy($id): JsonResponse
    {
        $this->organizerService->deleteOrganizer($id);
        return response()->json([
            'success' => true,
            'message' => 'Organizer deleted successfully.',
        ], HttpStatus::OK);
    }

    public function getProjectOrganizers(): JsonResponse
    {
        $organizers = $this->organizerService->getProjectOrganizers();
        return response()->json([
            'success' => true,
            'data' => OrganizerResource::collection($organizers),
        ], HttpStatus::OK);
    }
}
