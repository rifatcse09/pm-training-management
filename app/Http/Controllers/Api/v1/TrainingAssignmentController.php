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
use App\Http\Requests\UpdateTrainingAssignmentRequest;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;

class TrainingAssignmentController extends Controller
{
    protected $trainingAssignmentService;

    public function __construct(TrainingAssignmentService $trainingAssignmentService)
    {
        $this->trainingAssignmentService = $trainingAssignmentService;
    }

    public function assign(AssignTrainingRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            // Retrieve the Training model instance using the training_id
            $training = Training::findOrFail($data['training_id']);

            // Prepare group training data
            $groupTrainingData = [
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'total_days' => $data['total_days'],
                'file_link' => $data['file_link'] ?? null,
            ];

            // Pass the Training model instance and group training data to the service
            $this->trainingAssignmentService->assignMultiple(
                $training,
                $data['employee_ids'],
                $groupTrainingData
            );

            return response()->json([
                'success' => true,
                'message' => 'Employees assigned to training successfully.',
            ]);
        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error('Failed to assign employees to training', [
                'training_id' => $data['training_id'],
                'employee_ids' => $data['employee_ids'],
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to assign employees to training.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function index(Request $request): JsonResponse
    {
        Log::info('Index method called', [
            'page' => $request->query('page'),
            'per_page' => $request->query('per_page'),
            'search' => $request->query('search'),
            'sort_field' => $request->query('sort_field'),
            'sort_order' => $request->query('sort_order'),
        ]);

        $page = $request->query('page', 1);
        $perPage = $request->query('per_page', 10);
        $search = $request->query('search', null);
        $sortField = $request->query('sort_field', null);
        $sortOrder = $request->query('sort_order', 'asc');

        $assignments = $this->trainingAssignmentService->getAllAssignments($page, $perPage, $search, $sortField, $sortOrder);

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

    public function editAssignment(UpdateTrainingAssignmentRequest $request, $assignmentId): JsonResponse
    {
        $data = $request->validated();

        try {
            // Retrieve the Training model instance using the training_id
            $training = Training::findOrFail($data['training_id']);

            // Pass the Training model instance, assignment ID, and employee ID to the service
            $this->trainingAssignmentService->editAssignmentForSingleEmployee(
                $assignmentId,
                $training,
                $data['employee_id']
            );

            return response()->json([
                'success' => true,
                'message' => 'Training assignment updated successfully for the employee.',
            ]);
        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error('Failed to edit training assignment for the employee', [
                'assignment_id' => $assignmentId,
                'training_id' => $data['training_id'],
                'employee_id' => $data['employee_id'],
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to edit training assignment for the employee.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function generateAssignmentsPdf(Request $request)
    {
        $filters = $request->only(['search', 'employee_id', 'training_id', 'working_place', 'designation_id', 'start_date', 'end_date', 'orderBy', 'orderDirection']);

        try {
            $assignmentsData = $this->trainingAssignmentService->getAssignmentsForPdf($filters);

            // Check if we have data
            if ($assignmentsData->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data found for the specified criteria.',
                ], 404);
            }

            $pdf = PDF::loadView('pdf.reports.training-assignments', [
                'assignmentsData' => $assignmentsData,
                'filters' => $filters,
                'generatedAt' => now(),
            ], [], ['mode' => 'utf-8', 'format' => 'A4-L']);

            $filename = 'training-assignments-report-' . now()->format('Y-m-d_H-i-s') . '.pdf';

            return response($pdf->output(), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', "attachment; filename=\"$filename\"")
                ->header('Access-Control-Expose-Headers', 'Content-Disposition');

        } catch (\Exception $e) {
            Log::error('Failed to generate assignments PDF report', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'filters' => $filters
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate PDF report. Please try again later.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }
}