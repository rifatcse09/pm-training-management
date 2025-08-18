<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\Employee;
use App\Models\Training;
use App\Helpers\HttpStatus;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\v1\OrganizerService;
use App\Http\Requests\OrganizerRequest;
use App\Http\Resources\OrganizerResource;
use App\Http\Requests\AssignTrainingRequest;
use App\Services\v1\TrainingAssignmentService;
use Illuminate\Validation\ValidationException;

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

    public function index(Request $request)
    {
        // filters: status, due_from, due_to, search
        $query = DB::table('employee_training')
            ->join('employees','employees.id','=','employee_training.employee_id')
            ->join('trainings','trainings.id','=','employee_training.training_id')
            ->select('employee_training.*','employees.name as employee_name','trainings.title as training_title');

        if ($status = $request->get('status')) $query->where('employee_training.status', $status);
        if ($df = $request->get('due_from')) $query->whereDate('employee_training.due_date', '>=', $df);
        if ($dt = $request->get('due_to')) $query->whereDate('employee_training.due_date', '<=', $dt);
        if ($s = $request->get('search')) {
            $query->where(function($q) use ($s) {
                $q->where('employees.name','like',"%$s%")
                  ->orWhere('trainings.title','like',"%$s%");
            });
        }

        return $query->orderByDesc('employee_training.id')->paginate(20);
    }
}
