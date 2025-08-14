<?php

namespace App\Http\Controllers\Api\v1;

use App\Helpers\HttpStatus;
use App\Models\Designation;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\v1\DesignationService;
use App\Http\Requests\StoreDesignationRequest;
use App\Http\Requests\UpdateDesignationRequest;
use App\Http\Resources\DesignationResource;

class DesignationController extends Controller
{
    protected $service;

    public function __construct(DesignationService $service)
    {
        $this->service = $service;
    }

    public function index(): JsonResponse
    {
        $designations = $this->service->getAll(); // Fetch all designations without pagination

        return response()->json([
            'success' => true,
            'data' => DesignationResource::collection($designations),
        ], HttpStatus::OK);
    }

    public function store(StoreDesignationRequest $request): JsonResponse
    {
        $designation = $this->service->create($request->validated());
        return response()->json([
            'success' => true,
            'data' => new DesignationResource($designation),
        ], HttpStatus::CREATED);
    }

    public function update(UpdateDesignationRequest $request, Designation $designation): JsonResponse
    {
        $designation = $this->service->update($designation, $request->validated());
        return response()->json([
            'success' => true,
            'data' => new DesignationResource($designation),
        ], HttpStatus::OK);
    }

    public function destroy(Designation $designation): JsonResponse
    {
        $this->service->delete($designation);
        return response()->json([
            'success' => true,
            'message' => 'Designation deleted successfully.',
        ], HttpStatus::OK);
    }
}
