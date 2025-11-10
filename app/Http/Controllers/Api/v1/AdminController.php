<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\v1\AdminService;
use App\Http\Resources\UserResource;
use App\Helpers\HttpStatus;
use Illuminate\Http\JsonResponse;

class AdminController extends Controller
{
    protected $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function listPendingUsers(): JsonResponse
    {
        $users = $this->adminService->listPendingUsers();
        return response()->json([
            'success' => true,
            'data' => UserResource::collection($users), // Directly use the collection
        ], HttpStatus::OK);
    }

    public function activateUser(Request $request, int $userId): JsonResponse
    {
        $request->validate([
            'role_id' => 'nullable|integer|exists:roles,id'
        ]);

        $user = $this->adminService->activateUser($userId);

        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'User not found or already active',
            ], HttpStatus::NOT_FOUND);
        }

        $user->is_active = true;

        if ($request->has('role_id') && $request->role_id) {
            $user->role_id = $request->role_id;
        }

        $user->save();

        return response()->json([
            'success' => true,
            'data' => new UserResource($user),
        ], HttpStatus::OK);
    }

    public function listAllUsers(): JsonResponse
    {
        $users = $this->adminService->listAllUsers();
        return response()->json([
            'success' => true,
            'data' => UserResource::collection($users),
        ], HttpStatus::OK);
    }

    public function assignRole(Request $request, int $userId): JsonResponse
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

    public function updateUser(Request $request, int $userId): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $userId,
            'designation_id' => 'sometimes|nullable|integer|exists:designations,id',
            'role_id' => 'sometimes|nullable|integer|exists:roles,id'
        ]);

        $user = $this->adminService->updateUser($userId, $request->only(['name', 'email', 'designation_id', 'role_id']));

        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'User not found',
            ], HttpStatus::NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'data' => new UserResource($user),
            'message' => 'User updated successfully'
        ], HttpStatus::OK);
    }

    public function deleteUser(int $userId): JsonResponse
    {
        try {
            $deleted = $this->adminService->deleteUser($userId);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'error' => 'User not found or cannot be deleted',
                ], HttpStatus::NOT_FOUND);
            }

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully',
            ], HttpStatus::OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to delete user: ' . $e->getMessage(),
            ], HttpStatus::INTERNAL_SERVER_ERROR);
        }
    }
}