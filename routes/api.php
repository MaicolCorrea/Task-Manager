<?php

use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\usersController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\NotificationController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [usersController::class, 'createUser']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    
    Route::prefix('users')->group(function () {
        Route::get('/', [usersController::class, 'consultAllDataUsers']);
        Route::get('/{id}', [usersController::class, 'consultUserById']);
        Route::put('/{id}', [usersController::class, 'updateUser']);
        Route::patch('/{id}/password', [usersController::class, 'changePassword']);
    });

    Route::prefix('projects')->group(function () {
        Route::get('/', [ProjectController::class, 'index']);
        Route::post('/', [ProjectController::class, 'store']);
        Route::get('/{id}', [ProjectController::class, 'show']);
        Route::put('/{id}', [ProjectController::class, 'update']);
        Route::delete('/{id}', [ProjectController::class, 'destroy']);
        Route::post('/{id}/tasks', [ProjectController::class, 'addTaskToProject']);
        Route::get('/{id}/stats', [ProjectController::class, 'getProjectStats']);
    });

    Route::prefix('tasks')->group(function () {
        Route::get('/', [TaskController::class, 'index']);
        Route::post('/', [TaskController::class, 'store']);
        Route::get('/{id}', [TaskController::class, 'show']);
        Route::put('/{id}', [TaskController::class, 'update']);
        Route::delete('/{id}', [TaskController::class, 'destroy']);
        Route::post('/{id}/tags', [TaskController::class, 'attachTags']);
        Route::get('/status/{status}', [TaskController::class, 'getByStatus']);
        Route::get('/priority/{priority}', [TaskController::class, 'getByPriority']);
        Route::get('/user/{userId}', [TaskController::class, 'getUserTasks']);
    });

    Route::prefix('comments')->group(function () {
        Route::post('/', [CommentController::class, 'store']);
        Route::delete('/{comment}', [CommentController::class, 'destroy']);
    });

    Route::prefix('attachments')->group(function () {
        Route::post('/', [AttachmentController::class, 'store']);
        Route::get('/{attachment}', [AttachmentController::class, 'show']);
        Route::delete('/{attachment}', [AttachmentController::class, 'destroy']);
    });

    Route::apiResource('tasks', TaskController::class);
    
    Route::apiResource('tags', TagController::class);
    
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::post('notifications', [NotificationController::class, 'store']);
    Route::patch('notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
    Route::delete('notifications/{notification}', [NotificationController::class, 'destroy']);
});
