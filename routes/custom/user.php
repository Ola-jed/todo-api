<?php

use App\Http\Controllers\UserAccountController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (){
    Route::put('account/update',[UserAccountController::class,'updateAccount']);
    Route::delete('/account/delete',[UserAccountController::class,'deleteAccount']);
});
