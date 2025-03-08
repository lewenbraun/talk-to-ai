<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Message;
use App\Services\LLMService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateLLMAnswer implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $chatId;
    public Message $message;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $chatId, Message $message)
    {
        $this->chatId = $chatId;
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(LLMService $llmService)
    {
        $llmService->generateAnswer($this->chatId, $this->message);
    }
}
