<?php

use App\Http\Controllers\UserAccountController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function() {
    Route::get('/account',[UserAccountController::class,'getAccount']);
    Route::put('/account',[UserAccountController::class,'updateAccount']);
    Route::delete('/account',[UserAccountController::class,'deleteAccount']);
});
