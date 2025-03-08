<?php

declare(strict_types=1);

namespace App\Services;

use App\Events\LLMAnswerGenerated;
use App\Models\Message;
use Lewenbraun\Ollama\Ollama;
use App\Services\MessageService;
use App\Events\LLMChunkGenerated;
use App\Respositories\MessageRepository;

class LLMService
{
    private $client;

    public function __construct()
    {
        $this->client = Ollama::client();
    }

    public function generateAnswer(int $chat_id)
    {
        $messages = MessageRepository::messagesByChat($chat_id, 20);
        $formattedMessages = MessageService::formatMessageForChat($messages);

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

        broadcast(new LLMAnswerGenerated($chat_id));

        Message::create([
            'chat_id' => $chat_id,
            'content' => $fullMessage,
            'role' => 'assistant',
        ]);
    }
}
