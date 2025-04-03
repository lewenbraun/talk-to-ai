<?php

declare(strict_types=1);

use App\Models\Chat;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{chatId}', fn(User $user, int $chatId): bool => (int) $user->id === (int) Chat::findOrFail($chatId)->user_id);
