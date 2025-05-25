<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreCapteurRequest extends FormRequest
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
            'capteur_id' => 'required|string|max:255|unique:sensors,capteur_id',
            'type' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'patient_id' => 'nullable|exists:users,id',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'threshold_low' => 'nullable|numeric',
            'threshold_high' => 'nullable|numeric|gte:threshold_low',
            'alert_level' => 'required|in:low,medium,high,critical',
            'interval' => 'required|integer|min:1|max:3600',
            'active' => 'boolean',
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'sensor_id.required' => 'L\'identifiant du capteur est obligatoire.',
            'sensor_id.unique' => 'Cet identifiant de capteur existe déjà.',
            'type.required' => 'Le type de capteur est obligatoire.',
            'model.required' => 'Le modèle du capteur est obligatoire.',
            'patient_id.exists' => 'Le patient sélectionné n\'existe pas.',
            'threshold_high.gte' => 'Le seuil haut doit être supérieur ou égal au seuil bas.',
            'alert_level.in' => 'Le niveau d\'alerte doit être : low, medium, high ou critical.',
            'interval.min' => 'L\'intervalle doit être d\'au moins 1 seconde.',
            'interval.max' => 'L\'intervalle ne peut pas dépasser 3600 secondes (1h).',
        ];
    }
}