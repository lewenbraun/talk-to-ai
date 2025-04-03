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
    public function listByAiService(AiService $aiService): JsonResponse
    {
        $aiManager = new AiManagerService($aiService);
        $llms = $aiManager->redirectListLLM();

        return response()->json($llms);
    }

    public function add(AddLLMRequest $request): JsonResponse
    {
        $llmName = $request->input('llm_name')->trim();
        $aiServiceId = $request->integer('ai_service_id');

        $aiService = AiService::findOrFail($aiServiceId);
        $aiManager = new AiManagerService($aiService);

        $addedLLM = $aiManager->redirectAddLLM($llmName);

        return response()->json($addedLLM);
    }

    public function delete(DeleteLLMRequest $request): JsonResponse
    {
        $llmId = $request->integer('llm_id');
        $aiServiceId = $request->integer('ai_service_id');

        $llm = LLM::findOrFail($llmId);

        $aiService = AiService::findOrFail($aiServiceId);

        $aiManager = new AiManagerService($aiService);

        $aiManager->redirectDeleteLLM($llm);

        return response()->json();
    }
}
