<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Services\v1\EmployeeService;
use App\Helpers\HttpStatus;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    protected $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    public function index(Request $request)
    {
        $search = $request->query('search', null); // Get the search query
        $employees = $this->employeeService->getAllEmployees(
            $request->query('page', 1),
            $request->query('per_page', 10),
            $search
        );

        return response()->json([
            'success' => true,
            'data' => EmployeeResource::collection($employees->items()),
            'meta' => [
                'current_page' => $employees->currentPage(),
                'last_page' => $employees->lastPage(),
                'per_page' => $employees->perPage(),
                'total' => $employees->total(),
            ],
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
