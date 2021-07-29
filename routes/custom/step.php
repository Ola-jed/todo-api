<?php

use App\Http\Controllers\StepController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function() {
    Route::get('/tasks/{slug}/steps',[StepController::class,'all']);
    Route::post('/tasks/{slug}/steps',[StepController::class,'store']);
    Route::get('/steps/{id}',[StepController::class,'getStep'])
        ->whereNumber('id');
    Route::put('/steps/{id}',[StepController::class,'update'])
        ->whereNumber('id');
    Route::put('/steps/{id}/finish',[StepController::class,'finish'])
        ->whereNumber('id');
    Route::delete('/steps/{id}',[StepController::class,'delete'])
        ->whereNumber('id');
});
