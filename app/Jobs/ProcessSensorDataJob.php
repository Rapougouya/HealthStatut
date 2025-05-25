<?php

namespace App\Jobs;

use App\Models\SensorReading;
use App\Models\Alert;
use App\Models\AlertSetting;
use App\Services\AlertService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessSensorDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $sensorReading;

    /**
     * Create a new job instance.
     */
    public function __construct(SensorReading $sensorReading)
    {
        $this->sensorReading = $sensorReading;
    }

    /**
     * Execute the job.
     */
    public function handle(AlertService $alertService): void
    {
        try {
            // Analyser les données du capteur et générer des alertes si nécessaire
            $alertService->analyzeAndCreateAlerts($this->sensorReading);
            
            Log::info("Sensor data processed successfully", [
                'sensor_reading_id' => $this->sensorReading->id,
                'sensor_id' => $this->sensorReading->sensor_id,
                'value' => $this->sensorReading->value
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to process sensor data", [
                'sensor_reading_id' => $this->sensorReading->id,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }
}