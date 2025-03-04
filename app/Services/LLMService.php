<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Message;
use Lewenbraun\Ollama\Ollama;
use App\Services\MessageService;
use App\Respositories\MessageRepository;

class LLMService
{
    private $client;

    public function __construct()
    {
        $this->client = Ollama::client('ollama5:11434');
    }

    public function generateAnswer(int $chat_id, Message $message)
    {
        $messages = MessageRepository::messagesByChat($chat_id, 20);
        $formattedMessages = MessageService::formatMessageForChat($messages);

        $response = $this->client->chatCompletion()->create([
            'model' => 'deepseek-r1:1.5b',
            'messages' => $formattedMessages,
        ]);
    }
}
