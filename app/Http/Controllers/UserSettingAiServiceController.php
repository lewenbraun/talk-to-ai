<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\UserSettingAiService;
use App\Http\Requests\AiService\SetUrlApiUserSettingAiServiceRequest;
use App\Http\Requests\AiService\SetApiKeyUserSettingAiServiceRequest;
use Throwable;

class UserSettingAiServiceController extends Controller
{
    public function setApiKey(SetApiKeyUserSettingAiServiceRequest $request): JsonResponse
    {
        $aiServiceId = $request->input('ai_service_id');
        $apiKey = $request->input('api_key');

        UserSettingAiService::createOrUpdate(
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
        $aiServiceId = $request->input('ai_service_id');
        $apiUrl = $request->input('url_api');

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
