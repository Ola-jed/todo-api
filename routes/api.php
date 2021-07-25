<?php

\Illuminate\Support\Facades\Route::get('/token-check', function() {
    return response()->json([
        'authenticated' => true
    ]);
})->middleware('auth:sanctum');
