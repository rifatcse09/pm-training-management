<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(RegisterUserRequest $request)
    {
        $user = User::create([
            'emp_name' => $request->emp_name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'emp_dob' => $request->emp_dob,
            'gender' => $request->gender,
            // 'role_id' => 3, // Default role: Employee
            // 'is_active' => false, // Must be activated by Admin
        ]);

        return response()->json([
            'message' => 'Registration successful. Please wait for admin approval.',
            'user' => $user
        ], 201);
    }
}
