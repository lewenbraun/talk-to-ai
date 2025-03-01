<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Auth\AuthController;

Route::middleware('auth:sanctum')->prefix('chat')->group(function () {
    Route::post('/new/send-message', [ChatController::class, 'sendMessageInNewChat']);
    Route::post('/send-message', [ChatController::class, 'sendMessageInExistingChat']);
});

Route::post('/create-temporary-user', [AuthController::class, 'createTemporaryUser']);
