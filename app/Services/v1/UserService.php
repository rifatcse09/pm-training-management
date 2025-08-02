<?php

namespace App\Services\v1;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return User
     */
    public function create(array $data): User
    {
        return User::create([
            'emp_name' => $data['emp_name'],
            'mobile' => $data['mobile'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'emp_dob' => $data['emp_dob'],
            'gender' => $data['gender'],
        ]);
    }

    public function activateUser(User $user): User
    {
        $user->update(['is_active' => true]);
        return $user;
    }
}
