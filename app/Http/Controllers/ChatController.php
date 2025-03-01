<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\Chat;
use Illuminate\Http\Request;
use App\Services\ChatService;

class ChatController extends Controller
{
    private ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function userSendMessage(Request $request)
    {
        $content = $request->input('content');
        $chat_id = $request->input('chat_id');

        $chat = Chat::findOrFail('id', $chat_id);

        $status = $this->chatService->sendMessage($content, Role::USER, $chat);

        return $status;
    }

    public function sendMessageInNewChat(Request $request)
    {
        $content = $request->input('content');

        $chat = Chat::create([
            'user_id' => auth()->id(),
            'name' => '',
        ]);

        $status = $this->chatService->sendMessage($content, Role::USER, $chat);

        return $status;
    }

    public function sendMessageInExistingChat(Request $request)
    {
        $content = $request->input('content');

        $chat = Chat::create([
            'user_id' => auth()->id(),
            'name' => '',
        ]);

        $status = $this->chatService->sendMessage($content, Role::USER, $chat);

        return $status;
    }
}
