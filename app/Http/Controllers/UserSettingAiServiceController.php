<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserSettingAiService;
use Illuminate\Http\JsonResponse;

class UserSettingAiServiceController extends Controller
{
    public function addApiKey(Request $request): JsonResponse
    {
        UserSettingAiService::createOrUpdate(
            [
                'user_id' => user()->id(),
                'ai_service_id' => $request->input('ai_service_id'),
            ],
            [
                'api_key' => $request->input('api_key')
            ]
        );

        return response()->json();
    }
}
