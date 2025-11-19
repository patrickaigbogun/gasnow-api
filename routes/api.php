<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\PurchaseController;



Route::get('/ping', function () {
    return response()->json(['status' => 'ok', 'message' => 'pong'], 200);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/refresh', [AuthController::class, 'refresh']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [UserProfileController::class, 'show']);

    // Purchases (Bouncer can: middleware)
    Route::post('/purchases', [PurchaseController::class, 'store'])
        ->middleware('can:create-purchase');

    Route::get('/purchases', [PurchaseController::class, 'index'])
        ->middleware('can:read-purchases');

    Route::get('/purchases/{purchase}', [PurchaseController::class, 'show'])
        ->middleware('can:read-purchases');

    Route::put('/purchases/{purchase}', [PurchaseController::class, 'update'])
        ->middleware('can:update-purchases');

    Route::delete('/purchases/{purchase}', [PurchaseController::class, 'destroy'])
        ->middleware('can:delete-purchases');
});
