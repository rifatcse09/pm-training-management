<?php

namespace App\Services\v1;

use App\Models\Employee;
use App\Models\Training;
use App\Models\EmployeeTraining;
use App\Models\GroupTraining;
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

    public function getAllAssignments($page = 1, $perPage = 10, $search = null)
    {
        $query = EmployeeTraining::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('training', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%");
                })->orWhereHas('employee', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%")
                      ->orWhere('email', 'LIKE', "%$search%");
                });
            });
        }

        return $query->with([
            'training.organizer', // Include organizer relation
            'employee',
            'designation', // Include designation relation
            'groupTraining' => function ($q) { // Include groupTraining relation
                $q->select('id', 'start_date', 'end_date', 'total_days', 'file_link', 'file_name');
            },
        ])->paginate($perPage, ['*'], 'page', $page);
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
}