<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\LLM;
use App\Models\Chat;
use App\Models\AiService;
use App\Enums\AiServiceEnum;
use App\Models\UserSettingAiService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

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

        if (!$this->checkApiServiceHasUrl($aiService)) {
            return;
        }

        match ($aiService->name) {
            AiServiceEnum::OLLAMA->value => $this->ollamaService->generateAnswer($chat),
        };
    }

    public function redirectAddLLM(AiService $aiService, string $modelName): ?LLM
    {
        if (!$this->checkApiServiceHasUrl($aiService)) {
            return null;
        }

        $model = match ($aiService->name) {
            AiServiceEnum::OLLAMA->value => $this->ollamaService->addLLM($aiService->id, $modelName),
            default => null,
        };

        return $model;
    }

    public function redirectDeleteLLM(AiService $aiService, LLM $llm): void
    {
        if (!$this->checkApiServiceHasUrl($aiService)) {
            return;
        }

        match ($aiService->name) {
            AiServiceEnum::OLLAMA->value => $this->ollamaService->deleteLLM($llm),
        };
    }

    public function redirectListLLM(AiService $aiService): ?Collection
    {
        if (!$this->checkApiServiceHasUrl($aiService)) {
            return null;
        }

        $llms = match ($aiService->name) {
            AiServiceEnum::OLLAMA->value => $this->ollamaService->listLLM($aiService),
            default => null,
        };

        return $llms;
    }

    /**
     * Updates the list of available LLMs for Ollama if the service is configured.
     *
     * @return void
     */
    public function updateListLLMs(): void
    {
        $ollamaService = AiService::where('name', AiServiceEnum::OLLAMA->value)->first();

        if ($ollamaService && $this->checkApiServiceHasUrl($ollamaService)) {
            $this->ollamaService->updateListLLM();
        } else {
            return;
        }
    }

    /**
     * Checks if the AI service has a configured API URL for the current user.
     *
     * @param AiService $aiService
     * @param int|null $userId
     * @return bool
     */
    private function checkApiServiceHasUrl(AiService $aiService): bool
    {
        return match ($aiService->name) {
            AiServiceEnum::OLLAMA->value => $this->hasApiUrlConfigured($aiService->id),
            default => false,
        };
    }

    /**
     * Checks if the AI service with the given ID has an API URL configured for the specified user.
     *
     * @param int $aiServiceId
     * @param int $userId
     * @return bool
     */
    private function hasApiUrlConfigured(int $aiServiceId): bool
    {
        $userSetting = UserSettingAiService::where('user_id', auth()->id())
            ->where('ai_service_id', $aiServiceId)
            ->first();

        return $userSetting && $userSetting->url_api !== null;
    }
}
