<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\v1\AdminService;
use App\Http\Resources\UserResource;
use App\Helpers\HttpStatus;

class AdminController extends Controller
{
    protected $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function listPendingUsers()
    {
        $users = $this->adminService->listPendingUsers();
        return response()->json([
            'success' => true,
            'data' => UserResource::collection($users), // Directly use the collection
        ], HttpStatus::OK);
    }

    public function activateUser(Request $request, int $userId)
    {
        $user = $this->adminService->activateUser($userId);

        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'User not found or already active',
            ], HttpStatus::NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'data' => new UserResource($user),
        ], HttpStatus::OK);
    }

    public function listAllUsers()
    {
        $users = $this->adminService->listAllUsers();
        return response()->json([
            'success' => true,
            'data' => UserResource::collection($users),
        ], HttpStatus::OK);
    }

    public function assignRole(Request $request, int $userId)
    {
        $roleId = $request->input('role_id');
        $user = $this->adminService->assignRole($userId, $roleId);

        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'User not found',
            ], HttpStatus::NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'data' => new UserResource($user),
        ], HttpStatus::OK);
    }
}
