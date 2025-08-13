<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Services\v1\TrainingService;
use App\Http\Requests\TrainingRequest;
use App\Http\Resources\TrainingResource;
use App\Helpers\HttpStatus;
use Illuminate\Http\JsonResponse;

class TrainingController extends Controller
{
    protected $trainingService;

    public function __construct(TrainingService $trainingService)
    {
        $this->trainingService = $trainingService;
    }

    public function index(): JsonResponse
    {
        $trainings = $this->trainingService->getAllTrainings();
        return response()->json([
            'success' => true,
            'data' => TrainingResource::collection($trainings),
        ], HttpStatus::OK);
    }

    public function store(TrainingRequest $request): JsonResponse
    {
        $training = $this->trainingService->createTraining($request->validated());
        return response()->json([
            'success' => true,
            'data' => new TrainingResource($training),
        ], HttpStatus::CREATED);
    }

    public function show($id): JsonResponse
    {
        $training = $this->trainingService->getTrainingById($id);
        return response()->json([
            'success' => true,
            'data' => new TrainingResource($training),
        ], HttpStatus::OK);
    }

    public function update(TrainingRequest $request, $id): JsonResponse
    {
        $training = $this->trainingService->updateTraining($id, $request->validated());
        return response()->json([
            'success' => true,
            'data' => new TrainingResource($training),
        ], HttpStatus::OK);
    }

    public function destroy($id): JsonResponse
    {
        $this->trainingService->deleteTraining($id);
        return response()->json([
            'success' => true,
            'message' => 'Training deleted successfully.',
        ], HttpStatus::OK);
    }
}
