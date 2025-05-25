<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'nom_complet' => $this->nom_complet,
            'date_naissance' => $this->date_naissance->format('Y-m-d'),
            'age' => $this->date_naissance->age,
            'sexe' => $this->sexe,
            'sexe_label' => $this->sexe === 'M' ? 'Masculin' : 'Féminin',
            'adresse' => $this->adresse,
            'telephone' => $this->telephone,
            'email' => $this->email,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'notes_count' => $this->whenCounted('notes'),
        ];
    }
}