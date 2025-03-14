<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\LLM;
use Illuminate\Bus\Queueable;
use App\Services\OllamaService;
use App\Services\AiManagerService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PullLLMFromOllama implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public LLM $llm;

    /**
     * Create a new job instance.
     *
     * @param LLM $llm
     */
    public function __construct(LLM $llm)
    {
        $this->llm = $llm;
    }

    /**
     * Execute the job.
     *
     * @param OllamaService $ollamaService
     * @return void
     */
    public function handle(OllamaService $ollamaService): void
    {
        $ollamaService->pullLLM($this->llm);
    }
}
