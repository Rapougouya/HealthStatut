<?php

namespace App\Notifications;

use App\Models\Alert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $alert;

    /**
     * Create a new notification instance.
     */
    public function __construct(Alert $alert)
    {
        $this->alert = $alert;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $severityColors = [
            'critical' => '#dc2626',
            'high' => '#ea580c',
            'medium' => '#d97706',
            'low' => '#2563eb'
        ];

        $severityLabels = [
            'critical' => 'CRITIQUE',
            'high' => 'ÉLEVÉE',
            'medium' => 'MOYENNE',
            'low' => 'FAIBLE'
        ];

        $color = $severityColors[$this->alert->severity] ?? '#6b7280';
        $severityLabel = $severityLabels[$this->alert->severity] ?? 'INCONNUE';

        return (new MailMessage)
            ->subject("🚨 Alerte Médicale - Sévérité {$severityLabel}")
            ->greeting("Bonjour {$notifiable->prenom} {$notifiable->nom},")
            ->line("Une alerte médicale a été détectée pour votre profil.")
            ->line("**Type d'alerte :** {$this->alert->title}")
            ->line("**Message :** {$this->alert->message}")
            ->line("**Sévérité :** {$severityLabel}")
            ->line("**Date de détection :** {$this->alert->created_at->format('d/m/Y à H:i')}")
            ->when($this->alert->severity === 'critical', function ($message) {
                return $message->line('⚠️ **Cette alerte est CRITIQUE. Contactez immédiatement votre médecin.**');
            })
            ->action('Voir le détail de l\'alerte', url('/alerts/' . $this->alert->id))
            ->line('Si vous avez des questions, n\'hésitez pas à contacter votre équipe médicale.')
            ->salutation('Cordialement, L\'équipe MediSuivi');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'alert_id' => $this->alert->id,
            'title' => $this->alert->title,
            'message' => $this->alert->message,
            'severity' => $this->alert->severity,
            'type' => $this->alert->type,
            'created_at' => $this->alert->created_at,
        ];
    }
}