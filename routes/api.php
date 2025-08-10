<?php

use Illuminate\Http\Request;
use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\Auth\AuthController;
use App\Http\Controllers\Api\v1\UserController;
use App\Http\Controllers\Api\v1\AdminController;
use App\Http\Controllers\Api\v1\Auth\PasswordResetController;
use App\Http\Controllers\Api\v1\DesignationController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {

    // Public: register
    Route::post('/register', [UserController::class, 'register']);
    // Public: login
    Route::post('/login', [AuthController::class, 'login']);

    Route::apiResource('designations', DesignationController::class);

    // Protected routes
    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });

    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink']);
    Route::post('/reset-password', [PasswordResetController::class, 'reset']);

    Route::middleware(['auth:api', 'IsAdmin'])->group(function () {
        Route::get('/admin/pending-users', [AdminController::class, 'listPendingUsers']);
        Route::post('/admin/activate-user/{id}', [AdminController::class, 'activateUser']);
        Route::get('/admin/users', [AdminController::class, 'listAllUsers']);
        Route::post('/admin/assign-role/{id}', [AdminController::class, 'assignRole']);
    });

});