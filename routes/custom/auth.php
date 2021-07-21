<?php

use App\Http\Controllers\Auth\UserAuthController;
use Illuminate\Support\Facades\Route;

Route::post('/signup',[UserAuthController::class,'signup']);
Route::post('/signin',[UserAuthController::class,'signin'])
    ->name('login');
Route::post('/password-reset',[UserAuthController::class,'signin']);
Route::post('/logout',[UserAuthController::class,'logout'])
    ->middleware('auth:sanctum');
