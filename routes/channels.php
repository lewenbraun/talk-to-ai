<?php

declare(strict_types=1);

use App\Models\Chat;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{chatId}', function (User $user, int $chatId) {
    return (int) $user->id === (int) Chat::findOrFail($chatId)->user_id;
});
