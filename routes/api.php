<?php

declare(strict_types=1);

use App\Http\Controllers\AiServiceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\LLMController;
use App\Http\Controllers\UserSettingAiServiceController;

Route::middleware('auth:sanctum')->prefix('chat')->group(function (): void {
    Route::post('/create', [ChatController::class, 'createChat']);
    Route::post('/send-message', [ChatController::class, 'sendMessage']);
    Route::get('/list', [ChatController::class, 'list']);
    Route::get('/messages/{chat}', [ChatController::class, 'chatMessages']);
});

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/user', fn () => auth()->user());
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware('auth:sanctum')->prefix('ai-service')->group(function (): void {
    Route::get('/list', [AiServiceController::class, 'list']);
    Route::get('/llm/list/{aiService}', [LLMController::class, 'listByAiService']);
    Route::post('/llm/add', [LLMController::class, 'add']);
    Route::post('/llm/delete', [LLMController::class, 'delete']);
    Route::post('/api-key/set', [UserSettingAiServiceController::class, 'setApiKey']);
    Route::post('/url-api/set', [UserSettingAiServiceController::class, 'setApiUrl']);
});

Route::middleware('optional_auth')->group(function (): void {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/create-temporary-user', [AuthController::class, 'createTemporaryUser']);

Route::post('/broadcasting/auth', fn() => auth()->user());
