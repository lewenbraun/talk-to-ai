<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Enums\RoleEnum;
use App\Models\Message;
use App\Services\ChatService;
use App\Jobs\GenerateLLMAnswer;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\ChatResource;
use App\Http\Resources\MessageResource;
use App\Http\Requests\Chat\CreateChatRequest;
use App\Http\Requests\Chat\SendMessageInExistingChatRequest;
use Throwable;

class ChatController extends Controller
{
    private ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function list(): JsonResponse
    {
        $chats = Chat::where('user_id', auth()->id())
            ->with('llm')
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        return response()->json($chats);
    }

    public function chatMessages(Chat $chat): JsonResponse
    {
        $messages = Message::where('chat_id', $chat->id)
            ->orderBy('created_at', 'asc')
            ->take(20)
            ->get();

        return response()->json($messages);
    }

    public function createChat(CreateChatRequest $request): JsonResponse
    {
        $chat = Chat::create([
            'user_id' => auth()->id(),
            'llm_id' => $request->integer('llm_id'),
            'name' => 'New chat',
        ]);

        return response()->json(new ChatResource($chat));
    }

    public function sendMessage(SendMessageInExistingChatRequest $request): JsonResponse
    {
        $content = $request->input('content');
        $chatId = $request->integer('chat_id');

        try {
            $chat = Chat::findOrFail($chatId);
            $message = $this->chatService->createMessage($content, RoleEnum::USER, $chat);
            GenerateLLMAnswer::dispatch($chat);

            return response()->json(new MessageResource($message));
        } catch (Throwable $e) {
            return response()->json(['message' => __('errors.message_send_failed')], 500);
        }
    }
}
