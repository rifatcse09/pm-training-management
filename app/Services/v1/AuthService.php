<?php

namespace App\Services\v1;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Resources\UserResource;
use App\Helpers\HttpStatus;

class AuthService
{
    public function login(array $credentials)
    {
        $user = User::with('role')->where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return [
                'success' => false,
                'error' => 'Invalid credentials',
                'status' => HttpStatus::UNAUTHORIZED,
            ];
        }

        if (!$user->is_active) {
            return [
                'success' => false,
                'error' => 'Account not activated',
                'status' => HttpStatus::FORBIDDEN,
            ];
        }

        $token = Auth::login($user);

        // Calculate the expiration time in seconds using config/jwt.php
        $expiresIn = config('jwt.ttl', 60) * 60 * 24 * 30; // Default to 60 minutes if not set

        return [
            'success' => true,
            'data' => [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => $expiresIn,
                'user' => new UserResource($user),
            ],
            'status' => HttpStatus::OK,
        ];
    }
}
