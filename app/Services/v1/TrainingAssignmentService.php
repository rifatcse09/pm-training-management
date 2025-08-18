<?php

namespace App\Services\v1;

use App\Models\Employee;
use App\Models\Training;
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
}
