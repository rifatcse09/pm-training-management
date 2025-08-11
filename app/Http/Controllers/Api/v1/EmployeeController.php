<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Services\v1\EmployeeService;
use App\Helpers\HttpStatus;

class EmployeeController extends Controller
{
    protected $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    public function index()
    {
        $employees = $this->employeeService->getAllEmployees();
        return response()->json([
            'success' => true,
            'data' => EmployeeResource::collection($employees),
        ], HttpStatus::OK);
    }

    public function store(EmployeeRequest $request)
    {
        $employee = $this->employeeService->createEmployee($request->validated());
        return response()->json([
            'success' => true,
            'data' => new EmployeeResource($employee),
        ], HttpStatus::CREATED);
    }

    public function show($id)
    {
        $employee = $this->employeeService->getEmployeeById($id);
        return response()->json([
            'success' => true,
            'data' => new EmployeeResource($employee),
        ], HttpStatus::OK);
    }

    public function update(EmployeeRequest $request, $id)
    {
        $employee = $this->employeeService->updateEmployee($id, $request->validated());
        return response()->json([
            'success' => true,
            'data' => new EmployeeResource($employee),
        ], HttpStatus::OK);
    }

    public function destroy($id)
    {
        $this->employeeService->deleteEmployee($id);
        return response()->json([
            'success' => true,
            'message' => 'Employee deleted successfully.',
        ], HttpStatus::OK);
    }
}
