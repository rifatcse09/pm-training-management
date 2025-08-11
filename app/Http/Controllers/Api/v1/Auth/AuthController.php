<?php

namespace App\Http\Controllers\Api\v1\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserResource;
use App\Helpers\HttpStatus;
use App\Services\v1\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only(['email', 'password']);
        $response = $this->authService->login($credentials);

        return response()->json(
            ['success' => $response['success'], 'data' => $response['data'] ?? null, 'error' => $response['error'] ?? null],
            $response['status']
        );
    }

    public function me(): JsonResponse
    {
        $user = Auth::user()->load(['role', 'designation']); // Include designation relationship
        return response()->json([
            'success' => true,
            'data' => ['resource' => new UserResource($user)],
        ], HttpStatus::OK);
    }

    public function logout(): JsonResponse
    {
        Auth::logout();
        return response()->json(['success' => true, 'data' => ['message' => 'Logged out successfully']], HttpStatus::OK);
    }
}
