<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeRequest;
use App\Models\Employee;
use App\Services\EmployeeService;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    protected $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    public function index()
    {
        return response()->json($this->employeeService->getAllEmployees());
    }

    public function store(EmployeeRequest $request)
    {
        return response()->json($this->employeeService->createEmployee($request->validated()), 201);
    }

    public function show($id)
    {
        return response()->json($this->employeeService->getEmployeeById($id));
    }

    public function update(EmployeeRequest $request, $id)
    {
        return response()->json($this->employeeService->updateEmployee($id, $request->validated()));
    }

    public function destroy($id)
    {
        $this->employeeService->deleteEmployee($id);
        return response()->json(['message' => 'Employee deleted successfully.']);
    }
}
