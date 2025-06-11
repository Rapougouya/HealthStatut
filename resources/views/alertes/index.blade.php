@extends('layouts.app')

@section('title', 'Gestion des Alertes - Système de Monitoring Médical')

@section('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --alert-critical: #dc2626;
            --alert-high: #ea580c;
            --alert-medium: #d97706;
            --alert-low: #059669;
            --bg-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .alerts-container {
            background: var(--bg-gradient);
            min-height: 100vh;
            padding: 20px;
        }

        .alerts-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .alerts-title {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
        }

        .alerts-title h1 {
            color: #2d3748;
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
            background: linear-gradient(135deg, #dc2626, #ea580c);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .title-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: white;
            background: linear-gradient(135deg, #dc2626, #ea580c);
            box-shadow: 0 8px 25px rgba(220, 38, 38, 0.3);
        }

        .alerts-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(15px);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-left: 4px solid;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }

        .stat-card.critical { border-left-color: var(--alert-critical); }
        .stat-card.high { border-left-color: var(--alert-high); }
        .stat-card.medium { border-left-color: var(--alert-medium); }
        .stat-card.low { border-left-color: var(--alert-low); }

        .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }

        .stat-icon.critical { background: var(--alert-critical); }
        .stat-icon.high { background: var(--alert-high); }
        .stat-icon.medium { background: var(--alert-medium); }
        .stat-icon.low { background: var(--alert-low); }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #2d3748;
        }

        .stat-label {
            font-size: 0.9rem;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .filters-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .filter-group label {
            font-weight: 600;
            color: #2d3748;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filter-input {
            padding: 12px 15px;
            border: 2px solid rgba(220, 38, 38, 0.1);
            border-radius: 10px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
        }

        .filter-input:focus {
            outline: none;
            border-color: #dc2626;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
            background: white;
        }

        .alerts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .alert-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .alert-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
        }

        .alert-card.critical::before { background: var(--alert-critical); }
        .alert-card.high::before { background: var(--alert-high); }
        .alert-card.medium::before { background: var(--alert-medium); }
        .alert-card.low::before { background: var(--alert-low); }

        .alert-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .alert-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .alert-severity {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .alert-severity.critical {
            background: rgba(220, 38, 38, 0.1);
            color: var(--alert-critical);
        }

        .alert-severity.high {
            background: rgba(234, 88, 12, 0.1);
            color: var(--alert-high);
        }

        .alert-severity.medium {
            background: rgba(217, 119, 6, 0.1);
            color: var(--alert-medium);
        }

        .alert-severity.low {
            background: rgba(5, 150, 105, 0.1);
            color: var(--alert-low);
        }

        .alert-time {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.8rem;
            color: #718096;
        }

        .alert-patient {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 15px;
        }

        .patient-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .patient-info h4 {
            margin: 0;
            font-weight: 600;
            color: #2d3748;
        }

        .patient-info p {
            margin: 0;
            font-size: 0.8rem;
            color: #718096;
        }

        .alert-message {
            background: rgba(248, 250, 252, 0.8);
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 15px;
            border-left: 3px solid #cbd5e0;
        }

        .alert-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            border: none;
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

        @media (max-width: 768px) {
            .alerts-container { padding: 15px; }
            .alerts-header { padding: 20px; }
            .alerts-title h1 { font-size: 2rem; }
            .alerts-grid { grid-template-columns: 1fr; }
            .filters-grid { grid-template-columns: 1fr; }
        }
        
        .resolved-indicator {
            background: rgba(16, 185, 129, 0.1) !important;
            color: #10b981 !important;
            cursor: default !important;
        }

        .resolved-indicator:hover {
            transform: none !important;
            box-shadow: none !important;
        }
    </style>
@endsection

@section('content')
<div class="alerts-container">
    <!-- En-tête des alertes -->
    <div class="alerts-header">
        <div class="alerts-title">
            <div class="title-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h1>Gestion des Alertes</h1>
        </div>

        <!-- Statistiques des alertes -->
        <div class="alerts-stats">
            <div class="stat-card critical">
                <div class="stat-header">
                    <div class="stat-icon critical">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div class="stat-value">{{ $stats['critical'] ?? '3' }}</div>
                </div>
                <div class="stat-label">Alertes Critiques</div>
            </div>

            <div class="stat-card high">
                <div class="stat-header">
                    <div class="stat-icon high">
                        <i class="fas fa-exclamation"></i>
                    </div>
                    <div class="stat-value">{{ $stats['high'] ?? '8' }}</div>
                </div>
                <div class="stat-label">Alertes Élevées</div>
            </div>

            <div class="stat-card medium">
                <div class="stat-header">
                    <div class="stat-icon medium">
                        <i class="fas fa-triangle-exclamation"></i>
                    </div>
                    <div class="stat-value">{{ $stats['medium'] ?? '12' }}</div>
                </div>
                <div class="stat-label">Alertes Moyennes</div>
            </div>

            <div class="stat-card low">
                <div class="stat-header">
                    <div class="stat-icon low">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div class="stat-value">{{ $stats['low'] ?? '5' }}</div>
                </div>
                <div class="stat-label">Alertes Faibles</div>
            </div>
        </div>
    </div>

    <!-- Section de filtrage -->
    <div class="filters-section">
        <form action="{{ route('alertes.index') }}" method="GET">
            <div class="filters-grid">
                <div class="filter-group">
                    <label for="severity">
                        <i class="fas fa-exclamation-triangle"></i>
                        Sévérité
                    </label>
                    <select name="severity" id="severity" class="filter-input">
                        <option value="">Toutes les sévérités</option>
                        <option value="critical" {{ request('severity') == 'critical' ? 'selected' : '' }}>Critique</option>
                        <option value="high" {{ request('severity') == 'high' ? 'selected' : '' }}>Élevée</option>
                        <option value="medium" {{ request('severity') == 'medium' ? 'selected' : '' }}>Moyenne</option>
                        <option value="low" {{ request('severity') == 'low' ? 'selected' : '' }}>Faible</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="type">
                        <i class="fas fa-tag"></i>
                        Type d'alerte
                    </label>
                    <select name="type" id="type" class="filter-input">
                        <option value="">Tous les types</option>
                        <option value="heartrate" {{ request('type') == 'heartrate' ? 'selected' : '' }}>Fréquence cardiaque</option>
                        <option value="spo2" {{ request('type') == 'spo2' ? 'selected' : '' }}>Saturation O₂</option>
                        <option value="temperature" {{ request('type') == 'temperature' ? 'selected' : '' }}>Température</option>
                        <option value="pressure" {{ request('type') == 'pressure' ? 'selected' : '' }}>Pression artérielle</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="patient_id">
                        <i class="fas fa-user"></i>
                        Patient
                    </label>
                    <select name="patient_id" id="patient_id" class="filter-input">
                        <option value="">Tous les patients</option>
                        @foreach($patients ?? [] as $patient)
                            <option value="{{ $patient->id }}" {{ request('patient_id') == $patient->id ? 'selected' : '' }}>
                                {{ $patient->nom_complet }}
                            </option>
                        @endforeach
                        @if(empty($patients))
                            <option value="1" {{ request('patient_id') == 1 ? 'selected' : '' }}>Mehdi Rais</option>
                            <option value="2" {{ request('patient_id') == 2 ? 'selected' : '' }}>Sara Tazi</option>
                            <option value="3" {{ request('patient_id') == 3 ? 'selected' : '' }}>Karim Alaoui</option>
                        @endif
                    </select>
                </div>

                <div class="filter-group">
                    <label for="search">
                        <i class="fas fa-search"></i>
                        Rechercher
                    </label>
                    <input type="text" name="search" id="search" class="filter-input" 
                           placeholder="Rechercher dans les alertes..." 
                           value="{{ request('search') }}">
                </div>
            </div>

            <div style="display: flex; gap: 15px; margin-top: 20px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                    Filtrer
                </button>
                <a href="{{ route('alertes.index') }}" class="btn btn-secondary">
                    <i class="fas fa-refresh"></i>
                    Réinitialiser
                </a>
            </div>
        </form>
    </div>

    <!-- Grille des alertes -->
    <div class="alerts-grid">
        @forelse($alerts ?? [] as $alert)
            <div class="alert-card {{ $alert->severity ?? 'medium' }}" data-alert-id="{{ $alert->id ?? 1 }}">
                <div class="alert-header">
                    <span class="alert-severity {{ $alert->severity ?? 'medium' }}">
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
                    </span>
                    <div class="alert-time">
                        <i class="fas fa-clock"></i>
                        {{ \Carbon\Carbon::parse($alert->created_at ?? now())->diffForHumans() }}
                    </div>
                </div>

                <div class="alert-patient">
                    <div class="patient-avatar">
                        {{ substr($alert->patient->prenom ?? 'M', 0, 1) }}{{ substr($alert->patient->nom ?? 'R', 0, 1) }}
                    </div>
                    <div class="patient-info">
                        <h4>{{ $alert->patient->nom_complet ?? 'Mehdi Rais' }}</h4>
                        <p><i class="fas fa-id-card"></i> ID: {{ $alert->patient->id ?? '001' }}</p>
                    </div>
                </div>

                <div class="alert-message">
                    <strong>{{ $alert->title ?? 'Alerte de fréquence cardiaque' }}</strong>
                    <p>{{ $alert->message ?? 'Fréquence cardiaque élevée détectée: 120 bpm (seuil: 100 bpm)' }}</p>
                    @if($alert->type ?? false)
                        <p><i class="fas fa-tag"></i> Type: {{ ucfirst($alert->type) }}</p>
                    @endif
                </div>

                <div class="alert-actions">
                    @if(!($alert->resolved ?? false))
                        <button class="btn btn-primary resolve-btn" data-alert-id="{{ $alert->id ?? 1 }}">
                            <i class="fas fa-check"></i>
                            Résoudre
                        </button>
                    @else
                        <span class="btn resolved-indicator">
                            <i class="fas fa-check-circle"></i>
                            Résolu
                        </span>
                    @endif
                    <a href="{{ route('alertes.show', $alert->id ?? 1) }}" class="btn btn-secondary">
                        <i class="fas fa-eye"></i>
                        Détails
                    </a>
                </div>
            </div>
        @empty
            <!-- Alertes d'exemple si aucune donnée -->
            <div class="alert-card critical" data-alert-id="1">
                <div class="alert-header">
                    <span class="alert-severity critical">
                        <i class="fas fa-exclamation-circle"></i> Critique
                    </span>
                    <div class="alert-time">
                        <i class="fas fa-clock"></i>
                        Il y a 5 min
                    </div>
                </div>

                <div class="alert-patient">
                    <div class="patient-avatar">MR</div>
                    <div class="patient-info">
                        <h4>Mehdi Rais</h4>
                        <p><i class="fas fa-id-card"></i> ID: 001</p>
                    </div>
                </div>

                <div class="alert-message">
                    <strong>Fréquence cardiaque critique</strong>
                    <p>Fréquence cardiaque très élevée: 145 bpm (seuil critique: 130 bpm)</p>
                    <p><i class="fas fa-heartbeat"></i> Type: Fréquence cardiaque</p>
                </div>

                <div class="alert-actions">
                    <button class="btn btn-primary resolve-btn" data-alert-id="1">
                        <i class="fas fa-check"></i>
                        Résoudre
                    </button>
                    <a href="{{ route('alertes.show', 1) }}" class="btn btn-secondary">
                        <i class="fas fa-eye"></i>
                        Détails
                    </a>
                </div>
            </div>

            <div class="alert-card high" data-alert-id="2">
                <div class="alert-header">
                    <span class="alert-severity high">
                        <i class="fas fa-exclamation"></i> Élevée
                    </span>
                    <div class="alert-time">
                        <i class="fas fa-clock"></i>
                        Il y a 15 min
                    </div>
                </div>

                <div class="alert-patient">
                    <div class="patient-avatar">ST</div>
                    <div class="patient-info">
                        <h4>Sara Tazi</h4>
                        <p><i class="fas fa-id-card"></i> ID: 002</p>
                    </div>
                </div>

                <div class="alert-message">
                    <strong>Température élevée</strong>
                    <p>Température corporelle élevée: 38.7°C (seuil: 38°C)</p>
                    <p><i class="fas fa-thermometer-half"></i> Type: Température</p>
                </div>

                <div class="alert-actions">
                    <button class="btn btn-primary resolve-btn" data-alert-id="2">
                        <i class="fas fa-check"></i>
                        Résoudre
                    </button>
                    <a href="{{ route('alerts.show', 2) }}" class="btn btn-secondary">
                        <i class="fas fa-eye"></i>
                        Détails
                    </a>
                </div>
            </div>

            <div class="alert-card medium" data-alert-id="3">
                <div class="alert-header">
                    <span class="alert-severity medium">
                        <i class="fas fa-triangle-exclamation"></i> Moyenne
                    </span>
                    <div class="alert-time">
                        <i class="fas fa-clock"></i>
                        Il y a 30 min
                    </div>
                </div>

                <div class="alert-patient">
                    <div class="patient-avatar">KA</div>
                    <div class="patient-info">
                        <h4>Karim Alaoui</h4>
                        <p><i class="fas fa-id-card"></i> ID: 003</p>
                    </div>
                </div>

                <div class="alert-message">
                    <strong>Saturation O₂ faible</strong>
                    <p>Saturation en oxygène faible: 93% (seuil: 95%)</p>
                    <p><i class="fas fa-lungs"></i> Type: Saturation O₂</p>
                </div>

                <div class="alert-actions">
                    <span class="btn resolved-indicator">
                        <i class="fas fa-check-circle"></i>
                        Résolu
                    </span>
                    <a href="{{ route('alertes.show', 3) }}" class="btn btn-secondary">
                        <i class="fas fa-eye"></i>
                        Détails
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestion de la résolution des alertes
        const resolveBtns = document.querySelectorAll('.resolve-btn');
        
        resolveBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const alertId = this.getAttribute('data-alert-id');
                resolveAlert(alertId, this);
            });
        });

        // Animation d'entrée pour les cartes
        const cards = document.querySelectorAll('.alert-card');
        cards.forEach((card, index) => {
            setTimeout(() => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                card.style.transition = 'all 0.6s ease';
                
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 100);
            }, index * 100);
        });
    });

    function resolveAlert(alertId, buttonElement) {
        if (confirm('Êtes-vous sûr de vouloir résoudre cette alerte ?')) {
            // Désactiver le bouton pendant le traitement
            buttonElement.disabled = true;
            buttonElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Résolution...';
            
            // Appel AJAX pour résoudre l'alerte
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
                    // Remplacer le bouton de résolution par l'indicateur "Résolu"
                    buttonElement.outerHTML = `
                        <span class="btn resolved-indicator">
                            <i class="fas fa-check-circle"></i>
                            Résolu
                        </span>
                    `;
                    
                    // Mettre à jour les statistiques si présentes
                    updateAlertStats();
                    
                    // Afficher un message de succès
                    showNotification('Alerte résolue avec succès', 'success');
                } else {
                    // Réactiver le bouton en cas d'erreur
                    buttonElement.disabled = false;
                    buttonElement.innerHTML = '<i class="fas fa-check"></i> Résoudre';
                    showNotification('Erreur lors de la résolution de l\'alerte', 'error');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                // Réactiver le bouton en cas d'erreur
                buttonElement.disabled = false;
                buttonElement.innerHTML = '<i class="fas fa-check"></i> Résoudre';
                showNotification('Erreur lors de la résolution de l\'alerte', 'error');
            });
        }
    }

    function updateAlertStats() {
        // Mettre à jour le compteur d'alertes actives si présent
        const criticalCount = document.querySelector('.stat-card.critical .stat-value');
        const highCount = document.querySelector('.stat-card.high .stat-value');
        const mediumCount = document.querySelector('.stat-card.medium .stat-value');
        const lowCount = document.querySelector('.stat-card.low .stat-value');
        
        // Ici vous pouvez implémenter la logique de mise à jour des compteurs
        // selon la sévérité de l'alerte résolue
    }

    function showNotification(message, type) {
        // Créer une notification temporaire
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            z-index: 1000;
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.3s ease;
            ${type === 'success' ? 'background: #10b981;' : 'background: #dc2626;'}
        `;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Animer l'apparition
        setTimeout(() => {
            notification.style.opacity = '1';
            notification.style.transform = 'translateX(0)';
        }, 100);
        
        // Supprimer après 3 secondes
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }
</script>
@endsection