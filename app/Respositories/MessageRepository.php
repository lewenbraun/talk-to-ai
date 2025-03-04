<?php

declare(strict_types=1);

namespace App\Respositories;

use App\Models\Message;

class MessageRepository
{
    public static function messagesByChat(int $chat_id, int $count)
    {
        $messages = Message::where('chat_id', $chat_id)
            ->orderBy('created_at', 'asc')
            ->take($count)
            ->get();

        return $messages;
    }
}
