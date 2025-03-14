<?php

declare(strict_types=1);

use App\Http\Controllers\AiServiceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\LLMController;
use App\Http\Controllers\UserSettingAiServiceController;

Route::middleware('auth:sanctum')->prefix('chat')->group(function () {
    Route::post('/create', [ChatController::class, 'createChat']);
    Route::post('/send-message', [ChatController::class, 'sendMessage']);

    Route::get('/list', [ChatController::class, 'list']);
    Route::get('/messages/{chat}', [ChatController::class, 'chatMessages']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn () => auth()->user());

    Route::group(['prefix' => 'user'], function () {
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::middleware('auth:sanctum')->prefix('ai-service')->group(function () {
    Route::get('/list', [AiServiceController::class, 'list']);
    Route::post('/update', [AiServiceController::class, 'update']);
    Route::get('/llm/list/{aiService}', [LLMController::class, 'listByAiService']);
    Route::post('/llm/add', [LLMController::class, 'add']);
    Route::post('/llm/update', [LLMController::class, 'update']);
    Route::post('/llm/delete', [LLMController::class, 'delete']);
    Route::post('/api-key/set', [UserSettingAiServiceController::class, 'setApiKey']);
});

Route::post('/create-temporary-user', [AuthController::class, 'createTemporaryUser']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/broadcasting/auth', function () {
    return auth()->user();
});
