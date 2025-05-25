<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CapteurResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'capteur_id' => $this->capteur_id,
            'type' => $this->type,
            'model' => $this->model,
            'patient_id' => $this->patient_id,
            'patient' => new PatientResource($this->whenLoaded('patient')),
            'location' => $this->location,
            'description' => $this->description,
            'threshold_low' => $this->threshold_low,
            'threshold_high' => $this->threshold_high,
            'alert_level' => $this->alert_level,
            'alert_level_label' => $this->getAlertLevelLabel(),
            'interval' => $this->interval,
            'active' => $this->active,
            'last_reading' => $this->last_reading,
            'last_reading_at' => $this->last_reading_at?->format('Y-m-d H:i:s'),
            'status' => $this->status,
            'status_label' => $this->getStatusLabel(),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'readings_count' => $this->whenCounted('readings'),
        ];
    }

    /**
     * Get alert level label.
     */
    private function getAlertLevelLabel(): string
    {
        return match($this->alert_level) {
            'low' => 'Faible',
            'medium' => 'Moyen',
            'high' => 'Élevé',
            'critical' => 'Critique',
            default => 'Inconnu'
        };
    }

    /**
     * Get status label.
     */
    private function getStatusLabel(): string
    {
        return match($this->status) {
            'ready' => 'Prêt',
            'active' => 'Actif',
            'error' => 'Erreur',
            'inactive' => 'Inactif',
            default => 'Inconnu'
        };
    }
}