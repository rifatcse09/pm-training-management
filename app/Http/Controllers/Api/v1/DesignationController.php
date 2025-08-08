<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\Designation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\v1\DesignationService;
use App\Http\Requests\StoreDesignationRequest;
use App\Http\Requests\UpdateDesignationRequest;

class DesignationController extends Controller
{
    protected $service;

    public function __construct(DesignationService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return response()->json($this->service->getAll());
    }

    public function store(StoreDesignationRequest $request)
    {
        $designation = $this->service->create($request->validated());
        return response()->json($designation, 201);
    }

    public function update(UpdateDesignationRequest $request, Designation $designation)
    {
        $designation = $this->service->update($designation, $request->validated());
        return response()->json($designation);
    }

    public function destroy(Designation $designation)
    {
        $this->service->delete($designation);
        return response()->noContent();
    }
}
