<?php

namespace Tests\Unit\Services;

use Mockery;
use Exception;
use App\Models\LLM;
use Tests\TestCase;
use App\Models\Chat;
use App\Models\User;
use App\Models\AiService;
use App\Enums\AiServiceEnum;
use App\Jobs\PullLLMFromOllama;
use PHPUnit\Metadata\CoversClass;
use App\Services\AiManagerService;
use App\Models\UserSettingAiService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;

#[RunClassInSeparateProcess]
#[PreserveGlobalState(false)]
#[CoversClass(AiManagerService::class)]
class AiManagerServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function createBaseTestData(
        string $serviceName = AiServiceEnum::OLLAMA->value,
        string $apiUrl = 'http://default-ai-service.test',
        bool $createService = true
    ): array {
        $user = User::factory()->create();
        $aiService = null;

        if ($createService) {
            $aiService = AiService::firstOrCreate(
                ['name' => $serviceName],
                ['id' => (AiService::max('id') ?? 0) + 1]
            );
        } else {
            $aiService = AiService::where('name', $serviceName)->firstOrFail();
        }

        $userSetting = UserSettingAiService::factory()->create([
            'user_id' => $user->id,
            'ai_service_id' => $aiService->id,
            'url_api' => $apiUrl,
        ]);

        Http::fake([
            $apiUrl => Http::response('Ai Servcie is running', 200),
            $apiUrl . '/delete' => Http::response(null, 200),
            $apiUrl . '/pull' => Http::response(['status' => 'success'], 200),
            $apiUrl . '/*' => Http::response(['status' => 'fallback ok'], 200),
        ]);

        return ['user' => $user, 'aiService' => $aiService, 'userSetting' => $userSetting, 'apiUrl' => $apiUrl];
    }

    public function test_constructor_throws_exception_when_url_not_configured(): void
    {
        $aiService = AiService::findOrFail(1);
        if (!$aiService) {
            $this->fail('AiService with ID 1 not found.');
        }
        $user = User::factory()->create();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(__('errors.url_not_configured', ['service' => $aiService->name]));

        new AiManagerService($aiService, $user->id);
    }

    public function test_constructor_throws_exception_when_url_not_reachable(): void
    {
        $aiService = AiService::findOrFail(1);
        if (!$aiService) {
            $this->fail('AiService with ID 1 not found.');
        }
        $user = User::factory()->create();
        $apiUrl = 'http://unreachable-ai-service.test';
        $serviceName = $aiService->name;

        UserSettingAiService::factory()->create([
            'user_id' => $user->id,
            'ai_service_id' => $aiService->id,
            'url_api' => $apiUrl,
        ]);

        Http::fake([
            $apiUrl . '*' => Http::response(null, 500),
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(__('errors.api_error', ['url' => $apiUrl, 'service' => $serviceName, 'status' => 500]));

        new AiManagerService($aiService, $user->id);
    }

    public function test_constructor_throws_exception_when_no_user_id_and_auth_not_set(): void
    {
        $aiService = AiService::findOrFail(1);
        if (!$aiService) {
            $this->fail('AiService with ID 1 not found.');
        }

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(__('errors.no_user_id', ['service' => $aiService->name]));

        new AiManagerService($aiService);
    }

    public function test_check_api_service_url_returns_true_when_url_is_reachable(): void
    {
        ['user' => $user, 'aiService' => $aiService, 'apiUrl' => $apiUrl] = $this->createBaseTestData(createService: false);

        $service = new AiManagerService($aiService, $user->id);
        $result = $service->checkApiServiceUrl($apiUrl);
        $this->assertTrue($result);
    }

    public function test_check_api_service_url_throws_exception_on_connection_error(): void
    {
        $apiUrl = 'http://connection-error-check.test';
        ['user' => $user, 'aiService' => $aiService] = $this->createBaseTestData(apiUrl: $apiUrl, createService: false);
        $serviceName = $aiService->name;

        $service = new AiManagerService($aiService, $user->id);

        Http::fake([
            $apiUrl . '*' => fn ($request) => throw new ConnectionException("Connection timed out"),
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(__('errors.connection_error', ['url' => $apiUrl, 'service' => $serviceName]));

        $service->checkApiServiceUrl($apiUrl);
    }

    public function test_redirect_generate_answer_throws_exception_for_unsupported_service(): void
    {
        Event::fake();
        $unsupportedServiceName = 'UnsupportedService';
        ['user' => $user, 'aiService' => $aiService] = $this->createBaseTestData(serviceName: $unsupportedServiceName, createService: true);
        $llm = LLM::factory()->create(['ai_service_id' => $aiService->id, 'user_id' => $user->id, 'name' => 'unsupported-model']);
        $chat = Chat::factory()->create(['user_id' => $user->id, 'llm_id' => $llm->id]);

        $service = new AiManagerService($aiService, $user->id);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(__('errors.unsupported_ai_service', ['service' => $unsupportedServiceName]));

        $service->redirectGenerateAnswer($chat);
    }

    public function test_redirect_add_llm_calls_ollama_service_and_returns_llm(): void
    {
        Queue::fake();
        ['user' => $user, 'aiService' => $aiService, 'apiUrl' => $apiUrl] = $this->createBaseTestData(createService: false);
        $modelName = 'test-llama-model-add';

        $this->actingAs($user);
        $service = new AiManagerService($aiService, $user->id);
        $result = $service->redirectAddLLM($modelName);

        $this->assertInstanceOf(LLM::class, $result);
        $this->assertEquals($modelName, $result->name);
        $this->assertEquals($user->id, $result->user_id);
        $this->assertEquals($aiService->id, $result->ai_service_id);
        $this->assertFalse($result->isLoaded);

        $this->assertDatabaseHas('llms', ['name' => $modelName, 'user_id' => $user->id]);

        Queue::assertPushed(PullLLMFromOllama::class, fn ($job): bool => $job->llm->id === $result->id);
    }

    public function test_redirect_add_llm_returns_null_for_unsupported_service(): void
    {
        Queue::fake();
        ['user' => $user, 'aiService' => $aiService] = $this->createBaseTestData(serviceName: 'UnsupportedServiceAdd', createService: true);

        $this->actingAs($user);
        $service = new AiManagerService($aiService, $user->id);
        $result = $service->redirectAddLLM('test-model');

        $this->assertNull($result);
        Queue::assertNothingPushed();
    }

    public function test_redirect_delete_llm_throws_exception_for_unsupported_service(): void
    {
        $unsupportedServiceName = 'UnsupportedDelete';
        ['user' => $user, 'aiService' => $aiService] = $this->createBaseTestData(serviceName: $unsupportedServiceName, createService: true);
        $llm = LLM::factory()->create([
            'ai_service_id' => $aiService->id, 'user_id' => $user->id, 'name' => 'unsupported-model-delete',
        ]);

        $this->actingAs($user);
        $service = new AiManagerService($aiService, $user->id);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(__('errors.unsupported_ai_service', ['service' => $unsupportedServiceName]));

        $service->redirectDeleteLLM($llm);
    }
}
