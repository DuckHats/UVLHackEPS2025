<?php

namespace App\Http\Requests;

use App\Constants\AppConstants;
use Illuminate\Foundation\Http\FormRequest;

class AnalyzeProfileRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'prompt' => [
                'required',
                'string',
                'min:' . AppConstants::MIN_PROMPT_LENGTH,
                'max:2000'
            ],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'prompt.required' => 'The Maesters require your scroll to proceed.',
            'prompt.min' => 'Your scroll must contain at least :min characters.',
            'prompt.max' => 'Your scroll is too long. Please keep it under :max characters.',
        ];
    }

    /**
     * Get custom attribute names for error messages.
     */
    public function attributes(): array
    {
        return [
            'prompt' => 'user description',
        ];
    }
}
