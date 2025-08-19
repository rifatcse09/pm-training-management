<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrainingRequest;
use App\Http\Requests\UpdateTrainingRequest;
use App\Http\Resources\TrainingResource;
use App\Services\v1\TrainingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class TrainingController extends Controller
{
    protected $trainingService;

    public function __construct(TrainingService $trainingService)
    {
        $this->trainingService = $trainingService;
    }

    public function index(Request $request): JsonResponse
    {
        $search = $request->query('search', null); // Get the search query
        $trainings = $this->trainingService->getAllTrainings(
            $request->query('page', 1),
            $request->query('per_page', 10),
            $search
        );

        return response()->json([
            'success' => true,
            'data' => TrainingResource::collection($trainings->items()),
            'meta' => [
                'current_page' => $trainings->currentPage(),
                'last_page' => $trainings->lastPage(),
                'per_page' => $trainings->perPage(),
                'total' => $trainings->total(),
            ],
        ]);
    }

    public function store(TrainingRequest $request): JsonResponse
    {
        $training = $this->trainingService->createTraining($request->validated(), $request->file('file_link'));

        return response()->json([
            'success' => true,
            'message' => 'Training created successfully.',
            'data' => $this->trainingService->formatTrainingResponse($training),
        ], 201);
    }

    public function show($id): JsonResponse
    {
        try {
            $training = $this->trainingService->getTrainingById($id);

            return response()->json([
                'success' => true,
                'data' => new TrainingResource($training),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Training not found.',
            ], 404);
        }
    }

    public function update(UpdateTrainingRequest $request, $id): JsonResponse
    {
        // Log raw request data for debugging
        Log::info("Raw request data for training ID: {$id}", ['request_data' => $request->all()]);

        // Log validated data for debugging
        Log::info("Validated data for training ID: {$id}", ['validated_data' => $request->validated()]);

        try {
            // Pass validated data and file to the service
            $training = $this->trainingService->updateTraining($id, $request->validated(), $request->file('file_link'));

            return response()->json([
                'success' => true,
                'message' => 'Training updated successfully.',
                'data' => $this->trainingService->formatTrainingResponse($training),
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error("Training not found with ID: {$id}");
            return response()->json([
                'success' => false,
                'message' => 'Training not found.',
            ], 404);
        } catch (\Exception $e) {
            Log::error("Error updating training with ID: {$id} - {$e->getMessage()}");
            return response()->json([
                'success' => false,
                'message' => 'Failed to update training.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        $this->trainingService->deleteTraining($id);

        return response()->json(['message' => 'Training deleted successfully.']);
    }
}
