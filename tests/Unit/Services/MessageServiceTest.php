<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Message;
use App\Services\MessageService;
use Illuminate\Database\Eloquent\Collection;
use PHPUnit\Framework\TestCase;

class MessageServiceTest extends TestCase
{
    public function test_format_message_for_chat_returns_formatted_messages(): void
    {
        $messages = new Collection([
            new Message(['content' => 'Hello!', 'role' => 'user']),
            new Message(['content' => 'Hi there!', 'role' => 'assistant']),
        ]);

        $result = MessageService::formatMessageForChat($messages);

        $expected = [
            ['content' => 'Hello!', 'role' => 'user'],
            ['content' => 'Hi there!', 'role' => 'assistant'],
        ];

        $this->assertEquals($expected, $result);
    }

    public function test_format_message_for_chat_handles_empty_collection(): void
    {
        $messages = new Collection();

        $this->assertSame([], MessageService::formatMessageForChat($messages));
    }
}
