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
            'name' => $data['name'],
            'mobile' => $data['mobile'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'designation_id' => $data['designation_id'],
        ]);
    }

    public function activateUser(User $user): User
    {
        $user->update(['is_active' => true]);
        return $user;
    }
}
