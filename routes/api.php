<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Auth\AuthController;

Route::middleware('auth:sanctum')->prefix('chat')->group(function () {
    Route::post('/create', [ChatController::class, 'createChat']);
    Route::post('/send-message', [ChatController::class, 'sendMessageInExistingChat']);

    Route::get('/list', [ChatController::class, 'chatList']);
    Route::get('/messages/{chat}', [ChatController::class, 'chatMessages']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn () => auth()->user());

    Route::group(['prefix' => 'user'], function () {
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::post('/create-temporary-user', [AuthController::class, 'createTemporaryUser']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/broadcasting/auth', function () {
    return auth()->user();
});
