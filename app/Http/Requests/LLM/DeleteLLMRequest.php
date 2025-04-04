<?php

declare(strict_types=1);

namespace App\Http\Requests\LLM;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class DeleteLLMRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'llm_id' => ['required', 'integer', 'exists:llms,id'],
            'ai_service_id' => ['required', 'integer', 'exists:ai_services,id'],
        ];
    }
}
