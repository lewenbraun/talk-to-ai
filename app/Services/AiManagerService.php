<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\LLM;
use App\Models\Chat;
use App\Models\AiService;
use App\Enums\AiServiceEnum;
use Illuminate\Database\Eloquent\Collection;

class AiManagerService
{
    private OllamaService $ollamaService;

    public function __construct(OllamaService $ollamaService)
    {
        $this->ollamaService = $ollamaService;
    }

    public function redirectGenerateAnswer(Chat $chat): void
    {
        $aiService = $chat->llm->aiService;

        match ($aiService->name) {
            AiServiceEnum::OLLAMA->value => $this->ollamaService->generateAnswer($chat),
        };
    }

    public function redirectAddLLM(AiService $aiService, string $modelName): LLM
    {
        $model = match ($aiService->name) {
            AiServiceEnum::OLLAMA->value => $this->ollamaService->addLLM($aiService->id, $modelName),
        };

        return $model;
    }

    public function redirectDeleteLLM(AiService $aiService, LLM $llm): void
    {
        match ($aiService->name) {
            AiServiceEnum::OLLAMA->value => $this->ollamaService->deleteLLM($llm),
        };
    }

    public function redirectListLLM(AiService $aiService): Collection
    {
        $llms = match ($aiService->name) {
            AiServiceEnum::OLLAMA->value => $this->ollamaService->listLLM($aiService),
        };

        return $llms;
    }

    public function updateListLLMs(): void
    {
        $this->ollamaService->updateListLLM();
    }
}
