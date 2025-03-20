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
use Illuminate\Support\Facades\Http;
use Exception;
use Illuminate\Support\Facades\App;

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
     * @param integer|null $userId Optional. The ID of the user. This is crucial for jobs queue
     * where the authenticated user might not be directly available.
     */
    public function __construct(AiService $selectedAiService, ?int $userId = null)
    {
        $this->selectedAiService = $selectedAiService;
        $apiUrl = $this->getApiUrlForAiService($selectedAiService->id, $userId);
        $this->checkApiServiceUrl($apiUrl);

        $this->ollamaService = new OllamaService($apiUrl);
    }

    public function redirectGenerateAnswer(Chat $chat): void
    {
        match ($this->selectedAiService->name) {
            AiServiceEnum::OLLAMA->value => $this->ollamaService->generateAnswer($chat),
        };
    }

    public function redirectAddLLM(string $modelName): ?LLM
    {
        $model = match ($this->selectedAiService->name) {
            AiServiceEnum::OLLAMA->value => $this->ollamaService->addLLM($modelName),
            default => null,
        };

        return $model;
    }

    public function redirectDeleteLLM(LLM $llm): void
    {
        match ($this->selectedAiService->name) {
            AiServiceEnum::OLLAMA->value => $this->ollamaService->deleteLLM($llm),
        };
    }

    public function redirectListLLM(): ?Collection
    {
        $this->updateListLLMs();

        $llms = match ($this->selectedAiService->name) {
            AiServiceEnum::OLLAMA->value => $this->ollamaService->listLLM(),
            default => null,
        };

        return $llms;
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
     * @param AiService $aiService
     * @throws Exception
     * @return bool Returns true if the URL is available, throws exception otherwise.
     */
    public function checkApiServiceUrl(string $apiUrl): bool
    {
        if ($apiUrl) {
            try {
                $response = Http::timeout(5)->get($apiUrl);

                if (!$response->successful()) {
                    throw new Exception(__('errors.api_error', [
                        'url' => $apiUrl,
                        'service' => $this->selectedAiService->name,
                        'status' => $response->status(),
                    ]));
                }
                return true;
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                throw new Exception(__('errors.connection_error', [
                    'url' => $apiUrl,
                    'service' => $this->selectedAiService->name,
                ]));
            }
        } else {
            throw new Exception(__('errors.url_not_configured', [
                'service' => $this->selectedAiService->name,
            ]));
        }
    }

    /**
     * Retrieves the API URL for the given AI service ID for the current user.
     *
     * @param integer $aiServiceId
     * @param integer|null $userId // userId is required for the jobs queue.
     * @return string|null
     */
    private function getApiUrlForAiService(int $aiServiceId, ?int $userId = null): ?string
    {
        $userId = $userId ?? auth()->id();

        if (!$userId) {
            return null;
        }

        $userSetting = UserSettingAiService::where('user_id', $userId)
            ->where('ai_service_id', $aiServiceId)
            ->first();

        return $userSetting ? $userSetting->url_api : null;
    }

}
