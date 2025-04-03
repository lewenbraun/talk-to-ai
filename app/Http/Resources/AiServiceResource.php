<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\AiService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin AiService
 */
class AiServiceResource extends JsonResource
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
            'url_api' => $this->url_api,
            'api_key' => $this->api_key,
            'llms' => LLMResource::collection($this->whenLoaded('llms')),
        ];
    }
}
