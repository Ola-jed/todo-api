<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (){
    Route::get('/tasks',[TaskController::class,'all']);
    Route::post('/tasks',[TaskController::class,'store']);
    Route::get('/tasks/{slug}',[TaskController::class,'show']);
    Route::put('/tasks/{slug}',[TaskController::class,'update']);
    Route::put('/tasks/{slug}/finish',[TaskController::class,'update']);
    Route::delete('/tasks/{slug}',[TaskController::class,'delete']);
});
