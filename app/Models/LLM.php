<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\AiService;
use Database\Factories\LLMFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LLM extends Model
{
    /** @use HasFactory<LLMFactory> */
    use HasFactory;

    protected $table = 'llms';
    protected $guarded = [];

    /**
     * @return BelongsTo<AiService, $this>
     */
    public function aiService(): BelongsTo
    {
        return $this->belongsTo(AiService::class);
    }

    /**
     * @return HasMany<Chat, $this>
     */
    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class);
    }
}
