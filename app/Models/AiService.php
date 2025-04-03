<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\LLM;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AiService extends Model
{
    protected $guarded = [];

    /**
     * @return HasMany<LLM, $this>
     */
    public function llms(): HasMany
    {
        return $this->hasMany(LLM::class);
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function userSettings(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_setting_ai_services')
                    ->withPivot('api_key', 'url_api')
                    ->wherePivot('user_id', auth()->id());
    }

    public function getUrlApiAttribute(): ?string
    {
        return $this->userSettings->first()->pivot->url_api ?? null;
    }

    public function getApiKeyAttribute(): ?string
    {
        return $this->userSettings->first()->pivot->api_key ?? null;
    }
}
