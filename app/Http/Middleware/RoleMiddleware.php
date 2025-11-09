<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user('api');

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $userRoleName = strtolower($user->role->role_name ?? '');

        // convert all route roles to lowercase too
        $allowedRoles = array_map('strtolower', $roles);

        if (!in_array($userRoleName, $allowedRoles)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}