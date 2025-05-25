<?php

namespace App\Listeners;

use App\Events\AlertCreated;
use App\Notifications\AlertNotification;
use App\Models\AlertSetting;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class HandleAlertCreated implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AlertCreated $event): void
    {
        $alert = $event->alert;
        $patient = $alert->patient;

        if (!$patient) {
            Log::warning("Alerte {$alert->id} sans patient associé");
            return;
        }

        // Obtenir les paramètres de notification du patient
        $settings = AlertSetting::where('user_id', $patient->id)->first();
        
        // Notifier le patient
        $this->notifyPatient($alert, $patient, $settings);
        
        // Notifier l'équipe médicale
        $this->notifyMedicalTeam($alert, $patient);
        
        // Log pour le suivi
        Log::info("Notifications envoyées pour l'alerte {$alert->id}", [
            'patient_id' => $patient->id,
            'alert_type' => $alert->type,
            'severity' => $alert->severity
        ]);
    }

    /**
     * Notifier le patient
     */
    private function notifyPatient($alert, $patient, $settings): void
    {
        if (!$settings) {
            // Utiliser les paramètres par défaut
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

        // Ici vous pourrez ajouter d'autres notifications (SMS, Push, etc.)
    }

    /**
     * Notifier l'équipe médicale
     */
    private function notifyMedicalTeam($alert, $patient): void
    {
        // Notifier les médecins et infirmiers du service du patient
        $medicalStaff = User::whereHas('role', function($query) {
                $query->whereIn('nom', ['medecin', 'infirmier']);
            })
            ->where('service_id', $patient->service_id)
            ->where('actif', true)
            ->get();

        // Pour les alertes critiques, notifier aussi les administrateurs
        if ($alert->severity === 'critical') {
            $admins = User::whereHas('role', function($query) {
                $query->where('nom', 'admin');
            })
            ->where('actif', true)
            ->get();
            
            $medicalStaff = $medicalStaff->merge($admins);
        }

        // Envoyer les notifications
        Notification::send($medicalStaff, new AlertNotification($alert));
    }

    /**
     * Obtenir les paramètres par défaut
     */
    private function getDefaultSettings()
    {
        return (object) [
            'notify_email' => true,
            'notify_sms' => true,
            'notify_app' => true,
            'notify_critical_only' => false,
        ];
    }
}