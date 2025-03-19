<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSettingAiService extends Model
{
    protected $guarded = [];

    public function aiService(): BelongsTo
    {
        return $this->belongsTo(AiService::class);
    }
}
