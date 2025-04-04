<?php

declare(strict_types=1);

namespace Tests\Feature;

use Mockery;
use App\Models\LLM;
use Tests\TestCase;
use App\Models\User;
use App\Models\AiService;
use App\Enums\AiServiceEnum;
use App\Services\AiManagerService;
use App\Models\UserSettingAiService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LLMControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private AiService $aiService;
    private string $fakeApiUrl = 'http://fake-ollama:11434';

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->aiService = AiService::where('name', AiServiceEnum::OLLAMA->value)->firstOrFail();

        UserSettingAiService::create([
            'user_id'       => $this->user->id,
            'ai_service_id' => $this->aiService->id,
            'url_api'       => $this->fakeApiUrl,
        ]);

        $this->actingAs($this->user);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testListByAiService(): void
    {
        $llms = LLM::factory()->count(2)->create([
            'ai_service_id' => $this->aiService->id,
        ]);

        $expected = $llms->toArray();

        $stub = Mockery::mock(...['overload:' . AiManagerService::class])->makePartial();
        $stub->shouldAllowMockingProtectedMethods()
             ->shouldReceive('checkApiServiceUrl')
             ->andReturnTrue();

        $stub->shouldReceive('redirectListLLM')
             ->once()
             ->andReturn($llms);

        $response = $this->getJson("/api/ai-service/llm/list/{$this->aiService->id}");
        $response->assertStatus(200);

        $response->assertJsonFragment([
            'id'            => $expected[0]['id'],
            'name'          => $expected[0]['name'],
            'ai_service_id' => $expected[0]['ai_service_id'],
        ]);
        $this->assertCount(count($expected), $response->json());
    }

    public function testAddLLM(): void
    {
        $llmName = 'test-model:latest';

        /** @var class-string<AiManagerService> $class */
        $stub = Mockery::mock(...['overload:' . AiManagerService::class])->makePartial();
        $stub->shouldAllowMockingProtectedMethods()
             ->shouldReceive('checkApiServiceUrl')
             ->andReturnTrue();

        $fakeLLM = LLM::factory()->create([
            'name'          => $llmName,
            'ai_service_id' => $this->aiService->id,
        ]);

        $stub->shouldReceive('redirectAddLLM')
             ->once()
             ->with($llmName)
             ->andReturn($fakeLLM);

        $response = $this->postJson('/api/ai-service/llm/add', [
            'llm_name'      => $llmName,
            'ai_service_id' => $this->aiService->id,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('llms', [
            'name'          => $llmName,
            'ai_service_id' => $this->aiService->id,
        ]);
        $response->assertJsonFragment(['name' => $llmName]);
    }

    public function testAddLLMValidationFails(): void
    {
        $response = $this->postJson('/api/ai-service/llm/add', [
            'ai_service_id' => $this->aiService->id,
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['llm_name']);
    }

    public function testDeleteLLM(): void
    {
        $llm = LLM::factory()->create([
            'ai_service_id' => $this->aiService->id,
        ]);

        $stub = Mockery::mock(...['overload:App\Services\AiManagerService'])->makePartial();
        $stub->shouldAllowMockingProtectedMethods()
             ->shouldReceive('checkApiServiceUrl')
             ->andReturnTrue();

        $stub->shouldReceive('redirectDeleteLLM')
             ->once()
             ->withArgs(fn ($arg): bool => $arg instanceof LLM && $arg->id === $llm->id)
             ->andReturnUsing(function ($llm): void {
                 $llm->delete();
             });

        $response = $this->postJson('/api/ai-service/llm/delete', [
            'llm_id'        => $llm->id,
            'ai_service_id' => $this->aiService->id,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('llms', ['id' => $llm->id]);
    }

    public function testDeleteLLMValidationFails(): void
    {
        $response = $this->postJson('/api/ai-service/llm/delete', [
            'llm_id'        => 999,
            'ai_service_id' => $this->aiService->id,
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['llm_id']);
    }
}
