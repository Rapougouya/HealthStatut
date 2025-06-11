@extends('layouts.app')

@section('title', 'Détails de l\'Alerte - Système de Monitoring Médical')

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --alert-critical: #dc2626;
            --alert-high: #ea580c;
            --alert-medium: #d97706;
            --alert-low: #059669;
            --bg-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .alert-details-container {
            background: var(--bg-gradient);
            min-height: 100vh;
            padding: 20px;
        }

        .back-button {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(15px);
            border: none;
            border-radius: 10px;
            padding: 12px 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #2d3748;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .back-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            background: white;
        }

        .alert-details-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .alert-details-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .alert-title-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .alert-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: white;
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
        }

        .alert-icon.critical { background: var(--alert-critical); }
        .alert-icon.high { background: var(--alert-high); }
        .alert-icon.medium { background: var(--alert-medium); }
        .alert-icon.low { background: var(--alert-low); }

        .alert-details-title h1 {
            color: #2d3748;
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
        }

        .alert-status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .alert-status-badge.critical {
            background: rgba(220, 38, 38, 0.1);
            color: var(--alert-critical);
            border: 2px solid var(--alert-critical);
        }

        .alert-status-badge.high {
            background: rgba(234, 88, 12, 0.1);
            color: var(--alert-high);
            border: 2px solid var(--alert-high);
        }

        .alert-status-badge.medium {
            background: rgba(217, 119, 6, 0.1);
            color: var(--alert-medium);
            border: 2px solid var(--alert-medium);
        }

        .alert-status-badge.low {
            background: rgba(5, 150, 105, 0.1);
            color: var(--alert-low);
            border: 2px solid var(--alert-low);
        }

        .alert-status-badge.resolved {
            background: rgba(34, 197, 94, 0.1);
            color: #22c55e;
            border: 2px solid #22c55e;
        }

        .alert-details-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 25px;
            margin-bottom: 25px;
        }

        .alert-main-info {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .alert-patient-info {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .info-group {
            margin-bottom: 20px;
        }

        .info-label {
            font-size: 0.9rem;
            font-weight: 600;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-value {
            font-size: 1.1rem;
            color: #2d3748;
            font-weight: 500;
        }

        .alert-message {
            background: rgba(248, 250, 252, 0.8);
            padding: 20px;
            border-radius: 12px;
            border-left: 4px solid #cbd5e0;
            margin: 20px 0;
        }

        .alert-message h4 {
            margin: 0 0 10px 0;
            color: #2d3748;
            font-weight: 600;
        }

        .alert-message p {
            margin: 0;
            color: #4a5568;
            line-height: 1.6;
        }

        .patient-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .patient-name {
            font-size: 1.3rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 10px;
        }

        .patient-details {
            list-style: none;
            padding: 0;
        }

        .patient-details li {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 0;
            border-bottom: 1px solid rgba(226, 232, 240, 0.5);
        }

        .patient-details li:last-child {
            border-bottom: none;
        }

        .patient-details i {
            width: 20px;
            color: #718096;
        }

        .alert-actions {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 20px;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            border: none;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            color: white;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.8);
            color: #2d3748;
            border: 2px solid rgba(220, 38, 38, 0.2);
        }

        .btn-success {
            background: linear-gradient(135deg, #059669, #047857);
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #b91c1c, #991b1b);
        }

        .btn-secondary:hover {
            background: #dc2626;
            color: white;
            border-color: #dc2626;
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #047857, #065f46);
        }

        .alert-timeline {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 15px;
            padding: 30px;
            margin-top: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .timeline-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid rgba(226, 232, 240, 0.5);
        }

        .timeline-item:last-child {
            border-bottom: none;
        }

        .timeline-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .timeline-content {
            flex: 1;
        }

        .timeline-time {
            font-size: 0.8rem;
            color: #718096;
            margin-bottom: 5px;
        }

        .timeline-text {
            color: #2d3748;
            line-height: 1.5;
        }

        @media (max-width: 768px) {
            .alert-details-container { padding: 15px; }
            .alert-details-grid { grid-template-columns: 1fr; }
            .alert-details-title { flex-direction: column; gap: 15px; }
            .alert-title-left { flex-direction: column; text-align: center; }
            .alert-actions { justify-content: center; }
        }
    </style>
@endsection

@section('content')
<div class="alert-details-container">
    <!-- Bouton retour -->
    <a href="{{ route('alerts.index') }}" class="back-button">
        <i class="fas fa-arrow-left"></i>
        Retour aux alertes
    </a>

    <!-- En-tête de l'alerte -->
    <div class="alert-details-header">
        <div class="alert-details-title">
            <div class="alert-title-left">
                <div class="alert-icon {{ $alert->severity ?? 'medium' }}">
                    @switch($alert->severity ?? 'medium')
                        @case('critical')
                            <i class="fas fa-exclamation-circle"></i>
                            @break
                        @case('high')
                            <i class="fas fa-exclamation"></i>
                            @break
                        @case('medium')
                            <i class="fas fa-triangle-exclamation"></i>
                            @break
                        @default
                            <i class="fas fa-info-circle"></i>
                    @endswitch
                </div>
                <div>
                    <h1>Alerte #{{ $alert->id ?? 'ALT001' }}</h1>
                    <p style="margin: 0; color: #718096; font-size: 0.9rem;">
                        <i class="fas fa-clock"></i>
                        Créée {{ \Carbon\Carbon::parse($alert->created_at ?? now())->diffForHumans() }}
                    </p>
                </div>
            </div>
            <div>
                <span class="alert-status-badge {{ ($alert->resolved ?? false) ? 'resolved' : ($alert->severity ?? 'medium') }}">
                    @if($alert->resolved ?? false)
                        <i class="fas fa-check-circle"></i> Résolue
                    @else
                        @switch($alert->severity ?? 'medium')
                            @case('critical')
                                <i class="fas fa-exclamation-circle"></i> Critique
                                @break
                            @case('high')
                                <i class="fas fa-exclamation"></i> Élevée
                                @break
                            @case('medium')
                                <i class="fas fa-triangle-exclamation"></i> Moyenne
                                @break
                            @default
                                <i class="fas fa-info-circle"></i> Faible
                        @endswitch
                    @endif
                </span>
            </div>
        </div>
    </div>

    <!-- Grille des détails -->
    <div class="alert-details-grid">
        <!-- Informations principales de l'alerte -->
        <div class="alert-main-info">
            <h3 style="margin-top: 0; color: #2d3748; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-info-circle"></i>
                Détails de l'alerte
            </h3>

            <div class="info-group">
                <div class="info-label">
                    <i class="fas fa-hashtag"></i>
                    ID de l'alerte
                </div>
                <div class="info-value">{{ $alert->id ?? 'ALT001' }}</div>
            </div>

            <div class="info-group">
                <div class="info-label">
                    <i class="fas fa-tag"></i>
                    Type d'alerte
                </div>
                <div class="info-value">
                    @switch($alert->type ?? 'heartrate')
                        @case('heartrate')
                            <i class="fas fa-heartbeat" style="color: #dc2626;"></i> Fréquence cardiaque
                            @break
                        @case('spo2')
                            <i class="fas fa-lungs" style="color: #2563eb;"></i> Saturation O₂
                            @break
                        @case('temperature')
                            <i class="fas fa-thermometer-half" style="color: #ea580c;"></i> Température
                            @break
                        @case('pressure')
                            <i class="fas fa-tachometer-alt" style="color: #7c3aed;"></i> Pression artérielle
                            @break
                        @default
                            <i class="fas fa-stethoscope"></i> {{ ucfirst($alert->type ?? 'Autre') }}
                    @endswitch
                </div>
            </div>

            <div class="info-group">
                <div class="info-label">
                    <i class="fas fa-calendar-alt"></i>
                    Date de création
                </div>
                <div class="info-value">{{ \Carbon\Carbon::parse($alert->created_at ?? now())->format('d/m/Y à H:i:s') }}</div>
            </div>

            @if($alert->resolved ?? false)
                <div class="info-group">
                    <div class="info-label">
                        <i class="fas fa-check-circle"></i>
                        Date de résolution
                    </div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($alert->resolved_at ?? now())->format('d/m/Y à H:i:s') }}</div>
                </div>

                <div class="info-group">
                    <div class="info-label">
                        <i class="fas fa-user-md"></i>
                        Résolue par
                    </div>
                    <div class="info-value">{{ $alert->resolvedBy->name ?? 'Dr. Ahmed' }}</div>
                </div>
            @endif

            <!-- Message de l'alerte -->
            <div class="alert-message">
                <h4>{{ $alert->title ?? 'Alerte de fréquence cardiaque élevée' }}</h4>
                <p>{{ $alert->message ?? 'Fréquence cardiaque détectée à 145 bpm, dépassant le seuil critique de 130 bpm. Surveillance immédiate recommandée.' }}</p>
            </div>
        </div>

        <!-- Informations du patient -->
        <div class="alert-patient-info">
            <h3 style="margin-top: 0; color: #2d3748; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-user"></i>
                Patient concerné
            </h3>

            <div style="text-align: center; margin-bottom: 20px;">
                <div class="patient-avatar">
                    {{ substr($alert->patient->prenom ?? 'M', 0, 1) }}{{ substr($alert->patient->nom ?? 'R', 0, 1) }}
                </div>
                <div class="patient-name">{{ $alert->patient->nom_complet ?? 'Mehdi Rais' }}</div>
            </div>

            <ul class="patient-details">
                <li>
                    <i class="fas fa-id-card"></i>
                    <span><strong>ID:</strong> {{ $alert->patient->id ?? '001' }}</span>
                </li>
                <li>
                    <i class="fas fa-birthday-cake"></i>
                    <span><strong>Âge:</strong> {{ $alert->patient->age ?? '45' }} ans</span>
                </li>
                <li>
                    <i class="fas fa-phone"></i>
                    <span><strong>Téléphone:</strong> {{ $alert->patient->telephone ?? '+212 6 12 34 56 78' }}</span>
                </li>
                <li>
                    <i class="fas fa-envelope"></i>
                    <span><strong>Email:</strong> {{ $alert->patient->email ?? 'mehdi.rais@email.com' }}</span>
                </li>
                <li>
                    <i class="fas fa-map-marker-alt"></i>
                    <span><strong>Adresse:</strong> {{ $alert->patient->adresse ?? 'Casablanca, Maroc' }}</span>
                </li>
                <li>
                    <i class="fas fa-hospital"></i>
                    <span><strong>Service:</strong> {{ $alert->patient->service->nom ?? 'Cardiologie' }}</span>
                </li>
            </ul>

            <div style="margin-top: 20px;">
                <a href="{{ route('patients.show', $alert->patient->id ?? 1) }}" class="btn btn-secondary" style="width: 100%; justify-content: center;">
                    <i class="fas fa-external-link-alt"></i>
                    Voir le dossier patient
                </a>
            </div>
        </div>
    </div>

    <!-- Actions disponibles -->
    @if(!($alert->resolved ?? false))
        <div class="alert-actions">
            <button class="btn btn-success" onclick="resolveAlert({{ $alert->id ?? 1 }})">
                <i class="fas fa-check-circle"></i>
                Marquer comme résolue
            </button>
            <button class="btn btn-primary" onclick="contactPatient({{ $alert->patient->id ?? 1 }})">
                <i class="fas fa-phone"></i>
                Contacter le patient
            </button>
            <a href="{{ route('patients.show', $alert->patient->id ?? 1) }}" class="btn btn-secondary">
                <i class="fas fa-user-md"></i>
                Consulter le dossier
            </a>
        </div>
    @endif

    <!-- Timeline des événements -->
    <div class="alert-timeline">
        <h3 style="margin-top: 0; color: #2d3748; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-history"></i>
            Historique de l'alerte
        </h3>

        <div class="timeline-item">
            <div class="timeline-icon" style="background: #fbbf24; color: white;">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="timeline-content">
                <div class="timeline-time">{{ \Carbon\Carbon::parse($alert->created_at ?? now())->format('d/m/Y à H:i:s') }}</div>
                <div class="timeline-text">
                    <strong>Alerte créée</strong><br>
                    {{ $alert->title ?? 'Alerte de fréquence cardiaque élevée' }} détectée pour {{ $alert->patient->nom_complet ?? 'Mehdi Rais' }}
                </div>
            </div>
        </div>

        @if($alert->acknowledged_at ?? false)
            <div class="timeline-item">
                <div class="timeline-icon" style="background: #3b82f6; color: white;">
                    <i class="fas fa-eye"></i>
                </div>
                <div class="timeline-content">
                    <div class="timeline-time">{{ \Carbon\Carbon::parse($alert->acknowledged_at ?? now())->format('d/m/Y à H:i:s') }}</div>
                    <div class="timeline-text">
                        <strong>Alerte prise en charge</strong><br>
                        Vue par {{ $alert->acknowledgedBy->name ?? 'Dr. Ahmed' }}
                    </div>
                </div>
            </div>
        @endif

        @if($alert->resolved ?? false)
            <div class="timeline-item">
                <div class="timeline-icon" style="background: #10b981; color: white;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="timeline-content">
                    <div class="timeline-time">{{ \Carbon\Carbon::parse($alert->resolved_at ?? now())->format('d/m/Y à H:i:s') }}</div>
                    <div class="timeline-text">
                        <strong>Alerte résolue</strong><br>
                        Résolue par {{ $alert->resolvedBy->name ?? 'Dr. Ahmed' }}
                        @if($alert->resolution_notes ?? false)
                            <br><em>Note: {{ $alert->resolution_notes }}</em>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    function resolveAlert(alertId) {
        if (confirm('Êtes-vous sûr de vouloir marquer cette alerte comme résolue ?')) {
            fetch(`/alerts/${alertId}/resolve`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Erreur lors de la résolution de l\'alerte');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de la résolution de l\'alerte');
            });
        }
    }

    function contactPatient(patientId) {
        if (confirm('Voulez-vous contacter ce patient ?')) {
            // Ici vous pouvez ajouter la logique pour contacter le patient
            alert('Fonctionnalité de contact en cours de développement');
        }
    }

    // Animation d'entrée
    document.addEventListener('DOMContentLoaded', function() {
        const elements = document.querySelectorAll('.alert-details-header, .alert-main-info, .alert-patient-info, .alert-actions, .alert-timeline');
        elements.forEach((element, index) => {
            setTimeout(() => {
                element.style.opacity = '0';
                element.style.transform = 'translateY(30px)';
                element.style.transition = 'all 0.6s ease';
                
                setTimeout(() => {
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                }, 100);
            }, index * 100);
        });
    });
</script>
@endsection