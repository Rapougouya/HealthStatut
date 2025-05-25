<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePatientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->isAdmin() || auth()->user()->role->nom === 'medecin');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'nom' => 'sometimes|required|string|max:255',
            'prenom' => 'sometimes|required|string|max:255',
            'date_naissance' => 'sometimes|required|date|before:today',
            'sexe' => 'sometimes|required|in:M,F',
            'adresse' => 'sometimes|required|string|max:500',
            'telephone' => 'sometimes|required|string|max:20|regex:/^[0-9+\-\s\(\)]+$/',
            'email' => [
                'sometimes',
                'nullable',
                'email',
                'max:255',
                Rule::unique('patients', 'email')->ignore($this->route('patient'))
            ],
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'date_naissance.required' => 'La date de naissance est obligatoire.',
            'date_naissance.before' => 'La date de naissance doit être antérieure à aujourd\'hui.',
            'sexe.required' => 'Le sexe est obligatoire.',
            'sexe.in' => 'Le sexe doit être M ou F.',
            'adresse.required' => 'L\'adresse est obligatoire.',
            'telephone.required' => 'Le téléphone est obligatoire.',
            'telephone.regex' => 'Le format du téléphone n\'est pas valide.',
            'email.email' => 'L\'email doit être une adresse valide.',
            'email.unique' => 'Cette adresse email existe déjà.',
        ];
    }
}