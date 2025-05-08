@extends('layouts.app')

@section('title', 'Alertes | Système de Surveillance Médicale')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/alerts.css') }}">
@endsection

@section('content')
    <div class="app-container">
        @include('patials.sidebar')

        <div class="main-content">
            <div class="main-header">
                <h1>Gestion des Alertes</h1>
                <div class="user-info">
                    <span>{{ auth()->user()->name ?? 'Dr. Ahmed' }}</span>
                    <img src="{{ auth()->user()->avatar ?? 'https://randomuser.me/api/portraits/men/4.jpg' }}" alt="User" class="avatar">
                </div>
            </div>

            <div class="alerts-container">
                <div class="alerts-header">
                    <div class="alerts-status">
                        <div class="status-badge active-alerts">
                            <i class="fas fa-exclamation-circle"></i>
                            <span id="active-alerts-count">{{ $activeAlertsCount ?? 5 }}</span> Alertes actives
                        </div>
                        <div class="status-badge resolved-alerts">
                            <i class="fas fa-check-circle"></i>
                            <span id="resolved-alerts-count">{{ $resolvedAlertsCount ?? 23 }}</span> Alertes résolues
                        </div>
                    </div>
                    <div class="alerts-actions">
                        <button class="action-btn settings-btn">
                            <i class="fas fa-cog"></i> Paramètres d'alerte
                        </button>
                        <button class="action-btn resolve-all-btn" 
                                data-url="{{ route('alerts.resolve-all') }}" 
                                @if(empty($activeAlerts)) disabled @endif>
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
                        <form id="active-filter-form" action="{{ route('alerts.index') }}" method="GET">
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
                                    @if(empty($patients))
                                        <option value="1">Mehdi Rais</option>
                                        <option value="2">Sara Tazi</option>
                                        <option value="3">Karim Alaoui</option>
                                    @endif
                                </select>
                            </div>
                            <div class="search-group">
                                <input type="text" id="search-alerts" name="search" placeholder="Rechercher..." value="{{ request('search') }}">
                                <button type="submit" id="search-btn"><i class="fas fa-search"></i></button>
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
                                    <span class="alert-timestamp">{{ $alert->created_at->format('d/m/Y H:i') }}</span>
                                    <span class="alert-severity severity-{{ $alert->severity }}">
                                        {{ ucfirst($alert->severity) }}
                                    </span>
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
                        <form id="history-filter-form" action="{{ route('alerts.history') }}" method="GET">
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
                            <button type="submit" id="apply-history-filter" class="apply-btn">Appliquer</button>
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
                                    <span class="alert-timestamp">{{ $alert->created_at->format('d/m/Y H:i') }}</span>
                                    <span class="alert-severity severity-{{ $alert->severity }}">
                                        {{ ucfirst($alert->severity) }}
                                    </span>
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
                        {{ $historyAlerts->withQueryString()->links('pagination.custun') }}
                    @else
                        <div class="pagination">
                            <button class="pagination-btn prev-history-page" {{ isset($currentHistoryPage) && $currentHistoryPage <= 1 ? 'disabled' : '' }}>
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <span class="page-info">Page <span id="current-history-page">{{ $currentHistoryPage ?? 1 }}</span> sur <span id="total-history-pages">{{ $totalHistoryPages ?? 10 }}</span></span>
                            <button class="pagination-btn next-history-page" {{ isset($currentHistoryPage) && isset($totalHistoryPages) && $currentHistoryPage >= $totalHistoryPages ? 'disabled' : '' }}>
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    @endif
                </div>

                <div class="tab-content" id="settings-tab">
                    <div class="settings-section">
                        <h3>Configuration des Seuils d'Alerte</h3>
                        <p>Définissez les seuils pour lesquels une alerte sera déclenchée.</p>

                        <form id="alert-settings-form" action="{{ route('alerts.settings.save') }}" method="POST">
                            @csrf
                            <div class="settings-grid">
                                <div class="settings-card">
                                    <h4><i class="fas fa-heartbeat"></i> Fréquence Cardiaque</h4>
                                    <div class="threshold-settings">
                                        <div class="threshold-group">
                                            <label>Seuil bas (bpm):</label>
                                            <input type="number" name="heart_rate_low" id="heart-rate-low" value="{{ $settings->heart_rate_low ?? 60 }}" min="40" max="100">
                                        </div>
                                        <div class="threshold-group">
                                            <label>Seuil haut (bpm):</label>
                                            <input type="number" name="heart_rate_high" id="heart-rate-high" value="{{ $settings->heart_rate_high ?? 100 }}" min="60" max="180">
                                        </div>
                                        <div class="severity-group">
                                            <label>Sévérité:</label>
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
                                            <label>Seuil bas (%):</label>
                                            <input type="number" name="spo2_low" id="spo2-low" value="{{ $settings->spo2_low ?? 94 }}" min="80" max="100">
                                        </div>
                                        <div class="severity-group">
                                            <label>Sévérité:</label>
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
                                            <label>Seuil bas (°C):</label>
                                            <input type="number" name="temp_low" id="temp-low" value="{{ $settings->temp_low ?? 36.0 }}" min="34.0" max="37.0" step="0.1">
                                        </div>
                                        <div class="threshold-group">
                                            <label>Seuil haut (°C):</label>
                                            <input type="number" name="temp_high" id="temp-high" value="{{ $settings->temp_high ?? 38.0 }}" min="37.0" max="42.0" step="0.1">
                                        </div>
                                        <div class="severity-group">
                                            <label>Sévérité:</label>
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
                                            <label>Systolique min:</label>
                                            <input type="number" name="bp_sys_low" id="bp-sys-low" value="{{ $settings->bp_sys_low ?? 90 }}" min="70" max="120">
                                        </div>
                                        <div class="threshold-group">
                                            <label>Systolique max:</label>
                                            <input type="number" name="bp_sys_high" id="bp-sys-high" value="{{ $settings->bp_sys_high ?? 140 }}" min="120" max="200">
                                        </div>
                                        <div class="threshold-group">
                                            <label>Diastolique min:</label>
                                            <input type="number" name="bp_dia_low" id="bp-dia-low" value="{{ $settings->bp_dia_low ?? 60 }}" min="40" max="80">
                                        </div>
                                        <div class="threshold-group">
                                            <label>Diastolique max:</label>
                                            <input type="number" name="bp_dia_high" id="bp-dia-high" value="{{ $settings->bp_dia_high ?? 90 }}" min="80" max="120">
                                        </div>
                                        <div class="severity-group">
                                            <label>Sévérité:</label>
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
                                <h3>Paramètres de Notification</h3>
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
                                <button type="submit" id="save-alert-settings" class="save-btn">Enregistrer les paramètres</button>
                                <button type="button" id="reset-alert-settings" class="reset-btn" data-url="{{ route('alerts.settings.reset') }}">Réinitialiser</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="alert-details-modal" id="alert-details-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Détails de l'Alerte</h3>
                <button class="close-modal"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body" id="alert-details-content">
                <!-- Le contenu sera injecté dynamiquement via JavaScript -->
            </div>
            <div class="modal-footer">
                <button class="resolve-btn" data-url="{{ route('alerts.resolve', ['alert' => ':alertId']) }}">Marquer comme résolu</button>
                <button class="action-btn" data-url="{{ route('alerts.contact-patient', ['alert' => ':alertId']) }}">Contacter le patient</button>
                <button class="close-btn">Fermer</button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/alerts.js') }}"></script>
@endsection