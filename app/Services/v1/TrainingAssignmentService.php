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
        DB::transaction(function () use ($employee, $training, $data) {
            $exists = $employee->trainings()->where('training_id', $training->id)->exists();
            if ($exists) {
                throw ValidationException::withMessages(['assignment' => 'Already assigned.']);
            }

            $employee->trainings()->attach($training->id, [
                'assigned_at' => now(),
                'assigned_by' => $data['assigned_by'] ?? auth()->id(),
            ]);
        });
    }

    public function assignMultiple(Training $training, array $employeeIds): void
    {
        DB::transaction(function () use ($training, $employeeIds) {
            foreach ($employeeIds as $employeeId) {
                $employee = Employee::findOrFail($employeeId);
                $this->assign($employee, $training, [
                    'assigned_by' => auth()->id(),
                ]);
            }
        });
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

        \Log::info($query->toSql()); // Log the query for debugging

        return $query->with(['training', 'employee'])->paginate($perPage, ['*'], 'page', $page);
    }
}