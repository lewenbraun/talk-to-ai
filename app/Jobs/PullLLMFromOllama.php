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
    private string $ollamaApiUrl;

    /**
     * Create a new job instance.
     *
     * @param LLM $llm
     * @param string $ollamaApiUrl
     */
    public function __construct(LLM $llm, string $ollamaApiUrl)
    {
        $this->llm = $llm;
        $this->ollamaApiUrl = $ollamaApiUrl;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $ollamaService = new OllamaService($this->ollamaApiUrl);
        $ollamaService->pullLLM($this->llm);
    }
}
