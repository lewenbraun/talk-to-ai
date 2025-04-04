<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ChatFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\BroadcastsEvents;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Chat extends Model
{
    /** @use HasFactory<ChatFactory> */
    use HasFactory;
    use BroadcastsEvents;

    protected $guarded = [];

    /**
     * @return HasMany<Message, $this>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<LLM, $this>
     */
    public function llm(): BelongsTo
    {
        return $this->belongsTo(LLM::class);
    }
}
