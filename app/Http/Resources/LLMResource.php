<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\LLM;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin LLM
 */
class LLMResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'isLoaded' => $this->isLoaded,
            'user_id' => $this->user_id,
            'ai_service_id' => $this->ai_service_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
