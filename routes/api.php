<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\UserController;
use App\Http\Controllers\Api\v1\AdminController;
use App\Http\Controllers\Api\v1\CountryController;
use App\Http\Controllers\Api\v1\ProfileController;
use App\Http\Controllers\Api\v1\EmployeeController;
use App\Http\Controllers\Api\v1\TrainingController;
use App\Http\Controllers\Api\v1\Auth\AuthController;
use App\Http\Controllers\Api\v1\OrganizerController;
use App\Http\Controllers\Api\v1\DesignationController;
use App\Http\Controllers\Api\v1\TrainingReportController;
use App\Http\Controllers\Api\v1\Auth\PasswordResetController;
use App\Http\Controllers\Api\v1\TrainingAssignmentController;

Route::prefix('v1')->group(function () {

    // Preflight handler (lets Laravel add CORS headers)
    Route::options('/{any}', fn() => response('', 204))
    ->where('any', '.*');

    // Public: register
    Route::post('/register', [UserController::class, 'register']);
    // Public: login
    Route::post('/login', [AuthController::class, 'login']);

    // Public: fetch countries
    Route::get('countries', [CountryController::class, 'index']);

    Route::apiResource('designations', DesignationController::class);

    // Protected routes
    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/profile', [ProfileController::class, 'update']);

        // Employee routes
        Route::get('employees', [EmployeeController::class, 'index']);
        Route::post('employees', [EmployeeController::class, 'store']);
        Route::get('employees/{id}', [EmployeeController::class, 'show']);
        Route::put('employees/{id}', [EmployeeController::class, 'update']);
        Route::delete('employees/{id}', [EmployeeController::class, 'destroy']);
        Route::get('employees/{id}/trainings', [TrainingAssignmentController::class, 'getEmployeeTrainings'])->name('employees.trainings');

        // Training routes
        Route::controller(TrainingController::class)->group(function () {
            Route::get('trainings', 'index');
            Route::post('trainings', 'store'); // Add this line to support POST requests
            Route::get('trainings/assignments', [TrainingAssignmentController::class, 'index'])->name('trainings.assignments'); // Ensure this route is defined first
            Route::get('trainings/{id}', 'show')->where('id', '[0-9]+'); // Add constraint to match only numeric IDs
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
        Route::get('training-assignments/pdf', [TrainingAssignmentController::class, 'generateAssignmentsPdf']);
    });

    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink']);
    Route::post('/reset-password', [PasswordResetController::class, 'reset']);
    
    Route::middleware(['auth:api', 'IsAdmin'])->group(function () {
        Route::get('/admin/pending-users', [AdminController::class, 'listPendingUsers']);
        Route::post('/admin/activate-user/{id}', [AdminController::class, 'activateUser']);
        Route::get('/admin/users', [AdminController::class, 'listAllUsers']);
        Route::post('/admin/assign-role/{id}', [AdminController::class, 'assignRole']);
    });

    Route::get('/training-reports', [TrainingReportController::class, 'generateReport']);
});