<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AiService;
use App\Models\LLM;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\AiManagerService;

class LLMController extends Controller
{
    private AiManagerService $aiManagerService;

    public function __construct(AiManagerService $aiManagerService)
    {
        $this->aiManagerService = $aiManagerService;
    }

    public function listByAiService(int $ai_service_id): JsonResponse
    {
        $aiService = AiService::findOrFail($ai_service_id);
        $llms = $this->aiManagerService->redirectListLLM($aiService);

        return response()->json($llms);
    }

    public function add(Request $request): JsonResponse
    {
        $aiService = AiService::findOrFail($request->input('ai_service_id'));
        $llmName = $request->input('llm_name');
        $addedLLM = $this->aiManagerService->redirectAddLLM($aiService, $llmName);

        return response()->json($addedLLM);
    }

    public function delete(Request $request): JsonResponse
    {
        $llm = LLM::findOrFail($request->input('llm_id'));
        $aiService = AiService::findOrFail($request->input('ai_service_id'));

        $this->aiManagerService->redirectDeleteLLM($aiService, $llm);

        return response()->json();
    }
}
