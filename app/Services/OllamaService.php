<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\LLM;
use App\Models\Chat;
use App\Models\Message;
use App\Models\AiService;
use App\Enums\AiServiceEnum;
use Lewenbraun\Ollama\Ollama;
use App\Jobs\PullLLMFromOllama;
use App\Services\MessageService;
use App\Events\LLMChunkGenerated;
use App\Events\LLMAnswerGenerated;
use App\Respositories\MessageRepository;
use Illuminate\Database\Eloquent\Collection;
use App\Services\Contracts\AiServiceContract;

class OllamaService implements AiServiceContract
{
    private Ollama $client;
    private string $ollama_url_api;
    private int $ollama_id;

    public function __construct(string $ollama_url_api)
    {
        $this->client = Ollama::client($ollama_url_api);
        $this->ollama_url_api = $ollama_url_api;
        $this->ollama_id = $this->getIdOllamaInDb();
    }

    public function generateAnswer(Chat $chat): void
    {
        $messages = MessageRepository::messagesByChat($chat->id, 20);
        $formattedMessages = MessageService::formatMessageForChat($messages);
        $fullMessage = $this->processLLMStream($chat, $formattedMessages);
        $this->saveLLMAnswer($chat->id, $fullMessage);
    }

    /**
     * @param array<mixed> $formattedMessages
     */
    private function processLLMStream(Chat $chat, array $formattedMessages): string
    {
        if (!$chat->llm || !isset($chat->llm->name)) {
            throw new \UnexpectedValueException(__('errors.missing_llm_name'));
        }

        $chatStream = $this->client->chatCompletion()->create([
            'model' => $chat->llm->name,
            'messages' => $formattedMessages,
            'stream' => true,
        ]);

        $fullMessage = '';

        if (!is_iterable($chatStream)) {
            throw new \UnexpectedValueException(__('errors.expected_iterable_chat_stream'));
        }

        foreach ($chatStream as $chunk) {
            $contentChunk = $chunk->message->content ?? '';
            if (!empty($contentChunk)) {
                broadcast(new LLMChunkGenerated($chat->id, $contentChunk));
                $fullMessage .= $contentChunk;
            }
        }

        return $fullMessage;
    }

    private function saveLLMAnswer(int $chat_id, string $fullMessage): void
    {
        broadcast(new LLMAnswerGenerated($chat_id));

        Message::create([
            'chat_id' => $chat_id,
            'content' => $fullMessage,
            'role' => 'assistant',
        ]);
    }

    public function addLLM(string $llmName): LLM
    {
        $addedModel = LLM::create([
            'user_id' => auth()->id(),
            'ai_service_id' => $this->ollama_id,
            'name' => $llmName,
            'isLoaded' => false,
        ]);

        PullLLMFromOllama::dispatch($addedModel, $this->ollama_url_api);

        return $addedModel;
    }

    public function deleteLLM(LLM $llm): void
    {
        $this->client->models()->delete([
            'model' => $llm->name,
        ]);

        $llm->delete();
    }

    public function pullLLM(LLM $llm): void
    {
        $response = $this->client->models()->pull([
            'model' => $llm->name,
        ]);

        if (isset($response->status) && $response->status === 'success') {
            $llm->update(['isLoaded' => true]);
        } else {
            $llm->delete();
        }
    }

    /**
     * @return Collection<int, LLM>
     */
    public function listLLM(): Collection
    {
        $this->updateListLLM();

        $listLLM = LLM::where('user_id', auth()->id())
            ->where('ai_service_id', $this->ollama_id)
            ->get();

        return $listLLM;
    }

    public function updateListLLM(): void
    {
        /** @var array<string> $dbModelNames */
        $dbModelNames = LLM::where('user_id', auth()->id())
            ->where('ai_service_id', $this->ollama_id)
            ->pluck('name')
            ->toArray();

        /** @var array<string> $ollamaModelNames */
        $ollamaModelNames = collect($this->client->models()->list()->models)
            ->pluck('name')
            ->toArray();

        $diff = $this->getModelDiff($ollamaModelNames, $dbModelNames);

        if (!empty($diff['newModels'])) {
            $this->addNewModels($diff['newModels']);
        }

        if (!empty($diff['modelsToUpdateIsLoaded'])) {
            $this->updateLoadedModels($diff['modelsToUpdateIsLoaded']);
        }
    }

    /**
     * @param array<LLM> $modelsForUpdateIsLoaded
     */
    private function updateLoadedModels(array $modelsForUpdateIsLoaded): void
    {
        foreach ($modelsForUpdateIsLoaded as $model) {
            $model->isLoaded = true;
            $model->save();
        }
    }

    /**
     * @param array<string> $ollamaModelNames
     * @param array<string> $dbModelNames
     * @return array{newModels: array<string>, modelsToUpdateIsLoaded: array<LLM>}
     */
    private function getModelDiff(array $ollamaModelNames, array $dbModelNames): array
    {
        $newModelNames = array_diff($ollamaModelNames, $dbModelNames);
        $modelsToUpdateIsLoaded = [];

        /** @var Collection<int, LLM> $dbLlmModels */
        $dbLlmModels = LLM::with('aiService')
            ->where('user_id', auth()->id())
            ->where('ai_service_id', $this->ollama_id)
            ->get();

        foreach ($dbLlmModels as $dbModel) {
            if (in_array($dbModel->name, $ollamaModelNames, true) && !$dbModel->isLoaded) {
                $modelsToUpdateIsLoaded[] = $dbModel;
            }
        }

        return [
            'newModels' => $newModelNames,
            'modelsToUpdateIsLoaded' => $modelsToUpdateIsLoaded,
        ];
    }

    /**
     * @param array<string> $newModels
     */
    private function addNewModels(array $newModels): void
    {
        foreach ($newModels as $newModelName) {
            LLM::create([
                'user_id' => auth()->id(),
                'ai_service_id' => $this->ollama_id,
                'name' => $newModelName,
                'isLoaded' => true,
            ]);
        }
    }

    private function getIdOllamaInDb(): int
    {
        return AiService::where('name', AiServiceEnum::OLLAMA->value)->firstOrFail()->id;
    }
}
