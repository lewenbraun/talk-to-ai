<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Database\Factories\UserSettingAiServiceFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserSettingAiService extends Model
{
    /** @use HasFactory<UserSettingAiServiceFactory> */
    use HasFactory;
    protected $guarded = [];

    /**
     * @return BelongsTo<AiService, $this>
     */
    public function aiService(): BelongsTo
    {
        return $this->belongsTo(AiService::class);
    }
}
