<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\AiService\AddApiKeyUserSettingAiServiceRequest;
use App\Models\UserSettingAiService;
use Illuminate\Http\JsonResponse;

class UserSettingAiServiceController extends Controller
{
    public function addApiKey(AddApiKeyUserSettingAiServiceRequest $request): JsonResponse
    {
        UserSettingAiService::createOrUpdate(
            [
                'user_id' => auth()->id(),
                'ai_service_id' => $request->input('ai_service_id'),
            ],
            [
                'api_key' => $request->input('api_key')
            ]
        );

        return response()->json();
    }
}
