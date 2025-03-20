<?php

declare(strict_types=1);

namespace App\Http\Requests\AiService;

use Illuminate\Foundation\Http\FormRequest;

class SetApiKeyUserSettingAiServiceRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'ai_service_id' => 'required|integer|exists:ai_services,id',
            'api_key' => 'required|string',
        ];
    }
}
