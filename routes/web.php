<?php

declare(strict_types=1);

use App\Http\Controllers\AppController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function () {
    Route::prefix('chat')->group(function () {
        Route::post('/new/send-message', [ChatController::class, 'sendMessageInNewChat']);
        Route::post('/send-message', [ChatController::class, 'sendMessageInExistingChat']);
    });
});

Route::get('/{any}', AppController::class)
    ->where('any', '.*')
    ->name('app');
