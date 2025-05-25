<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AlertResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'message' => $this->message,
            'severity' => $this->severity,
            'severity_label' => $this->getSeverityLabel(),
            'type' => $this->type,
            'patient_id' => $this->patient_id,
            'patient' => new PatientResource($this->whenLoaded('patient')),
            'resolved' => $this->resolved,
            'resolved_at' => $this->resolved_at?->format('Y-m-d H:i:s'),
            'resolved_by' => $this->resolved_by,
            'resolved_by_user' => new UserResource($this->whenLoaded('resolvedBy')),
            'data' => $this->data,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'time_since_creation' => $this->created_at->diffForHumans(),
        ];
    }

    /**
     * Get severity label.
     */
    private function getSeverityLabel(): string
    {
        return match($this->severity) {
            'low' => 'Faible',
            'medium' => 'Moyen',
            'high' => 'Élevé',
            'critical' => 'Critique',
            default => 'Inconnu'
        };
    }
}