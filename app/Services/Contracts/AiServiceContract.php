<?php

declare(strict_types=1);

namespace App\Services\Contracts;

interface AiServiceContract
{
    /**
     * Generates a response from the language model.
     *
     * @param int $chat_id
     * @return void
     */
    public function generateAnswer(int $chat_id);
}
