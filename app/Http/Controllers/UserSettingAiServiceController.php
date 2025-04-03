<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Models\UserSettingAiService;
use App\Http\Requests\AiService\SetUrlApiUserSettingAiServiceRequest;
use App\Http\Requests\AiService\SetApiKeyUserSettingAiServiceRequest;

class UserSettingAiServiceController extends Controller
{
    public function setApiKey(SetApiKeyUserSettingAiServiceRequest $request): JsonResponse
    {
        $aiServiceId = $request->integer('ai_service_id');
        $apiKey = $request->integer('api_key');

        UserSettingAiService::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'ai_service_id' => $aiServiceId,
            ],
            [
                'api_key' => $apiKey
            ]
        );

        return response()->json();
    }

    public function setApiUrl(SetUrlApiUserSettingAiServiceRequest $request): JsonResponse
    {
        $aiServiceId = $request->integer('ai_service_id');
        $apiUrl = $request->integer('url_api');

        UserSettingAiService::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'ai_service_id' => $aiServiceId,
            ],
            [
                'url_api' => $apiUrl,
            ]
        );

        return response()->json();
    }
}
