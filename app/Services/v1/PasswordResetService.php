<?php

namespace App\Services\v1;

use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class PasswordResetService
{
    /**
     * Send a password reset link to the given email.
     *
     * @param string $email
     * @return string
     */
    public function sendResetLink(string $email): string
    {
        $status = Password::sendResetLink(['email' => $email]);

        return $status;
    }

    /**
     * Reset the password using the token and new password.
     *
     * @param array $data ['email', 'token', 'password', 'password_confirmation']
     * @return string
     */
    public function resetPassword(array $data): string
    {
        $status = Password::reset(
            $data,
            function (User $user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        return $status;
    }
}
