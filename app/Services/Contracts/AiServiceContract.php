<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\Chat;

interface AiServiceContract
{
    /**
     * Generates a response from the language model.
     *
     * @param Chat $chat
     * @return void
     */
    public function generateAnswer(Chat $chat): void;
}
