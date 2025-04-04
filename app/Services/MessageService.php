<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Message;
use Illuminate\Database\Eloquent\Collection;

class MessageService
{
    /**
    * Formats messages for chat.
     *
     * @param Collection<int, Message> $messages
     * @return array<int, array{content: string, role: string}>
     */
    public static function formatMessageForChat(Collection $messages): array
    {
        $formattedMessages = $messages->map(
            fn (Message $message): array => [
                'content' => $message->content,
                'role' => $message->role,
            ]
        )->toArray();

        return $formattedMessages;
    }
}
