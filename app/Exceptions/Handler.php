<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Helpers\HttpStatus;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        // ...existing code...
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register()
    {
        $this->renderable(function (Throwable $e, $request) {
            return $this->handleException($e);
        });
    }

    /**
     * Handle exceptions and return a standardized JSON response.
     *
     * @param  \Throwable  $e
     * @return \Illuminate\Http\JsonResponse
     */
    private function handleException(Throwable $e)
    {
        if ($e instanceof HttpResponseException) {
            return $e->getResponse();
        }

        $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : HttpStatus::INTERNAL_SERVER_ERROR;

        // Generic error message for users
        $message = $status === HttpStatus::INTERNAL_SERVER_ERROR
            ? 'An unexpected error occurred. Please try again later.'
            : $e->getMessage();

        return response()->json([
            'success' => false,
            'error' => $message,
            'details' => config('app.debug') ? $e->getMessage() : null, // Only show details in debug mode
        ], $status);
    }
}
