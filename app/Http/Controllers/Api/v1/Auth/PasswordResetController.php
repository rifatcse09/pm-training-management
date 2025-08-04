<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Services\v1\PasswordResetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class PasswordResetController extends Controller
{
    protected $passwordResetService;

    public function __construct(PasswordResetService $passwordResetService)
    {
        $this->passwordResetService = $passwordResetService;
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = $this->passwordResetService->sendResetLink($request->email);

        return response()->json([
            'status' => $status === Password::RESET_LINK_SENT
                ? 'Reset link sent to your email.'
                : 'Unable to send reset link.'
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ]);

        $status = $this->passwordResetService->resetPassword($request->only(
            'email', 'token', 'password', 'password_confirmation'
        ));

        return response()->json([
            'status' => $status === Password::PASSWORD_RESET
                ? 'Password reset successful.'
                : 'Password reset failed.'
        ]);
    }
}
