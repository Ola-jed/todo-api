<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function() {
    Route::get('/tasks', [TaskController::class, 'all']);
    Route::get('/tasks/finished', [TaskController::class, 'getFinished']);
    Route::get('/tasks/unfinished', [TaskController::class, 'getUnfinished']);
    Route::get('/tasks/expired', [TaskController::class, 'getExpired']);
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::get('/tasks/search/{title}', [TaskController::class, 'search']);
    Route::get('/tasks/{slug}', [TaskController::class, 'show']);
    Route::put('/tasks/{slug}', [TaskController::class, 'update']);
    Route::put('/tasks/{slug}/finish', [TaskController::class, 'finish']);
    Route::delete('/tasks/{slug}', [TaskController::class, 'delete']);
});
