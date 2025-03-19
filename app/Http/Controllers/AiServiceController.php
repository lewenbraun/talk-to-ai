<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\AiService\UpdateAiServiceRequest;
use App\Models\AiService;
use Illuminate\Http\JsonResponse;
use App\Services\AiManagerService;

class AiServiceController extends Controller
{
    private AiManagerService $aiManagerService;

    public function __construct(AiManagerService $aiManagerService)
    {
        $this->aiManagerService = $aiManagerService;
    }

    public function list(): JsonResponse
    {
        $this->aiManagerService->updateListLLMs();

        $aiServiceList = AiService::with(['llms' => function ($query) {
            $query->where('user_id', auth()->id());
        }])->get();

        return response()->json($aiServiceList);
    }
}
