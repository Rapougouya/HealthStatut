<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email' => $this->email,
            'service_id' => $this->service_id,
            'role_id' => $this->role_id,
            'role' => $this->whenLoaded('role', function() {
                return [
                    'id' => $this->role->id,
                    'nom' => $this->role->nom,
                    'description' => $this->role->description,
                ];
            }),
            'service' => $this->whenLoaded('service', function() {
                return [
                    'id' => $this->service->id,
                    'name' => $this->service->name,
                    'description' => $this->service->description,
                ];
            }),
            'statut' => $this->statut,
            'derniere_connexion' => $this->derniere_connexion?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}