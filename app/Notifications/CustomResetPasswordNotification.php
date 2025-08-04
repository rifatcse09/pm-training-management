<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPasswordNotification extends ResetPassword
{
    public function toMail($notifiable)
    {


        $frontendUrl = config('app.frontend_url', 'https://your-frontend.com');

        $resetLink = "{$frontendUrl}/reset-password?token={$this->token}&email=" . urlencode($notifiable->getEmailForPasswordReset());

        return (new MailMessage)
        ->subject('Reset Your Password')
        ->view('emails.reset-password', [
            'resetUrl' => $resetLink,
            'user' => $notifiable,
        ]);
    }
}
