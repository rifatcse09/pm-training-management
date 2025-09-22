<?php

namespace App\Services\v1;

use App\Models\Employee;
use App\Models\Training;
use App\Models\GroupTraining;
use App\Enums\WorkingPlaceEnum;
use App\Models\EmployeeTraining;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class TrainingAssignmentService
{
    protected $groupTrainingService;

    public function __construct(GroupTrainingService $groupTrainingService)
    {
        $this->groupTrainingService = $groupTrainingService;
    }

    public function assign(Employee $employee, Training $training, array $data = []): void
    {
        try {
            DB::transaction(function () use ($employee, $training, $data) {
                $exists = $employee->trainings()->where('training_id', $training->id)->exists();
                if ($exists) {
                    Log::warning('Employee already assigned to training', [
                        'employee_id' => $employee->id,
                        'training_id' => $training->id,
                    ]);
                    throw ValidationException::withMessages(['assignment' => 'Already assigned.']);
                }

                // Log the data being inserted
                Log::info('Attaching employee to training', [
                    'employee_id' => $employee->id,
                    'training_id' => $training->id,
                    'data' => $data,
                ]);

                $employee->trainings()->attach($training->id, [
                    'assigned_at' => $data['assigned_at'] ?? now(),
                    'assigned_by' => $data['assigned_by'] ?? auth()->id(),
                    'working_place' => $data['working_place'] ?? null,
                    'designation_id' => $data['designation_id'] ?? null,
                ]);
            });

            // Log success message
            Log::info('Employee assigned successfully', [
                'employee_id' => $employee->id,
                'training_id' => $training->id,
            ]);
        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error('Failed to assign employee to training', [
                'employee_id' => $employee->id,
                'training_id' => $training->id,
                'error' => $e->getMessage(),
            ]);

            // Optionally rethrow the exception to handle it at a higher level
            throw $e;
        }
    }

    /**
     * Assign multiple employees to a training with a group training ID.
     *
     * @param Training $training
     * @param array $employeeIds
     * @param array $groupTrainingData
     * @return void
     */
    public function assignMultiple(Training $training, array $employeeIds, array $groupTrainingData): void
    {
        try {
            DB::transaction(function () use ($training, $employeeIds, $groupTrainingData) {
                // Create the group training and retrieve its ID
                $groupTrainingId = $this->groupTrainingService->createGroupTraining($groupTrainingData);

                foreach ($employeeIds as $employeeId) {
                    $employee = Employee::findOrFail($employeeId);

                    // Log the employee details
                    Log::info('Processing employee assignment', [
                        'assigned_at' => now(),
                        'assigned_by' => auth()->id(),
                        'employee_id' => $employee->id,
                        'training_id' => $training->id,
                        'group_training_id' => $groupTrainingId, // Log the group_training_id
                        'working_place' => $employee->working_place,
                        'designation_id' => $employee->designation_id,
                    ]);

                    // Assign the employee to the training with the group_training_id
                    EmployeeTraining::create([
                        'employee_id' => $employee->id,
                        'training_id' => $training->id,
                        'group_training_id' => $groupTrainingId, // Assign group_training_id
                        'assigned_at' => now(),
                        'assigned_by' => auth()->id(),
                        'working_place' => $employee->working_place,
                        'designation_id' => $employee->designation_id,
                    ]);
                }

                // Log success message
                Log::info('All employees assigned successfully', [
                    'training_id' => $training->id,
                    'group_training_id' => $groupTrainingId,
                    'employee_ids' => $employeeIds,
                ]);
            });
        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error('Failed to assign employees to training', [
                'training_id' => $training->id,
                'employee_ids' => $employeeIds,
                'error' => $e->getMessage(),
            ]);

            // Optionally rethrow the exception to handle it at a higher level
            throw $e;
        }
    }

    public function getAllAssignments($page = 1, $perPage = 10, $search = null, $orderBy = null, $orderDirection = 'asc')
    {
        $query = EmployeeTraining::with([
            'employee.designation',
            'training.organizer',
            'groupTraining'
        ]);

        // Apply search filter if provided
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('training', function ($subQ) use ($search) {
                    $subQ->where('name', 'LIKE', "%{$search}%");
                })->orWhereHas('employee', function ($subQ) use ($search) {
                    $subQ->where('name', 'LIKE', "%{$search}%");
                })->orWhereHas('employee.designation', function ($subQ) use ($search) {
                    $subQ->where('name', 'LIKE', "%{$search}%");
                })->orWhereHas('training.organizer', function ($subQ) use ($search) {
                    $subQ->where('name', 'LIKE', "%{$search}%");
                });
            });
        }

        // Apply sorting if provided
        if ($orderBy && $orderDirection) {
            $tableName = (new EmployeeTraining())->getTable(); // Get actual table name

            switch ($orderBy) {
                case 'training_name':
                    $query->leftJoin('trainings', $tableName . '.training_id', '=', 'trainings.id')
                          ->orderBy('trainings.name', $orderDirection)
                          ->select($tableName . '.*');
                    break;
                case 'employee_name':
                    $query->leftJoin('employees', $tableName . '.employee_id', '=', 'employees.id')
                          ->orderBy('employees.name', $orderDirection)
                          ->select($tableName . '.*');
                    break;
                case 'designation_name':
                    $query->leftJoin('employees', $tableName . '.employee_id', '=', 'employees.id')
                          ->leftJoin('designations', 'employees.designation_id', '=', 'designations.id')
                          ->orderBy('designations.name', $orderDirection)
                          ->select($tableName . '.*');
                    break;
                case 'organizer_name':
                    $query->leftJoin('trainings', $tableName . '.training_id', '=', 'trainings.id')
                          ->leftJoin('organizers', 'trainings.organization_id', '=', 'organizers.id')
                          ->orderBy('organizers.name', $orderDirection)
                          ->select($tableName . '.*');
                    break;
                case 'working_place':
                    $query->leftJoin('employees', $tableName . '.employee_id', '=', 'employees.id')
                          ->orderBy('employees.working_place', $orderDirection)
                          ->select($tableName . '.*');
                    break;
                case 'start_date':
                    $query->leftJoin('group_trainings', $tableName . '.group_training_id', '=', 'group_trainings.id')
                          ->orderBy('group_trainings.start_date', $orderDirection)
                          ->select($tableName . '.*');
                    break;
                case 'end_date':
                    $query->leftJoin('group_trainings', $tableName . '.group_training_id', '=', 'group_trainings.id')
                          ->orderBy('group_trainings.end_date', $orderDirection)
                          ->select($tableName . '.*');
                    break;
                case 'total_days':
                    $query->leftJoin('group_trainings', $tableName . '.group_training_id', '=', 'group_trainings.id')
                          ->orderBy('group_trainings.total_days', $orderDirection)
                          ->select($tableName . '.*');
                    break;
                default:
                    $query->orderBy($tableName . '.created_at', 'desc');
            }
        } else {
            $query->orderBy((new EmployeeTraining())->getTable() . '.created_at', 'desc');
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function editAssignmentForSingleEmployee($assignmentId, Training $training, $employeeId)
    {
        try {
            // Begin a database transaction
            DB::beginTransaction();

            // Find the assignment by ID
            $assignment = $training->assignments()->findOrFail($assignmentId);

            // Update the employee_id for the assignment
            $assignment->update([
                'employee_id' => $employeeId,
            ]);

            // Commit the transaction
            DB::commit();
        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollBack();
            Log::error('Failed to edit training assignment for single employee', [
                'assignment_id' => $assignmentId,
                'training_id' => $training->id,
                'employee_id' => $employeeId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Assign employees to a training with additional fields and file upload.
     *
     * @param array $data
     * @return void
     */
    public function assignTraining(array $data): void
    {
        DB::transaction(function () use ($data) {
            // Handle file upload if file_link is provided
            if (isset($data['file_link']) && $data['file_link'] instanceof \Illuminate\Http\UploadedFile) {
                $filePath = $this->uploadFile($data['file_link']);
                $data['file_name'] = basename($filePath);
                $data['file_link'] = asset('storage/' . $filePath);
            }

            // Create a group training record
            $groupTraining = GroupTraining::create([
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'total_days' => $data['total_days'],
                'file_link' => $data['file_link'] ?? null,
                'file_name' => $data['file_name'] ?? null,
            ]);

            // Assign employees to the training
            foreach ($data['employee_ids'] as $employeeId) {
                EmployeeTraining::create([
                    'employee_id' => $employeeId,
                    'training_id' => $data['training_id'],
                    'group_training_id' => $groupTraining->id,
                    'assigned_at' => now(),
                    'assigned_by' => auth()->id(),
                ]);
            }

            Log::info('Training assigned successfully', [
                'training_id' => $data['training_id'],
                'group_training_id' => $groupTraining->id,
                'employee_ids' => $data['employee_ids'],
            ]);
        });
    }

    /**
     * Upload a file to storage.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return string The file path
     */
    private function uploadFile($file): string
    {
        return $file->store('group_training_files', 'public');
    }

    public function getAssignmentsForPdf(array $filters)
    {
        $query = EmployeeTraining::with([
            'employee.designation',
            'training.organizer',
            'groupTraining'
        ]);

        // Apply search filter if provided (same as getAllAssignments)
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->whereHas('training', function ($subQ) use ($search) {
                    $subQ->where('name', 'LIKE', "%{$search}%");
                })->orWhereHas('employee', function ($subQ) use ($search) {
                    $subQ->where('name', 'LIKE', "%{$search}%");
                })->orWhereHas('employee.designation', function ($subQ) use ($search) {
                    $subQ->where('name', 'LIKE', "%{$search}%");
                })->orWhereHas('training.organizer', function ($subQ) use ($search) {
                    $subQ->where('name', 'LIKE', "%{$search}%");
                });
            });
        }

        // Apply additional filters for PDF
        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (!empty($filters['training_id'])) {
            $query->where('training_id', $filters['training_id']);
        }

        if (!empty($filters['working_place'])) {
            $query->whereHas('employee', function ($subQ) use ($filters) {
                $subQ->where('working_place', $filters['working_place']);
            });
        }

        if (!empty($filters['designation_id'])) {
            $query->whereHas('employee', function ($subQ) use ($filters) {
                $subQ->where('designation_id', $filters['designation_id']);
            });
        }

        if (!empty($filters['start_date'])) {
            $query->whereHas('groupTraining', function ($subQ) use ($filters) {
                $subQ->where('start_date', '>=', $filters['start_date']);
            });
        }

        if (!empty($filters['end_date'])) {
            $query->whereHas('groupTraining', function ($subQ) use ($filters) {
                $subQ->where('end_date', '<=', $filters['end_date']);
            });
        }

        // Apply sorting (same logic as getAllAssignments)
        $orderBy = $filters['orderBy'] ?? 'created_at';
        $orderDirection = $filters['orderDirection'] ?? 'desc';

        $tableName = (new EmployeeTraining())->getTable();

        switch ($orderBy) {
            case 'training_name':
                $query->leftJoin('trainings', $tableName . '.training_id', '=', 'trainings.id')
                      ->orderBy('trainings.name', $orderDirection)
                      ->select($tableName . '.*');
                break;
            case 'employee_name':
                $query->leftJoin('employees', $tableName . '.employee_id', '=', 'employees.id')
                      ->orderBy('employees.name', $orderDirection)
                      ->select($tableName . '.*');
                break;
            case 'designation_name':
                $query->leftJoin('employees', $tableName . '.employee_id', '=', 'employees.id')
                      ->leftJoin('designations', 'employees.designation_id', '=', 'designations.id')
                      ->orderBy('designations.name', $orderDirection)
                      ->select($tableName . '.*');
                break;
            case 'organizer_name':
                $query->leftJoin('trainings', $tableName . '.training_id', '=', 'trainings.id')
                      ->leftJoin('organizers', 'trainings.organization_id', '=', 'organizers.id')
                      ->orderBy('organizers.name', $orderDirection)
                      ->select($tableName . '.*');
                break;
            case 'working_place':
                $query->leftJoin('employees', $tableName . '.employee_id', '=', 'employees.id')
                      ->orderBy('employees.working_place', $orderDirection)
                      ->select($tableName . '.*');
                break;
            case 'start_date':
                $query->leftJoin('group_trainings', $tableName . '.group_training_id', '=', 'group_trainings.id')
                      ->orderBy('group_trainings.start_date', $orderDirection)
                      ->select($tableName . '.*');
                break;
            case 'end_date':
                $query->leftJoin('group_trainings', $tableName . '.group_training_id', '=', 'group_trainings.id')
                      ->orderBy('group_trainings.end_date', $orderDirection)
                      ->select($tableName . '.*');
                break;
            case 'total_days':
                $query->leftJoin('group_trainings', $tableName . '.group_training_id', '=', 'group_trainings.id')
                      ->orderBy('group_trainings.total_days', $orderDirection)
                      ->select($tableName . '.*');
                break;
            default:
                $query->orderBy($tableName . '.created_at', $orderDirection);
        }

        return $query->get();
    }
}