<?php

namespace App\Services\v1;

use App\Models\Employee;
use App\Enums\WorkingPlaceEnum;
use App\Enums\SubjectGradeMappingEnum;

class EmployeeService
{
    public function getAllEmployees($page = 1, $perPage = 10, $search = null, $workingPlace = null, $designationId = null)
    {
        $query = Employee::query();

        if ($search) {
            // Handle special grade search conditions using enum
            $gradeEnum = SubjectGradeMappingEnum::fromGradeSearchTerm($search);
            
            if ($gradeEnum) {
                $this->applyGradeFilterFromEnum($query, $gradeEnum);
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

    /**
     * Apply grade filter using enum instance
     */
    private function applyGradeFilterFromEnum($query, SubjectGradeMappingEnum $gradeEnum): void
    {
        $designationRange = $gradeEnum->getDesignationRange();

        if ($designationRange === '>45') {
            // Special case for designations greater than 45
            $query->whereHas('designation', function ($q) {
                $q->where('id', '>', 45);
            });
        } elseif (is_array($designationRange) && !empty($designationRange)) {
            $query->whereHas('designation', function ($q) use ($designationRange) {
                $q->whereIn('id', $designationRange);
            });
        }
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