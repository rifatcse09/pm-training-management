<?php

use Illuminate\Http\Request;
use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\UserController;
use App\Http\Controllers\Api\v1\AdminController;
use App\Http\Controllers\Api\v1\EmployeeController;
use App\Http\Controllers\Api\v1\Auth\AuthController;
use App\Http\Controllers\Api\v1\OrganizerController;
use App\Http\Controllers\Api\v1\DesignationController;
use App\Http\Controllers\Api\v1\Auth\PasswordResetController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {

    // Public: register
    Route::post('/register', [UserController::class, 'register']);
    // Public: login
    Route::post('/login', [AuthController::class, 'login']);

    Route::apiResource('designations', DesignationController::class);

    // Employee routes
    Route::get('employees', [EmployeeController::class, 'index']);
    Route::post('employees', [EmployeeController::class, 'store']);
    Route::get('employees/{id}', [EmployeeController::class, 'show']);
    Route::put('employees/{id}', [EmployeeController::class, 'update']);
    Route::delete('employees/{id}', [EmployeeController::class, 'destroy']);

    // Protected routes
    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);

        // Organizer routes
        Route::get('organizers', [OrganizerController::class, 'index']);
        Route::post('organizers', [OrganizerController::class, 'store']);
        Route::get('organizers/{id}', [OrganizerController::class, 'show']);
        Route::put('organizers/{id}', [OrganizerController::class, 'update']);
        Route::delete('organizers/{id}', [OrganizerController::class, 'destroy']);
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