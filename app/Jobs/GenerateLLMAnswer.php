<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Chat;
use App\Models\Message;
use Illuminate\Bus\Queueable;
use App\Services\AiManagerService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class GenerateLLMAnswer implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public Chat $chat;

    /**
     * Create a new job instance.
     *
     * @param Chat $chat
     */
    public function __construct(Chat $chat)
    {
        $this->chat = $chat;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $aiService = $this->chat->llm?->aiService;
        $userId = $this->chat->user?->id;

        if ($aiService === null || $userId === null) {
            throw new \Exception(__('errors.missing_connections'));
        }

        $aiManager = new AiManagerService($aiService, $userId);
        $aiManager->redirectGenerateAnswer($this->chat);
    }
}
