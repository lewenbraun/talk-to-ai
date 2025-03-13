<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\LLM;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiService extends Model
{
    protected $guarded = [];

    public function llms(): HasMany
    {
        return $this->hasMany(LLM::class);
    }
}
