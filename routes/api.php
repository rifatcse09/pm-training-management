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
use App\Http\Controllers\Api\v1\TrainingController;
use App\Http\Controllers\Api\v1\TrainingAssignmentController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {

      // Preflight handler (lets Laravel add CORS headers)
      Route::options('/{any}', fn() => response('', 204))
      ->where('any', '.*');

    // Public: register
    Route::post('/register', [UserController::class, 'register']);
    // Public: login
    Route::post('/login', [AuthController::class, 'login']);

    Route::apiResource('designations', DesignationController::class);

    // Protected routes
    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);

        // Employee routes
        Route::get('employees', [EmployeeController::class, 'index']);
        Route::post('employees', [EmployeeController::class, 'store']);
        Route::get('employees/{id}', [EmployeeController::class, 'show']);
        Route::put('employees/{id}', [EmployeeController::class, 'update']);
        Route::delete('employees/{id}', [EmployeeController::class, 'destroy']);

        // Training routes
        Route::controller(TrainingController::class)->group(function () {
            Route::get('trainings', 'index');
            Route::post('trainings', 'store');
            Route::get('trainings/{id}', 'show');
            Route::put('trainings/{id}', 'update');
            Route::delete('trainings/{id}', 'destroy');
        });

        // Organizer routes
        Route::controller(OrganizerController::class)->group(function () {
            Route::get('organizers', 'index');
            Route::post('organizers', 'store');
            Route::get('organizers/{id}', 'show');
            Route::put('organizers/{id}', 'update');
            Route::delete('organizers/{id}', 'destroy');
            Route::get('project-organizers', 'getProjectOrganizers'); // New route
        });

        Route::post('trainings/assign', [TrainingAssignmentController::class, 'assign'])->name('trainings.assign');
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
