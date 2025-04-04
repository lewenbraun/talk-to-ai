<?php

declare(strict_types=1);

namespace App\Respositories;

use App\Models\Message;
use Illuminate\Database\Eloquent\Collection;

class MessageRepository
{
    /**
     * @param int $chat_id
     * @param int $count
     * @return Collection<int, Message>
     */
    public static function messagesByChat(int $chat_id, int $count): Collection
    {
        $messages = Message::where('chat_id', $chat_id)
            ->orderBy('created_at', 'asc')
            ->take($count)
            ->get();

        return $messages;
    }
}
