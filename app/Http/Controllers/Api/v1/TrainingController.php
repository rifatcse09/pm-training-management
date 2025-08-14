<?php

namespace App\Http\Controllers\Api\v1;

use App\Helpers\HttpStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\TrainingRequest;
use App\Http\Requests\UpdateTrainingRequest;
use App\Http\Resources\TrainingResource;
use App\Services\v1\TrainingService;
use Illuminate\Http\JsonResponse;
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
        $search = $request->query('search', null);
        $trainings = $this->trainingService->getAllTrainings(
            $request->query('page', 1),
            $request->query('per_page', 10),
            $search
        );

        return response()->json([
            'success' => true,
            'data' => TrainingResource::collection($trainings),
            'meta' => [
                'current_page' => $trainings->currentPage(),
                'last_page' => $trainings->lastPage(),
                'per_page' => $trainings->perPage(),
                'total' => $trainings->total(),
            ],
        ], HttpStatus::OK);
    }

    public function store(TrainingRequest $request): JsonResponse
    {
        $data = $request->all();

        if ($request->hasFile('file_link')) {
            $data['file_link'] = $request->file('file_link');
        }

        $training = $this->trainingService->createTraining($data);

        return response()->json([
            'success' => true,
            'data' => new TrainingResource($training),
        ], HttpStatus::CREATED);
    }

    public function show($id): JsonResponse
    {
        try {
            $training = $this->trainingService->getTrainingById($id);
            return response()->json([
                'success' => true,
                'data' => new TrainingResource($training),
            ], HttpStatus::OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Training not found.',
            ], HttpStatus::NOT_FOUND);
        }
    }

    public function update(UpdateTrainingRequest $request, $id): JsonResponse
    {
        $data = $request->all();

        if ($request->hasFile('file_link')) {
            $data['file_link'] = $request->file('file_link');
        }

        try {
            $training = $this->trainingService->updateTraining($id, $data);

            return response()->json([
                'success' => true,
                'data' => new TrainingResource($training),
            ], HttpStatus::OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Training not found or could not be updated.',
            ], HttpStatus::NOT_FOUND);
        }
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
