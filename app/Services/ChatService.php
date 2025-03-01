<?php

declare(strict_types=1);

namespace App\Services;

use Exception;
use App\Enums\Role;
use App\Models\Chat;

class ChatService
{
    public function sendMessage(string $message, Role $role, Chat $chat): bool
    {
        try {
            $message = $chat->messages->create([
                'chat_id' => $chat->id,
                'message' => $message,
                'role' => $role,
            ]);

        } catch (Exception $e) {
            return false;
        }

        return true;
    }
}
