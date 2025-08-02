<?php

namespace App\Http\Controllers\Api\v1;

use App\Services\v1\UserService;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Handle user registration.
     *
     * @param RegisterUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterUserRequest $request): \Illuminate\Http\JsonResponse
    {
        $user = $this->userService->create($request->validated());

        return response()->json([
            'message' => 'Registration successful. Please wait for admin approval.',
            'user' => $user
        ], 201);
    }
}
