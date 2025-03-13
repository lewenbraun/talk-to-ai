<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\LLM;
use App\Models\Message;
use App\Models\AiService;
use App\Enums\AiServiceEnum;
use Lewenbraun\Ollama\Ollama;
use App\Jobs\PullLLMFromOllama;
use App\Services\MessageService;
use App\Events\LLMChunkGenerated;
use App\Events\LLMAnswerGenerated;
use App\Respositories\MessageRepository;
use App\Services\Contracts\AiServiceContract;
use Illuminate\Database\Eloquent\Collection;

class OllamaService implements AiServiceContract
{
    private $client;

    public function __construct()
    {
        $this->client = Ollama::client();
    }

    public function generateAnswer(int $chat_id): void
    {
        $formattedMessages = $this->prepareMessagesForLLM($chat_id);
        $fullMessage = $this->processLLMStream($chat_id, $formattedMessages);
        $this->saveLLMAnswer($chat_id, $fullMessage);
    }

    private function prepareMessagesForLLM(int $chat_id): array
    {
        $messages = MessageRepository::messagesByChat($chat_id, 20);
        return MessageService::formatMessageForChat($messages);
    }

    private function processLLMStream(int $chat_id, array $formattedMessages): string
    {
        $chatStream = $this->client->chatCompletion()->create([
            'model' => 'deepseek-r1:1.5b',
            'messages' => $formattedMessages,
            'stream' => true,
        ]);

        $fullMessage = '';

        foreach ($chatStream as $chunk) {
            $contentChunk = $chunk->message->content ?? '';
            if (!empty($contentChunk)) {
                broadcast(new LLMChunkGenerated($chat_id, $contentChunk));
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

    public function addLLM(int $aiServiceId, string $llmName): LLM
    {
        $addedModel = LLM::create([
            'ai_service_id' => $aiServiceId,
            'name' => $llmName,
            'isLoaded' => false,
        ]);

        PullLLMFromOllama::dispatch($addedModel);

        return $addedModel;
    }

    public function pullLLM(LLM $llm): void
    {
        $response = $this->client->models()->pull([
            'model' => $llm->name,
        ]);

        if ($response->status === 'success') {
            $llm->update(['isLoaded' => true]);
        } else {
            $llm->delete();
        }
    }

    public function deleteLLM(LLM $llm): void
    {
        $this->client->models()->delete([
            'model' => $llm->name,
        ]);

        $llm->delete();
    }

    public function listLLM(AiService $aiService): Collection
    {
        $this->updateListLLM();

        $listLLM = LLM::where('ai_service_id', $aiService->id)->get();

        return $listLLM;
    }

    public function updateListLLM(): void
    {
        $ollamaIdInDb = AiService::where('name', AiServiceEnum::OLLAMA->value)->first()->id;
        $ollamaModelNames = collect($this->client->models()->list()->models)->pluck('name')->toArray();
        $dbModelNames = LLM::where('ai_service_id', $ollamaIdInDb)->pluck('name')->toArray();

        $diff = $this->getModelDiff($ollamaModelNames, $dbModelNames);

        if (!empty($diff['newModels'])) {
            $this->addNewModels($diff['newModels'], $ollamaIdInDb);
        }

        if (!empty($diff['modelslsToUpdateIsLoaded'])) {
            $this->updateLoadedModels($diff['modelslsToUpdateIsLoaded']);
        }
    }

    private function updateLoadedModels(array $modelslsToUpdateIsLoaded): void
    {
        foreach ($modelslsToUpdateIsLoaded as $model) {
            $model->isLoaded = true;
            $model->save();
        }
    }

    private function getModelDiff(array $ollamaModelNames, array $dbModelNames): array
    {
        $newModelNames = array_diff($ollamaModelNames, $dbModelNames);
        $modelsToUpdateIsLoaded = [];

        $dbLlmModels = LLM::with('aiService')
            ->where('ai_service_id', AiService::where('name', AiServiceEnum::OLLAMA->value)->first()->id)
            ->get();

        foreach ($dbLlmModels as $dbModel) {
            if (in_array($dbModel->name, $ollamaModelNames) && !$dbModel->isLoaded) {
                $modelsToUpdateIsLoaded[] = $dbModel;
            }
        }

        return [
            'newModels' => $newModelNames,
            'modelsToUpdateIsLoaded' => $modelsToUpdateIsLoaded,
        ];
    }

    private function addNewModels(array $newModels, int $ollamaIdInDb): void
    {
        foreach ($newModels as $newModelName) {
            LLM::create([
                'ai_service_id' => $ollamaIdInDb,
                'name' => $newModelName,
                'isLoaded' => true,
            ]);
        }
    }


}
