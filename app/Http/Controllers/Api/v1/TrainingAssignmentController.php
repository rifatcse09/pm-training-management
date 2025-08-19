<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\Employee;
use App\Models\Training;
use App\Helpers\HttpStatus;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\AssignTrainingRequest;
use App\Services\v1\TrainingAssignmentService;
use Illuminate\Validation\ValidationException;
use App\Http\Resources\TrainingAssignmentResource;

class TrainingAssignmentController extends Controller
{
    protected $trainingAssignmentService;

    public function __construct(TrainingAssignmentService $trainingAssignmentService)
    {
        $this->trainingAssignmentService = $trainingAssignmentService;
    }

    public function assign(AssignTrainingRequest $request): JsonResponse
    {
        try {
            $training = Training::findOrFail($request->training_id); // Fetch training by ID
            $this->trainingAssignmentService->assignMultiple($training, $request->employee_ids);
            return response()->json(['message' => 'Training assigned successfully.'], 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function index(Request $request): JsonResponse
    {
        Log::info('Index method called', [
            'page' => $request->query('page'),
            'per_page' => $request->query('per_page'),
            'search' => $request->query('search'),
        ]);

        $page = $request->query('page', 1);
        $perPage = $request->query('per_page', 10);
        $search = $request->query('search', null);

        $assignments = $this->trainingAssignmentService->getAllAssignments($page, $perPage, $search);

        if ($assignments->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No training assignments found.',
                'data' => [],
            ], 200);
        }

        return response()->json([
            'success' => true,
            'message' => 'Training assignments retrieved successfully.',
            'data' => TrainingAssignmentResource::collection($assignments->items()),
            'meta' => [
                'current_page' => $assignments->currentPage(),
                'last_page' => $assignments->lastPage(),
                'total' => $assignments->total(),
                'per_page' => $assignments->perPage(),
            ],
        ], 200);
    }

    public function getEmployeeTrainings($id)
    {
        $employee = Employee::findOrFail($id);
        $trainings = $employee->trainings()->with('organizer')->get(); // Assuming a relationship exists
        return response()->json(['data' => $trainings], 200);
    }
}
