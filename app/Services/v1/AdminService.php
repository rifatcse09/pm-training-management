<?php

namespace App\Services\v1;

use App\Models\User;
use Illuminate\Support\Collection;

class AdminService
{
    /**
     * List all pending (inactive) users.
     */
    public function listPendingUsers(): Collection
    {
        return User::where('is_active', false)
            ->where('role_id', 3) // role_id 3 = employee
            ->with('designation') // Include designation relationship
            ->get();
    }

    /**
     * Activate a user by ID.
     */
    public function activateUser(int $userId): ?User
    {
        $user = User::find($userId);

        if ($user && !$user->is_active) {
            $user->update(['is_active' => true]);
        }

        return $user;
    }

    /**
     * List all users (optional: with filters).
     */
    public function listAllUsers(): Collection
    {
        return User::with('role')->where('is_active', 1)->get();
    }

    /**
     * Assign a new role to a user.
     */
    public function assignRole(int $userId, int $roleId): ?User
    {
        $user = User::find($userId);

        if ($user) {
            $user->update(['role_id' => $roleId]);
        }

        return $user;
    }
}
