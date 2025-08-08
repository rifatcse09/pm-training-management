<?php

namespace App\Http\Controllers\Api\v1\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;
use App\Helpers\HttpStatus;
use App\Services\v1\AuthService;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);
        $response = $this->authService->login($credentials);

        return response()->json(
            ['success' => $response['success'], 'data' => $response['data'] ?? null, 'error' => $response['error'] ?? null],
            $response['status']
        );
    }

    public function me()
    {
        $user = Auth::user()->load('role');
        return response()->json([
            'success' => true,
            'data' => ['resource' => new UserResource($user)],
        ], HttpStatus::OK);
    }

    public function logout()
    {
        Auth::logout();
        return response()->json(['success' => true, 'data' => ['message' => 'Logged out successfully']], HttpStatus::OK);
    }
}
