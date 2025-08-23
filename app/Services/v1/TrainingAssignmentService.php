<?php

namespace App\Services\v1;

use App\Models\Employee;
use App\Models\Training;
use App\Models\EmployeeTraining;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TrainingAssignmentService
{
    public function assign(Employee $employee, Training $training, array $data = []): void
    {
        try {
            DB::transaction(function () use ($employee, $training, $data) {
                $exists = $employee->trainings()->where('training_id', $training->id)->exists();
                if ($exists) {
                    \Log::warning('Employee already assigned to training', [
                        'employee_id' => $employee->id,
                        'training_id' => $training->id,
                    ]);
                    throw ValidationException::withMessages(['assignment' => 'Already assigned.']);
                }

                // Log the data being inserted
                \Log::info('Attaching employee to training', [
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
            \Log::info('Employee assigned successfully', [
                'employee_id' => $employee->id,
                'training_id' => $training->id,
            ]);
        } catch (\Exception $e) {
            // Log the exception for debugging
            \Log::error('Failed to assign employee to training', [
                'employee_id' => $employee->id,
                'training_id' => $training->id,
                'error' => $e->getMessage(),
            ]);

            // Optionally rethrow the exception to handle it at a higher level
            throw $e;
        }
    }

    public function assignMultiple(Training $training, array $employeeIds): void
    {
        try {
            DB::transaction(function () use ($training, $employeeIds) {
                foreach ($employeeIds as $employeeId) {
                    $employee = Employee::findOrFail($employeeId);

                    // Log the employee details
                    \Log::info('Processing employee assignment', [
                        'assigned_at' => now(),
                        'assigned_by' => auth()->id(),
                        'employee_id' => $employee->id,
                        'training_id' => $training->id,
                        'working_place' => $employee->working_place,
                        'designation_id' => $employee->designation_id,
                    ]);

                    // Call the assign method for each employee
                    $this->assign($employee, $training, [
                        'assigned_at' => now(),
                        'assigned_by' => auth()->id(),
                        'working_place' => $employee->working_place,
                        'designation_id' => $employee->designation_id,
                    ]);
                }
            });

            // Log success message
            \Log::info('All employees assigned successfully', [
                'training_id' => $training->id,
                'employee_ids' => $employeeIds,
            ]);
        } catch (\Exception $e) {
            // Log the exception for debugging
            \Log::error('Failed to assign employees to training', [
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

        return $query->with(['training', 'employee', 'designation'])->paginate($perPage, ['*'], 'page', $page);
    }
}