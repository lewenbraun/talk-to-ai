<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use App\Models\LLM;
use App\Models\Chat;
use App\Models\AiService;
use App\Enums\AiServiceEnum;
use App\Models\UserSettingAiService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Exception;

class AiManagerService
{
    private AiService $selectedAiService;
    private OllamaService $ollamaService;

    /**
     * Initializes a new instance of the AiManagerService.
     *
     * This constructor sets the selected AI service and initializes the corresponding service client
     * (e.g., OllamaService) with the API URL for the current user.
     *
     * @param AiService $selectedAiService The AI service to be managed.
     * @param int|null $userId Optional. The ID of the user. This is crucial for jobs queue
     * where the authenticated user might not be directly available.
     *
     * @throws Exception if API URL is not configured or not reachable.
     */
    public function __construct(AiService $selectedAiService, ?int $userId = null)
    {
        $this->selectedAiService = $selectedAiService;
        $apiUrl = $this->getApiUrlForAiService($selectedAiService->id, $userId);
        $this->ollamaService = new OllamaService($apiUrl);
    }

    public function redirectGenerateAnswer(Chat $chat): void
    {
        match ($this->selectedAiService->name) {
            AiServiceEnum::OLLAMA->value => $this->ollamaService->generateAnswer($chat),
            default => throw new Exception(__('errors.unsupported_ai_service', ['service' => $this->selectedAiService->name])),
        };
    }

    public function redirectAddLLM(string $modelName): ?LLM
    {
        return match ($this->selectedAiService->name) {
            AiServiceEnum::OLLAMA->value => $this->ollamaService->addLLM($modelName),
            default => null,
        };
    }

    public function redirectDeleteLLM(LLM $llm): void
    {
        match ($this->selectedAiService->name) {
            AiServiceEnum::OLLAMA->value => $this->ollamaService->deleteLLM($llm),
            default => throw new Exception(__('errors.unsupported_ai_service', ['service' => $this->selectedAiService->name])),
        };
    }

    /**
     * @return Collection<int, LLM>|null
     */
    public function redirectListLLM(): ?Collection
    {
        $this->updateListLLMs();

        return match ($this->selectedAiService->name) {
            AiServiceEnum::OLLAMA->value => $this->ollamaService->listLLM(),
            default => null,
        };
    }

    /**
     * Updates the list of available LLMs for Ollama if the service is configured and available.
     *
     * @throws Exception
     * @return void
     */
    public function updateListLLMs(): void
    {
        $this->ollamaService->updateListLLM();
    }

    /**
     * Checks the availability of the API URL for a specific AI service.
     *
     * @param string $apiUrl
     * @throws Exception
     * @return bool Returns true if the URL is available, throws exception otherwise.
     */
    public function checkApiServiceUrl(string $apiUrl): bool
    {
        try {
            $response = Http::timeout(5)->get($apiUrl);

            if (!$response->successful()) {
                throw new Exception(__('exceptions.api_error', [
                    'url' => $apiUrl,
                    'service' => $this->selectedAiService->name,
                    'status' => $response->status(),
                ]));
            }

            return true;
        } catch (ConnectionException $e) {
            throw new Exception(__('exceptions.connection_error', [
                'url' => $apiUrl,
                'service' => $this->selectedAiService->name,
            ]), $e->getCode(), $e);
        }
    }

    /**
     * Retrieves the API URL for the given AI service ID for the current user.
     *
     * @param int $aiServiceId
     * @param int|null $userId // userId is required for the jobs queue.
     * @return string
     * @throws Exception
     */
    private function getApiUrlForAiService(int $aiServiceId, ?int $userId = null): string
    {
        $userId = $userId ?? auth()->id();

        if (!$userId) {
            throw new Exception(__('exceptions.no_user_id', [
                'service' => $this->selectedAiService->name,
            ]));
        }

        $userSetting = UserSettingAiService::where('user_id', $userId)
            ->where('ai_service_id', $aiServiceId)
            ->first();

        if ($userSetting && $userSetting->url_api) {
            $this->checkApiServiceUrl($userSetting->url_api);
        } else {
            throw new Exception(__('exceptions.url_not_configured', [
                'service' => $this->selectedAiService->name,
            ]));
        }

        return $userSetting->url_api;
    }
}
