<?php

namespace App\Providers;

use DB;
use Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        ResetPassword::createUrlUsing(function ($notifiable, string $token) {
            $frontend = config('app.frontend_url', env('FRONTEND_URL')); // add APP_FRONTEND_URL if you prefer
            $email = urlencode($notifiable->getEmailForPasswordReset());
            return "{$frontend}/reset-password?token={$token}&email={$email}";
        });

        // (Optional) customize the mail content
        ResetPassword::toMailUsing(function ($notifiable, string $url) {
            return (new MailMessage)
                ->subject('Reset Your Password')
                ->line('Click the button below to reset your password.')
                ->action('Reset Password', $url)
                ->line('If you did not request a password reset, no further action is required.');
        });
        
        DB::listen(function ($query) {
            Log::info('SQL Query', [
                'sql' => $query->sql,
                'bindings' => $query->bindings,
                'time' => $query->time,
            ]);
        });
    }
}
