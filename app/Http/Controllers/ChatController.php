<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\Chat;
use App\Models\Message;
use App\Services\LLMService;
use Illuminate\Http\Request;
use App\Services\ChatService;
use App\Jobs\GenerateLLMAnswer;
use App\Http\Resources\ChatResource;
use App\Http\Resources\MessageResource;

class ChatController extends Controller
{
    private ChatService $chatService;
    private LLMService $llmService;

    public function __construct(ChatService $chatService, LLMService $llmService)
    {
        $this->chatService = $chatService;
        $this->llmService = $llmService;
    }

    public function chatList()
    {
        $chats = Chat::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        return $chats;
    }

    public function chatMessages(Chat $chat)
    {
        $messages = Message::where('chat_id', $chat->id)
            ->orderBy('created_at', 'asc')
            ->take(20)
            ->get();

        return $messages;
    }

    public function createChat()
    {
        $chat = Chat::create([
            'user_id' => auth()->id(),
            'name' => 'New chat',
        ]);

        return [
            'chat' => new ChatResource($chat),
        ];
    }

    public function sendMessageInExistingChat(Request $request)
    {
        $content = $request->input('content');

        $chat = Chat::findOrFail($request->chat_id);

        $message = $this->chatService->sendMessage($content, Role::USER, $chat);

        GenerateLLMAnswer::dispatch($chat->id, $message);

        return [
            'message' => new MessageResource($message)
        ];
    }
}
