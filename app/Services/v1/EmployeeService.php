<?php

namespace App\Services\v1;

use App\Models\Employee;
use App\Enums\WorkingPlaceEnum;

class EmployeeService
{
    public function getAllEmployees($page = 1, $perPage = 10, $search = null, $workingPlace = null, $designationId = null)
    {
        $query = Employee::query();

        if ($search) {
            // Handle special grade search conditions separately
            if (strtolower($search) === 'grade-9') {
                $query->whereHas('designation', function ($q) {
                    $q->whereIn('id', range(1, 29));
                });
            } elseif (strtolower($search) === 'grade-10') {
                $query->whereHas('designation', function ($q) {
                    $q->whereIn('id', [30, 34]);
                });
            } elseif (strtolower($search) === 'grade-11-16') {
                $query->whereHas('designation', function ($q) {
                    $q->whereIn('id', [35, 45]);
                });
            } elseif (strtolower($search) === 'grade-17-20') {
                $query->whereHas('designation', function ($q) {
                    $q->where('id', '>=', 46);
                });
            } else {
                // Handle general search for other cases
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%")
                    ->orWhere('email', 'LIKE', "%$search%")
                    ->orWhere('mobile', 'LIKE', "%$search%")
                    ->orWhereHas('designation', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%$search%")
                            ->orWhere('grade', 'LIKE', "%$search%");
                    });

                    // Map working place name to ID using WorkingPlaceEnum
                    $workingPlaceId = array_search($search, WorkingPlaceEnum::getNames());
                    if ($workingPlaceId !== false) {
                        $q->orWhere('working_place', $workingPlaceId);
                    }
                });
            }
        }

        if ($workingPlace) {
            $query->where('working_place', $workingPlace);
        }

        if ($designationId) {
            $query->where('designation_id', $designationId);
        }

        return $query->with('designation')->paginate($perPage, ['*'], 'page', $page);
    }

    public function createEmployee(array $data)
    {
        return Employee::create($data);
    }

    public function getEmployeeById($id)
    {
        return Employee::findOrFail($id);
    }

    public function updateEmployee($id, array $data)
    {
        $employee = Employee::findOrFail($id);
        $employee->update($data);
        return $employee;
    }

    public function deleteEmployee($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();
    }
}