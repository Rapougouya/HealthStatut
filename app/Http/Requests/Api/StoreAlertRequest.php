<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreAlertRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'severity' => 'required|in:low,medium,high,critical',
            'type' => 'required|string|max:100',
            'patient_id' => 'nullable|exists:users,id',
            'data' => 'nullable|array',
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Le titre de l\'alerte est obligatoire.',
            'message.required' => 'Le message de l\'alerte est obligatoire.',
            'severity.required' => 'La sévérité de l\'alerte est obligatoire.',
            'severity.in' => 'La sévérité doit être : low, medium, high ou critical.',
            'type.required' => 'Le type d\'alerte est obligatoire.',
            'patient_id.exists' => 'Le patient sélectionné n\'existe pas.',
        ];
    }
}