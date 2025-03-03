<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Services\ChatService;
use App\Http\Resources\ChatResource;
use App\Http\Resources\MessageResource;

class ChatController extends Controller
{
    private ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
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

    public function sendMessageInNewChat(Request $request)
    {
        $content = $request->input('content');

        $chat = Chat::create([
            'user_id' => auth()->id(),
            'name' => 'New chat',
        ]);

        $message = $this->chatService->sendMessage($content, Role::USER, $chat);

        return [
            'chat' => new ChatResource($chat),
            'message' => new MessageResource($message)
        ];
    }

    public function sendMessageInExistingChat(Request $request)
    {
        $content = $request->input('content');

        $chat = Chat::findOrFail($request->chat_id);

        $message = $this->chatService->sendMessage($content, Role::USER, $chat);

        return [
            'message' => new MessageResource($message)
        ];
    }
}
