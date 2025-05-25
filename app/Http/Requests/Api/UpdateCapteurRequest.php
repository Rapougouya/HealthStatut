<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCapteurRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->isAdmin() || auth()->user()->role->nom === 'technicien');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'ccapteur_id' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('sensors', 'ccapteur_id')->ignore($this->route('sensor'))
            ],
            'type' => 'sometimes|required|string|max:255',
            'model' => 'sometimes|required|string|max:255',
            'patient_id' => 'sometimes|nullable|exists:users,id',
            'location' => 'sometimes|nullable|string|max:255',
            'description' => 'sometimes|nullable|string|max:1000',
            'threshold_low' => 'sometimes|nullable|numeric',
            'threshold_high' => 'sometimes|nullable|numeric|gte:threshold_low',
            'alert_level' => 'sometimes|required|in:low,medium,high,critical',
            'interval' => 'sometimes|required|integer|min:1|max:3600',
            'active' => 'sometimes|boolean',
            'status' => 'sometimes|in:ready,active,error,inactive',
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'capteur_id.required' => 'L\'identifiant du capteur est obligatoire.',
            'capteur_id.unique' => 'Cet identifiant de capteur existe déjà.',
            'type.required' => 'Le type de capteur est obligatoire.',
            'model.required' => 'Le modèle du capteur est obligatoire.',
            'patient_id.exists' => 'Le patient sélectionné n\'existe pas.',
            'threshold_high.gte' => 'Le seuil haut doit être supérieur ou égal au seuil bas.',
            'alert_level.in' => 'Le niveau d\'alerte doit être : low, medium, high ou critical.',
            'interval.min' => 'L\'intervalle doit être d\'au moins 1 seconde.',
            'interval.max' => 'L\'intervalle ne peut pas dépasser 3600 secondes (1h).',
            'status.in' => 'Le statut doit être : ready, active, error ou inactive.',
        ];
    }
}