<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Chat;
use App\Enums\RoleEnum;
use App\Models\Message;

class ChatService
{
    public function createMessage(string $content, RoleEnum $role, Chat $chat): Message
    {
        $message = Message::create([
            'chat_id' => $chat->id,
            'content' => $content,
            'role' => $role->value,
        ]);

        return $message;
    }
}
