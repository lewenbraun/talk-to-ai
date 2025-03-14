<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\RoleEnum;
use App\Models\Chat;
use App\Models\Message;

class ChatService
{
    public function sendMessage(string $content, RoleEnum $role, Chat $chat): Message
    {
        $message = Message::create([
            'chat_id' => $chat->id,
            'content' => $content,
            'role' => $role->value,
        ]);

        return $message;
    }
}
