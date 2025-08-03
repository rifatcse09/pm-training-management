<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\UserController;
use App\Http\Controllers\Api\v1\AdminController;
use App\Http\Middleware\IsAdmin;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {

    // Public: register
    Route::post('/register', [UserController::class, 'register']);
    // Public: login
    Route::post('/login', [AuthController::class, 'login']);

    // Protected routes
    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });

    Route::middleware(['auth:api', 'IsAdmin'])->group(function () {
        Route::get('/admin/pending-users', [AdminController::class, 'pendingUsers']);
        Route::post('/admin/activate-user/{id}', [AdminController::class, 'activate']);
        Route::get('/admin/users', [AdminController::class, 'allUsers']);
        Route::post('/admin/assign-role/{id}', [AdminController::class, 'assignRole']);
    });
});
