<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'emp_name' => 'Admin User',
                'password' => Hash::make('password123'),
                'role_id' => 1, // assuming 1 is admin
                'is_active' => true,
                'gender' => 'Other',
                'mobile' => '0123456789',
                'emp_dob' => '1990-01-01',
            ]
        );
    }
}
