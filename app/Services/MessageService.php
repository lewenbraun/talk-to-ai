<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Message;
use Illuminate\Database\Eloquent\Collection;

class MessageService
{
    public static function formatMessageForChat(Collection $messages): array
    {
        $formattedMessages = $messages->map(fn(Message $message): array => [
            'content' => $message->content,
            'role' => $message->role,
        ])->toArray();

        return $formattedMessages;
    }
}
