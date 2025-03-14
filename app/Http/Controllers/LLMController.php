<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\LLM\AddLLMRequest;
use App\Http\Requests\LLM\DeleteLLMRequest;
use App\Models\AiService;
use App\Models\LLM;
use App\Services\AiManagerService;
use Illuminate\Http\JsonResponse;

class LLMController extends Controller
{
    private AiManagerService $aiManagerService;

    public function __construct(AiManagerService $aiManagerService)
    {
        $this->aiManagerService = $aiManagerService;
    }

    public function listByAiService(AiService $aiService): JsonResponse
    {
        $llms = $this->aiManagerService->redirectListLLM($aiService);

        return response()->json($llms);
    }

    public function add(AddLLMRequest $request): JsonResponse
    {
        $aiService = AiService::findOrFail($request->input('ai_service_id'));
        $llmName = $request->input('llm_name');
        $addedLLM = $this->aiManagerService->redirectAddLLM($aiService, $llmName);

        return response()->json($addedLLM);
    }

    public function delete(DeleteLLMRequest $request): JsonResponse
    {
        $llm = LLM::findOrFail($request->input('llm_id'));
        $aiService = AiService::findOrFail($request->input('ai_service_id'));

        $this->aiManagerService->redirectDeleteLLM($aiService, $llm);

        return response()->json();
    }
}
