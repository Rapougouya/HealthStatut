<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\AlertSetting;
use App\Models\SensorReading;
use App\Models\User;
use App\Notifications\AlertNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class AlertService
{
    /**
     * Analyser les données du capteur et créer des alertes si nécessaire
     */
    public function analyzeAndCreateAlerts(SensorReading $reading): void
    {
        $sensor = $reading->sensor;
        $patient = $sensor->patient;
        
        if (!$patient) {
            Log::warning("Capteur {$sensor->id} n'est assigné à aucun patient");
            return;
        }

        // Obtenir les paramètres d'alerte du patient
        $settings = AlertSetting::where('user_id', $patient->id)->first();
        
        if (!$settings) {
            // Utiliser les paramètres par défaut
            $settings = $this->getDefaultSettings();
        }

        $alerts = $this->checkThresholds($reading, $settings, $patient);
        
        foreach ($alerts as $alertData) {
            $this->createAlert($alertData, $patient);
        }
    }

    /**
     * Vérifier les seuils selon le type de capteur
     */
    private function checkThresholds(SensorReading $reading, AlertSetting $settings, User $patient): array
    {
        $alerts = [];
        $sensorType = $reading->sensor->type;
        $value = $reading->value;

        switch ($sensorType) {
            case 'heart_rate':
                if ($value < $settings->heart_rate_low) {
                    $alerts[] = [
                        'title' => 'Fréquence cardiaque faible',
                        'message' => "Fréquence cardiaque de {$value} bpm détectée (seuil: {$settings->heart_rate_low} bpm)",
                        'severity' => $settings->heart_rate_severity,
                        'type' => 'heart_rate_low',
                        'data' => ['value' => $value, 'threshold' => $settings->heart_rate_low]
                    ];
                } elseif ($value > $settings->heart_rate_high) {
                    $alerts[] = [
                        'title' => 'Fréquence cardiaque élevée',
                        'message' => "Fréquence cardiaque de {$value} bpm détectée (seuil: {$settings->heart_rate_high} bpm)",
                        'severity' => $settings->heart_rate_severity,
                        'type' => 'heart_rate_high',
                        'data' => ['value' => $value, 'threshold' => $settings->heart_rate_high]
                    ];
                }
                break;

            case 'spo2':
                if ($value < $settings->spo2_low) {
                    $alerts[] = [
                        'title' => 'Saturation en oxygène faible',
                        'message' => "SpO2 de {$value}% détectée (seuil: {$settings->spo2_low}%)",
                        'severity' => $settings->spo2_severity,
                        'type' => 'spo2_low',
                        'data' => ['value' => $value, 'threshold' => $settings->spo2_low]
                    ];
                }
                break;

            case 'temperature':
                if ($value < $settings->temp_low) {
                    $alerts[] = [
                        'title' => 'Température corporelle faible',
                        'message' => "Température de {$value}°C détectée (seuil: {$settings->temp_low}°C)",
                        'severity' => $settings->temp_severity,
                        'type' => 'temp_low',
                        'data' => ['value' => $value, 'threshold' => $settings->temp_low]
                    ];
                } elseif ($value > $settings->temp_high) {
                    $alerts[] = [
                        'title' => 'Température corporelle élevée',
                        'message' => "Température de {$value}°C détectée (seuil: {$settings->temp_high}°C)",
                        'severity' => $settings->temp_severity,
                        'type' => 'temp_high',
                        'data' => ['value' => $value, 'threshold' => $settings->temp_high]
                    ];
                }
                break;

            case 'blood_pressure':
                $values = explode('/', $value); // Format: "120/80"
                if (count($values) === 2) {
                    $systolic = (int)$values[0];
                    $diastolic = (int)$values[1];
                    
                    if ($systolic < $settings->bp_sys_low || $diastolic < $settings->bp_dia_low) {
                        $alerts[] = [
                            'title' => 'Tension artérielle faible',
                            'message' => "Tension de {$value} mmHg détectée",
                            'severity' => $settings->bp_severity,
                            'type' => 'bp_low',
                            'data' => ['value' => $value, 'systolic' => $systolic, 'diastolic' => $diastolic]
                        ];
                    } elseif ($systolic > $settings->bp_sys_high || $diastolic > $settings->bp_dia_high) {
                        $alerts[] = [
                            'title' => 'Tension artérielle élevée',
                            'message' => "Tension de {$value} mmHg détectée",
                            'severity' => $settings->bp_severity,
                            'type' => 'bp_high',
                            'data' => ['value' => $value, 'systolic' => $systolic, 'diastolic' => $diastolic]
                        ];
                    }
                }
                break;
        }

        return $alerts;
    }

    /**
     * Créer une alerte et envoyer les notifications
     */
    private function createAlert(array $alertData, User $patient): void
    {
        // Vérifier si une alerte similaire existe déjà récemment
        $existingAlert = Alert::where('patient_id', $patient->id)
            ->where('type', $alertData['type'])
            ->where('resolved', false)
            ->where('created_at', '>', now()->subMinutes(30))
            ->first();

        if ($existingAlert) {
            Log::info("Alerte similaire déjà existante pour le patient {$patient->id}");
            return;
        }

        $alert = Alert::create([
            'title' => $alertData['title'],
            'message' => $alertData['message'],
            'severity' => $alertData['severity'],
            'type' => $alertData['type'],
            'patient_id' => $patient->id,
            'data' => $alertData['data']
        ]);

        // Envoyer les notifications selon les préférences
        $this->sendNotifications($alert, $patient);

        Log::info("Alerte créée pour le patient {$patient->nom_complet}", [
            'alert_id' => $alert->id,
            'type' => $alertData['type'],
            'severity' => $alertData['severity']
        ]);
    }

    /**
     * Envoyer les notifications selon les préférences du patient
     */
    private function sendNotifications(Alert $alert, User $patient): void
    {
        $settings = AlertSetting::where('user_id', $patient->id)->first();
        
        if (!$settings) {
            $settings = $this->getDefaultSettings();
        }

        // Vérifier si on doit notifier seulement les alertes critiques
        if ($settings->notify_critical_only && $alert->severity !== 'critical') {
            return;
        }

        // Notification email
        if ($settings->notify_email && $patient->email) {
            $patient->notify(new AlertNotification($alert));
        }

        // Ici vous pourrez ajouter d'autres types de notifications (SMS, Push, etc.)
    }

    /**
     * Obtenir les paramètres par défaut
     */
    private function getDefaultSettings(): AlertSetting
    {
        return new AlertSetting([
            'heart_rate_low' => 60,
            'heart_rate_high' => 100,
            'heart_rate_severity' => 'high',
            'spo2_low' => 94,
            'spo2_severity' => 'critical',
            'temp_low' => 36.0,
            'temp_high' => 38.0,
            'temp_severity' => 'high',
            'bp_sys_low' => 90,
            'bp_sys_high' => 140,
            'bp_dia_low' => 60,
            'bp_dia_high' => 90,
            'bp_severity' => 'medium',
            'notify_email' => true,
            'notify_sms' => true,
            'notify_app' => true,
            'notify_critical_only' => false,
        ]);
    }

    /**
     * Créer une alerte manuelle
     */
    public function createManualAlert(array $data, User $patient): Alert
    {
        return Alert::create([
            'title' => $data['title'],
            'message' => $data['message'],
            'severity' => $data['severity'],
            'type' => 'manual',
            'patient_id' => $patient->id,
            'data' => $data['additional_data'] ?? []
        ]);
    }
}