<?php

namespace App\Http\Controllers\Api\v1;

use App\Services\v1\AdminService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    protected $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function pendingUsers()
    {
        $users = $this->adminService->listPendingUsers();
        return response()->json($users);
    }

    public function activate($id)
    {
        $user = $this->adminService->activateUser($id);
        return $user ? response()->json(['message' => 'User activated']) :
        response()->json(['error' => 'User not found'], 404);
    }

    public function allUsers()
    {
        return response()->json($this->adminService->listAllUsers());
    }

    public function assignRole(Request $request, $userId)
    {
        $request->validate(['role_id' => 'required|exists:roles,id']);
        $user = $this->adminService->assignRole($userId, $request->role_id);

        return $user ? response()->json(['message' => 'Role updated']) :
        response()->json(['error' => 'User not found'], 404);
    }
}
