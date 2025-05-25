<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SensorReadingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'capteur_id' => $this->capteur_id,
            'value' => $this->value,
            'timestamp' => $this->timestamp->format('Y-m-d H:i:s'),
            'raw_data' => $this->raw_data,
            'signal_strength' => $this->signal_strength,
            'battery_level' => $this->battery_level,
            'status_code' => $this->status_code,
            'connection_type' => $this->connection_type,
            'latency' => $this->latency,
            'is_within_thresholds' => $this->isWithinThresholds(),
            'has_good_signal' => $this->hasGoodSignal(),
            'has_stable_connection' => $this->hasStableConnection(),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}