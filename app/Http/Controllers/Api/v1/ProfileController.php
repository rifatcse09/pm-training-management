<?php

namespace App\Http\Controllers\Api\v1;

use App\Helpers\HttpStatus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UpdateProfileRequest;

class ProfileController extends Controller
{
    /**
     * Get user profile information.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'user' => $request->user()
        ]);
    }

    /**
     * Update user profile information.
     *
     * @param UpdateProfileRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateProfileRequest $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        $validatedData = $request->validated();

        // Handle password update if provided
        if (isset($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        }

        $user->update($validatedData);

        $user = Auth::user()->load(['role', 'designation']); // Include designation relationship
        return response()->json([
            'success' => true,
            'data' => ['resource' => new UserResource($user)],
        ], HttpStatus::OK);
    }

        // return response()->json([
        //     'message' => 'Profile updated successfully',
        //     'user' => $user->fresh()
        // ]);
        //  }
}