<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\LLM;
use Tests\TestCase;
use App\Models\Chat;
use App\Models\User;
use App\Enums\RoleEnum;
use App\Models\Message;
use App\Jobs\GenerateLLMAnswer;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChatControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
        Event::fake();
    }

    public function testList(): void
    {
        $llm = LLM::factory()->create();

        Chat::factory()->count(25)->create([
            'user_id' => $this->user->id,
            'llm_id' => $llm->id,
        ]);

        $response = $this->getJson('/api/chat/list');

        $response->assertStatus(200);

        $expectedChats = Chat::where('user_id', $this->user->id)
            ->with('llm')
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        $response->assertJsonCount(20);

        $responseIds = collect($response->json())->pluck('id');
        $expectedIds = $expectedChats->pluck('id');
        $this->assertEquals($expectedIds->toArray(), $responseIds->toArray());
    }

    public function testChatMessages(): void
    {
        $chat = Chat::factory()->create(['user_id' => $this->user->id]);

        Message::factory()->count(25)->create(['chat_id' => $chat->id]);

        $response = $this->getJson("/api/chat/messages/{$chat->id}");

        $response->assertStatus(200);

        $expectedMessages = Message::where('chat_id', $chat->id)
            ->orderBy('created_at', 'asc')
            ->take(20)
            ->get();

        $response->assertJsonCount(20);

        $responseIds = collect($response->json())->pluck('id');
        $expectedIds = $expectedMessages->pluck('id');
        $this->assertEquals($expectedIds->toArray(), $responseIds->toArray());
    }

    public function testCreateChat(): void
    {
        $llm = LLM::factory()->create();

        $response = $this->postJson('/api/chat/create', ['llm_id' => $llm->id]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('chats', [
            'user_id' => $this->user->id,
            'llm_id' => $llm->id,
            'name' => 'New chat',
        ]);

        $response->assertJsonFragment([
            'user_id' => $this->user->id,
            'name' => 'New chat',
        ]);
    }

    public function testSendMessage(): void
    {
        Queue::fake();

        $chat = Chat::factory()->create(['user_id' => $this->user->id]);

        $content = 'Hi';
        $response = $this->postJson('/api/chat/send-message', [
            'chat_id' => $chat->id,
            'content' => $content,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('messages', [
            'chat_id' => $chat->id,
            'content' => $content,
            'role' => RoleEnum::USER->value,
        ]);

        Queue::assertPushed(GenerateLLMAnswer::class, fn ($job): bool => $job->chat->id === $chat->id);

        $response->assertJsonFragment([
            'content' => $content,
            'role' => RoleEnum::USER->value,
        ]);
    }
}
