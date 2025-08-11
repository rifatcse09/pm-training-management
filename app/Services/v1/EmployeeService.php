<?php

namespace App\Services\v1;

use App\Models\Employee;

class EmployeeService
{
    public function getAllEmployees()
    {
        return Employee::all();
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
