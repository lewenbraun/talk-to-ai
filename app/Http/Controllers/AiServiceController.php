<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AiService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\AiServiceResource;

class AiServiceController extends Controller
{
    public function list(): JsonResponse
    {
        $aiServiceList = AiService::with(['llms' => function ($query): void {
            $query->where('user_id', auth()->id());
        }, 'userSettings'])->get();

        return AiServiceResource::collection($aiServiceList)->response();
    }
}
