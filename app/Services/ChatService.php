<?php

declare(strict_types=1);

namespace App\Services;

use Exception;
use App\Enums\Role;
use App\Models\Chat;
use App\Models\Message;

class ChatService
{
    public function sendMessage(string $content, Role $role, Chat $chat): Message
    {
        $message = Message::create([
            'chat_id' => $chat->id,
            'content' => $content,
            'role' => $role->value,
        ]);

        return $message;
    }
}
