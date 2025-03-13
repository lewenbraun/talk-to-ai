<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AiService;
use Illuminate\Http\Request;
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

        $aiServiceList = AiService::with('llms')->get();

        return response()->json($aiServiceList);
    }

    public function update(Request $request): JsonResponse
    {
        $aiService = AiService::find($request->input('service_id'));

        $aiService->update([
            'url_api' => $request->input('url_api'),
        ]);

        return response()->json();
    }
}
