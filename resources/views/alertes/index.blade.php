@extends('layouts.app')

@section('title', 'Gestion des Alertes | Système de Surveillance')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/alertes.css') }}">
@endsection

@section('content')
    <div class="app-container">
      
            <div class="alerts-container">
                <div class="alerts-header">
                    <div class="alerts-status">
                        <div class="status-badge active-alerts">
                            <i class="fas fa-exclamation-circle"></i>
                            <span id="active-alerts-count">{{ $activeAlertsCount ?? 0 }}</span> Alertes actives
                        </div>
                        <div class="status-badge resolved-alerts">
                            <i class="fas fa-check-circle"></i>
                            <span id="resolved-alerts-count">{{ $resolvedAlertsCount ?? 0 }}</span> Alertes résolues
                        </div>
                    </div>
                    <div class="alerts-actions">
                        <a href="{{ route('alertes.settings') }}" class="action-btn settings-btn">
                            <i class="fas fa-cog"></i> Paramètres d'alerte
                        </a>
                        <button class="action-btn resolve-all-btn" 
                                data-url="{{ route('alertes.resolve-all') }}" 
                                @if(empty($activeAlerts) || $activeAlertsCount == 0) disabled @endif>
                            <i class="fas fa-check-double"></i> Tout marquer comme résolu
                        </button>
                    </div>
                </div>

                <div class="alerts-tabs">
                    <button class="tab-btn active" data-tab="active">Alertes Actives</button>
                    <button class="tab-btn" data-tab="history">Historique</button>
                    <button class="tab-btn" data-tab="settings">Configuration</button>
                </div>

                <div class="tab-content active" id="active-tab">
                    <div class="filter-bar">
                        <form id="active-filter-form" action="{{ route('alertes.index') }}" method="GET" class="w-full flex flex-wrap gap-4 items-center">
                            <div class="filter-group">
                                <label for="severity-filter">Sévérité:</label>
                                <select id="severity-filter" name="severity">
                                    <option value="all">Toutes</option>
                                    <option value="critical" {{ request('severity') == 'critical' ? 'selected' : '' }}>Critique</option>
                                    <option value="high" {{ request('severity') == 'high' ? 'selected' : '' }}>Élevée</option>
                                    <option value="medium" {{ request('severity') == 'medium' ? 'selected' : '' }}>Moyenne</option>
                                    <option value="low" {{ request('severity') == 'low' ? 'selected' : '' }}>Faible</option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label for="type-filter">Type:</label>
                                <select id="type-filter" name="type">
                                    <option value="all">Tous</option>
                                    <option value="heartrate" {{ request('type') == 'heartrate' ? 'selected' : '' }}>Fréquence Cardiaque</option>
                                    <option value="spo2" {{ request('type') == 'spo2' ? 'selected' : '' }}>SpO2</option>
                                    <option value="temperature" {{ request('type') == 'temperature' ? 'selected' : '' }}>Température</option>
                                    <option value="bloodpressure" {{ request('type') == 'bloodpressure' ? 'selected' : '' }}>Tension Artérielle</option>
                                    <option value="medication" {{ request('type') == 'medication' ? 'selected' : '' }}>Médication</option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label for="patient-filter">Patient:</label>
                                <select id="patient-filter" name="patient_id">
                                    <option value="all">Tous</option>
                                    @foreach ($patients ?? [] as $patient)
                                        <option value="{{ $patient->id }}" {{ request('patient_id') == $patient->id ? 'selected' : '' }}>
                                            {{ $patient->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="search-group">
                                <i class="fas fa-search"></i>
                                <input type="text" id="search-alerts" name="search" placeholder="Rechercher..." value="{{ request('search') }}">
                                <button type="submit" id="search-btn"><i class="fas fa-arrow-right"></i></button>
                            </div>
                        </form>
                    </div>

                    <div class="alerts-list" id="active-alerts">
                        @forelse ($activeAlerts ?? [] as $alert)
                            <div class="alert-item alert-{{ $alert->severity }}" data-alert-id="{{ $alert->id }}">
                                <div class="alert-info">
                                    <div class="alert-icon">
                                        @if($alert->type == 'heartrate')
                                            <i class="fas fa-heartbeat"></i>
                                        @elseif($alert->type == 'spo2')
                                            <i class="fas fa-lungs"></i>
                                        @elseif($alert->type == 'temperature')
                                            <i class="fas fa-thermometer-half"></i>
                                        @elseif($alert->type == 'bloodpressure')
                                            <i class="fas fa-tachometer-alt"></i>
                                        @elseif($alert->type == 'medication')
                                            <i class="fas fa-pills"></i>
                                        @else
                                            <i class="fas fa-exclamation-circle"></i>
                                        @endif
                                    </div>
                                    <div class="alert-details">
                                        <h4 class="alert-title">{{ $alert->title }}</h4>
                                        <p class="alert-message">{{ $alert->message }}</p>
                                    </div>
                                </div>
                                <div class="alert-meta">
                                    <span class="alert-timestamp">{{ $alert->created_at->diffForHumans() }}</span>
                                    <span class="alert-severity severity-{{ $alert->severity }}">
                                        {{ ucfirst($alert->severity) }}
                                    </span>
                                </div>
                                <div class="alert-actions">
                                    <button class="alert-btn" title="Voir détails" onclick="showAlertDetails({{ $alert->id }})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="alert-btn resolve-btn" title="Résoudre l'alerte" 
                                            data-url="{{ route('alertes.resolve', ['alert' => $alert->id]) }}">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="no-alerts-message">
                                <i class="fas fa-check-circle"></i>
                                <p>Aucune alerte active pour le moment</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="tab-content" id="history-tab">
                    <div class="filter-bar">
                        <form id="history-filter-form" action="{{ route('alertes.history') }}" method="GET" class="w-full flex flex-wrap gap-4 items-center">
                            <div class="filter-group">
                                <label for="date-from">Du:</label>
                                <input type="date" id="date-from" name="date_from" value="{{ request('date_from', date('Y-m-d', strtotime('-30 days'))) }}">
                            </div>
                            <div class="filter-group">
                                <label for="date-to">Au:</label>
                                <input type="date" id="date-to" name="date_to" value="{{ request('date_to', date('Y-m-d')) }}">
                            </div>
                            <div class="filter-group">
                                <label for="history-type-filter">Type:</label>
                                <select id="history-type-filter" name="type">
                                    <option value="all">Tous</option>
                                    <option value="heartrate" {{ request('type') == 'heartrate' ? 'selected' : '' }}>Fréquence Cardiaque</option>
                                    <option value="spo2" {{ request('type') == 'spo2' ? 'selected' : '' }}>SpO2</option>
                                    <option value="temperature" {{ request('type') == 'temperature' ? 'selected' : '' }}>Température</option>
                                    <option value="bloodpressure" {{ request('type') == 'bloodpressure' ? 'selected' : '' }}>Tension Artérielle</option>
                                    <option value="medication" {{ request('type') == 'medication' ? 'selected' : '' }}>Médication</option>
                                </select>
                            </div>
                            <button type="submit" id="apply-history-filter" class="apply-btn">
                                <i class="fas fa-filter mr-2"></i> Appliquer
                            </button>
                        </form>
                    </div>

                    <div class="alerts-list" id="history-alerts">
                        @forelse ($historyAlerts ?? [] as $alert)
                            <div class="alert-item alert-{{ $alert->severity }}" data-alert-id="{{ $alert->id }}">
                                <div class="alert-info">
                                    <div class="alert-icon">
                                        @if($alert->type == 'heartrate')
                                            <i class="fas fa-heartbeat"></i>
                                        @elseif($alert->type == 'spo2')
                                            <i class="fas fa-lungs"></i>
                                        @elseif($alert->type == 'temperature')
                                            <i class="fas fa-thermometer-half"></i>
                                        @elseif($alert->type == 'bloodpressure')
                                            <i class="fas fa-tachometer-alt"></i>
                                        @elseif($alert->type == 'medication')
                                            <i class="fas fa-pills"></i>
                                        @else
                                            <i class="fas fa-exclamation-circle"></i>
                                        @endif
                                    </div>
                                    <div class="alert-details">
                                        <h4 class="alert-title">{{ $alert->title }}</h4>
                                        <p class="alert-message">{{ $alert->message }}</p>
                                    </div>
                                </div>
                                <div class="alert-meta">
                                    <span class="alert-timestamp">
                                        Résolu: {{ $alert->resolved_at->format('d/m/Y H:i') }}
                                    </span>
                                    <span class="alert-severity severity-{{ $alert->severity }}">
                                        {{ ucfirst($alert->severity) }}
                                    </span>
                                </div>
                                <div class="alert-actions">
                                    <button class="alert-btn" title="Voir détails" onclick="showAlertDetails({{ $alert->id }})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="no-alerts-message">
                                <i class="fas fa-history"></i>
                                <p>Aucune alerte dans l'historique pour cette période</p>
                            </div>
                        @endforelse
                    </div>

                    @if(isset($historyAlerts) && $historyAlerts->hasPages())
                        <div class="pagination">
                            <button class="pagination-btn" onclick="window.location='{{ $historyAlerts->previousPageUrl() }}'" {{ $historyAlerts->onFirstPage() ? 'disabled' : '' }}>
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <span class="page-info">Page {{ $historyAlerts->currentPage() }} sur {{ $historyAlerts->lastPage() }}</span>
                            <button class="pagination-btn" onclick="window.location='{{ $historyAlerts->nextPageUrl() }}'" {{ !$historyAlerts->hasMorePages() ? 'disabled' : '' }}>
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    @endif
                </div>

                <div class="tab-content" id="settings-tab">
                    <div class="settings-section">
                        <h3><i class="fas fa-sliders-h"></i> Configuration des Seuils d'Alerte</h3>
                        <p>Définissez les seuils pour lesquels une alerte sera déclenchée et personnalisez les notifications.</p>

                        <form id="alert-settings-form" action="{{ route('alertes.settings.save') }}" method="POST">
                            @csrf
                            <div class="settings-grid">
                                <div class="settings-card">
                                    <h4><i class="fas fa-heartbeat"></i> Fréquence Cardiaque</h4>
                                    <div class="threshold-settings">
                                        <div class="threshold-group">
                                            <label for="heart-rate-low">Seuil bas (bpm):</label>
                                            <input type="number" name="heart_rate_low" id="heart-rate-low" value="{{ $settings->heart_rate_low ?? 60 }}" min="40" max="100">
                                        </div>
                                        <div class="threshold-group">
                                            <label for="heart-rate-high">Seuil haut (bpm):</label>
                                            <input type="number" name="heart_rate_high" id="heart-rate-high" value="{{ $settings->heart_rate_high ?? 100 }}" min="60" max="180">
                                        </div>
                                        <div class="severity-group">
                                            <label for="heart-rate-severity">Sévérité:</label>
                                            <select name="heart_rate_severity" id="heart-rate-severity">
                                                <option value="low" {{ ($settings->heart_rate_severity ?? '') === 'low' ? 'selected' : '' }}>Faible</option>
                                                <option value="medium" {{ ($settings->heart_rate_severity ?? '') === 'medium' ? 'selected' : '' }}>Moyenne</option>
                                                <option value="high" {{ ($settings->heart_rate_severity ?? 'high') === 'high' ? 'selected' : '' }}>Élevée</option>
                                                <option value="critical" {{ ($settings->heart_rate_severity ?? '') === 'critical' ? 'selected' : '' }}>Critique</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="settings-card">
                                    <h4><i class="fas fa-lungs"></i> Oxygénation (SpO2)</h4>
                                    <div class="threshold-settings">
                                        <div class="threshold-group">
                                            <label for="spo2-low">Seuil bas (%):</label>
                                            <input type="number" name="spo2_low" id="spo2-low" value="{{ $settings->spo2_low ?? 94 }}" min="80" max="100">
                                        </div>
                                        <div class="severity-group">
                                            <label for="spo2-severity">Sévérité:</label>
                                            <select name="spo2_severity" id="spo2-severity">
                                                <option value="low" {{ ($settings->spo2_severity ?? '') === 'low' ? 'selected' : '' }}>Faible</option>
                                                <option value="medium" {{ ($settings->spo2_severity ?? '') === 'medium' ? 'selected' : '' }}>Moyenne</option>
                                                <option value="high" {{ ($settings->spo2_severity ?? '') === 'high' ? 'selected' : '' }}>Élevée</option>
                                                <option value="critical" {{ ($settings->spo2_severity ?? 'critical') === 'critical' ? 'selected' : '' }}>Critique</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="settings-card">
                                    <h4><i class="fas fa-thermometer-half"></i> Température</h4>
                                    <div class="threshold-settings">
                                        <div class="threshold-group">
                                            <label for="temp-low">Seuil bas (°C):</label>
                                            <input type="number" name="temp_low" id="temp-low" value="{{ $settings->temp_low ?? 36.0 }}" min="34.0" max="37.0" step="0.1">
                                        </div>
                                        <div class="threshold-group">
                                            <label for="temp-high">Seuil haut (°C):</label>
                                            <input type="number" name="temp_high" id="temp-high" value="{{ $settings->temp_high ?? 38.0 }}" min="37.0" max="42.0" step="0.1">
                                        </div>
                                        <div class="severity-group">
                                            <label for="temp-severity">Sévérité:</label>
                                            <select name="temp_severity" id="temp-severity">
                                                <option value="low" {{ ($settings->temp_severity ?? '') === 'low' ? 'selected' : '' }}>Faible</option>
                                                <option value="medium" {{ ($settings->temp_severity ?? '') === 'medium' ? 'selected' : '' }}>Moyenne</option>
                                                <option value="high" {{ ($settings->temp_severity ?? 'high') === 'high' ? 'selected' : '' }}>Élevée</option>
                                                <option value="critical" {{ ($settings->temp_severity ?? '') === 'critical' ? 'selected' : '' }}>Critique</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="settings-card">
                                    <h4><i class="fas fa-tachometer-alt"></i> Tension Artérielle</h4>
                                    <div class="threshold-settings">
                                        <div class="threshold-group">
                                            <label for="bp-sys-low">Systolique min:</label>
                                            <input type="number" name="bp_sys_low" id="bp-sys-low" value="{{ $settings->bp_sys_low ?? 90 }}" min="70" max="120">
                                        </div>
                                        <div class="threshold-group">
                                            <label for="bp-sys-high">Systolique max:</label>
                                            <input type="number" name="bp_sys_high" id="bp-sys-high" value="{{ $settings->bp_sys_high ?? 140 }}" min="120" max="200">
                                        </div>
                                        <div class="threshold-group">
                                            <label for="bp-dia-low">Diastolique min:</label>
                                            <input type="number" name="bp_dia_low" id="bp-dia-low" value="{{ $settings->bp_dia_low ?? 60 }}" min="40" max="80">
                                        </div>
                                        <div class="threshold-group">
                                            <label for="bp-dia-high">Diastolique max:</label>
                                            <input type="number" name="bp_dia_high" id="bp-dia-high" value="{{ $settings->bp_dia_high ?? 90 }}" min="80" max="120">
                                        </div>
                                        <div class="severity-group">
                                            <label for="bp-severity">Sévérité:</label>
                                            <select name="bp_severity" id="bp-severity">
                                                <option value="low" {{ ($settings->bp_severity ?? '') === 'low' ? 'selected' : '' }}>Faible</option>
                                                <option value="medium" {{ ($settings->bp_severity ?? 'medium') === 'medium' ? 'selected' : '' }}>Moyenne</option>
                                                <option value="high" {{ ($settings->bp_severity ?? '') === 'high' ? 'selected' : '' }}>Élevée</option>
                                                <option value="critical" {{ ($settings->bp_severity ?? '') === 'critical' ? 'selected' : '' }}>Critique</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="notification-settings">
                                <h3><i class="fas fa-bell"></i> Paramètres de Notification</h3>
                                <div class="notification-options">
                                    <div class="notification-option">
                                        <input type="checkbox" name="notify_email" id="notify-email" {{ ($settings->notify_email ?? true) ? 'checked' : '' }}>
                                        <label for="notify-email">Notifications par email</label>
                                    </div>
                                    <div class="notification-option">
                                        <input type="checkbox" name="notify_sms" id="notify-sms" {{ ($settings->notify_sms ?? true) ? 'checked' : '' }}>
                                        <label for="notify-sms">Notifications par SMS</label>
                                    </div>
                                    <div class="notification-option">
                                        <input type="checkbox" name="notify_app" id="notify-app" {{ ($settings->notify_app ?? true) ? 'checked' : '' }}>
                                        <label for="notify-app">Notifications dans l'application</label>
                                    </div>
                                    <div class="notification-option">
                                        <input type="checkbox" name="notify_critical_only" id="notify-critical-only" {{ ($settings->notify_critical_only ?? false) ? 'checked' : '' }}>
                                        <label for="notify-critical-only">Notifier uniquement les alertes critiques</label>
                                    </div>
                                </div>
                            </div>

                            <div class="settings-actions">
                                <button type="button" id="reset-alert-settings" class="reset-btn" data-url="{{ route('alertes.settings.reset') }}">
                                    <i class="fas fa-undo mr-1"></i> Réinitialiser
                                </button>
                                <button type="submit" id="save-alert-settings" class="save-btn">
                                    <i class="fas fa-save mr-1"></i> Enregistrer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de détails d'alerte -->
    <div class="alert-details-modal" id="alert-details-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-exclamation-triangle"></i> Détails de l'Alerte</h3>
                <button class="close-modal" id="close-modal"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body" id="alert-details-content">
                <!-- Le contenu sera injecté dynamiquement via JavaScript -->
                <div class="flex items-center justify-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-color"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="resolve-btn" id="resolve-alert-btn" data-url="{{ route('alertes.resolve', ['alert' => ':alertId']) }}">
                    <i class="fas fa-check mr-1"></i> Résoudre
                </button>
                <button class="contact-btn" id="contact-patient-btn" data-url="{{ route('alertes.contact-patient', ['alert' => ':alertId']) }}">
                    <i class="fas fa-user-md mr-1"></i> Contacter le patient
                </button>
                <button class="close-btn" id="close-modal-btn">Fermer</button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestion des onglets
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                const tabId = button.getAttribute('data-tab');
                
                // Désactiver tous les onglets
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));
                
                // Activer l'onglet sélectionné
                button.classList.add('active');
                document.getElementById(tabId + '-tab').classList.add('active');
            });
        });

        // Fonction pour résoudre toutes les alertes
        const resolveAllBtn = document.querySelector('.resolve-all-btn');
        if (resolveAllBtn) {
            resolveAllBtn.addEventListener('click', function() {
                if (!this.hasAttribute('disabled')) {
                    if (confirm('Êtes-vous sûr de vouloir marquer toutes les alertes comme résolues?')) {
                        const url = this.getAttribute('data-url');
                        fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                window.location.reload();
                            } else {
                                alert('Une erreur est survenue lors de la résolution des alertes.');
                            }
                        })
                        .catch(error => {
                            console.error('Erreur:', error);
                            alert('Une erreur est survenue lors de la résolution des alertes.');
                        });
                    }
                }
            });
        }

        // Fonction pour résoudre une alerte individuelle
        const resolveBtns = document.querySelectorAll('.resolve-btn');
        resolveBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const url = this.getAttribute('data-url');
                if (url) {
                    if (confirm('Êtes-vous sûr de vouloir marquer cette alerte comme résolue?')) {
                        fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const alertItem = this.closest('.alert-item');
                                alertItem.style.opacity = '0';
                                setTimeout(() => {
                                    alertItem.remove();
                                    updateAlertCount();
                                    if (document.querySelectorAll('#active-alerts .alert-item').length === 0) {
                                        document.getElementById('active-alerts').innerHTML = `
                                            <div class="no-alerts-message">
                                                <i class="fas fa-check-circle"></i>
                                                <p>Aucune alerte active pour le moment</p>
                                            </div>
                                        `;
                                    }
                                }, 300);
                            } else {
                                alert('Une erreur est survenue lors de la résolution de l\'alerte.');
                            }
                        })
                        .catch(error => {
                            console.error('Erreur:', error);
                            alert('Une erreur est survenue lors de la résolution de l\'alerte.');
                        });
                    }
                }
            });
        });

        // Fonction pour mettre à jour le compteur d'alertes
        function updateAlertCount() {
            const activeCount = document.querySelectorAll('#active-alerts .alert-item').length;
            const activeCountElement = document.getElementById('active-alerts-count');
            if (activeCountElement) {
                activeCountElement.textContent = activeCount;
            }
            
            if (activeCount === 0) {
                if (resolveAllBtn) {
                    resolveAllBtn.setAttribute('disabled', '');
                }
            }
        }

        // Gestion du modal de détails d'alerte
        const modal = document.getElementById('alert-details-modal');
        const closeModalBtns = document.querySelectorAll('#close-modal, #close-modal-btn');
        
        closeModalBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                modal.classList.remove('show');
            });
        });

        // Cliquer en dehors du modal pour le fermer
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.classList.remove('show');
            }
        });

        // Bouton de réinitialisation des paramètres
        const resetBtn = document.getElementById('reset-alert-settings');
        if (resetBtn) {
            resetBtn.addEventListener('click', function() {
                if (confirm('Êtes-vous sûr de vouloir réinitialiser tous les paramètres?')) {
                    const url = this.getAttribute('data-url');
                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        } else {
                            alert('Une erreur est survenue lors de la réinitialisation des paramètres.');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        alert('Une erreur est survenue lors de la réinitialisation des paramètres.');
                    });
                }
            });
        }
    });

    // Fonction pour afficher les détails d'une alerte
    function showAlertDetails(alertId) {
        const modal = document.getElementById('alert-details-modal');
        const contentDiv = document.getElementById('alert-details-content');
        const resolveBtn = document.getElementById('resolve-alert-btn');
        const contactBtn = document.getElementById('contact-patient-btn');

        // Afficher le modal avec animation de chargement
        modal.classList.add('show');
        contentDiv.innerHTML = `
            <div class="flex items-center justify-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-color"></div>
            </div>
        `;

        // Mettre à jour les URLs des boutons
        if (resolveBtn) {
            resolveBtn.setAttribute('data-url', resolveBtn.getAttribute('data-url').replace(':alertId', alertId));
        }
        
        if (contactBtn) {
            contactBtn.setAttribute('data-url', contactBtn.getAttribute('data-url').replace(':alertId', alertId));
        }

        // Charger les détails de l'alerte
        fetch(`/alerts/${alertId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            contentDiv.innerHTML = html;
            
            // Si c'est une alerte déjà résolue, cacher le bouton de résolution
            if (document.querySelector('.alert-detail-resolved')) {
                resolveBtn.style.display = 'none';
            } else {
                resolveBtn.style.display = 'inline-flex';
                
                // Ajouter l'événement au bouton de résolution dans le modal
                resolveBtn.addEventListener('click', function() {
                    const url = this.getAttribute('data-url');
                    
                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            modal.classList.remove('show');
                            window.location.reload();
                        } else {
                            alert('Une erreur est survenue lors de la résolution de l\'alerte.');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        alert('Une erreur est survenue lors de la résolution de l\'alerte.');
                    });
                });
            }
            
            // Ajouter l'événement au bouton de contact
            contactBtn.addEventListener('click', function() {
                const url = this.getAttribute('data-url');
                
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(`Message: ${data.message}`);
                    } else {
                        alert('Une erreur est survenue lors de la tentative de contact du patient.');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Une erreur est survenue lors de la tentative de contact du patient.');
                });
            });
        })
        .catch(error => {
            console.error('Erreur:', error);
            contentDiv.innerHTML = '<p class="text-red-500 text-center py-4">Une erreur est survenue lors du chargement des détails.</p>';
        });
    }
</script>
@endsection