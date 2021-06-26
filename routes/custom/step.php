<?php

use App\Http\Controllers\StepController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (){
    Route::get('/tasks/{slug}/steps',[StepController::class,'all']);
    Route::post('/tasks/{slug}/steps',[StepController::class,'store']);
    Route::get('/steps/{id}',[StepController::class,'getStep'])->where('id','[0-9]+');
    Route::put('/steps/{id}',[StepController::class,'update'])->where('id','[0-9]+');
    Route::put('/steps/{id}/finish',[StepController::class,'finish'])->where('id','[0-9]+');
    Route::delete('/steps/{id}',[StepController::class,'delete'])->where('id','[0-9]+');
});
