@extends('layouts.app')

@section('title', 'HealthStatut - Tableau de Bord')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/bord.css') }}">
<link rel="stylesheet" href="{{ asset('css/animation.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('content')
    <!-- Stats Grid -->
    <section class="stats-grid">
        @foreach($stats ?? [] as $stat)
            <div class="stats-card {{ $stat['class'] }}">
                <i class="icon-{{ $stat['icon'] }}"></i>
                <h3>{{ $stat['title'] }}</h3>
                <p class="value">{{ $stat['value'] }}</p>
                <p class="trend {{ $stat['trend_direction'] }}">
                    {{ $stat['trend_value'] }}
                    <span>{{ $stat['trend_period'] }}</span>
                </p>
            </div>
        @endforeach

        @if(!isset($stats) || count($stats ?? []) === 0)
            <div class="stats-card patients-card">
                <i class="icon-patients-total"></i>
                <h3>Total Patients</h3>
                <p class="value">248</p>
                <p class="trend up">+12% <span>ce mois</span></p>
            </div>
            <div class="stats-card alerts-card">
                <i class="icon-alert-bell"></i>
                <h3>Alertes actives</h3>
                <p class="value">13</p>
                <p class="trend down">-5% <span>vs hier</span></p>
            </div>
            <div class="stats-card critical-card">
                <i class="icon-critical"></i>
                <h3>État critique</h3>
                <p class="value">4</p>
                <p class="trend up">+2 <span>depuis hier</span></p>
            </div>
            <div class="stats-card sensors-card">
                <i class="icon-sensor"></i>
                <h3>Capteurs actifs</h3>
                <p class="value">156</p>
                <p class="trend neutral">0% <span>ce mois</span></p>
            </div>
        @endif
    </section>

    <div class="content-grid">
        <!-- Patients Table -->
        <section class="data-card patients-table-container">
            <div class="card-header">
                <h2><i class="fas fa-user-injured"></i> Patients récents</h2>
                <button class="view-all-btn">Voir tout</button>
            </div>
            <div class="table-wrapper">
                <table class="patients-table">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Statut</th>
                            <th>Dernier contrôle</th>
                            <th>Signes vitaux</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="patient-info">
                                <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Patient">
                                <div>
                                    <p class="name">Robert Dupont</p>
                                    <p class="patient-id">ID: 24601</p>
                                </div>
                            </td>
                            <td><span class="status status-stable">Stable</span></td>
                            <td>Aujourd'hui, 10:30</td>
                            <td class="vital-signs">
                                <span class="vital"><i class="fas fa-heartbeat"></i> 85 bpm</span>
                                <span class="vital"><i class="fas fa-thermometer-half"></i> 37.2°C</span>
                            </td>
                            <td>
                                <button class="action-btn"><i class="fas fa-eye"></i></button>
                                <button class="action-btn"><i class="fas fa-notes-medical"></i></button>
                            </td>
                        </tr>
                        <tr id="alertPatient">
                            <td class="patient-info">
                                <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Patient">
                                <div>
                                    <p class="name">Émilie Carter</p>
                                    <p class="patient-id">ID: 32145</p>
                                </div>
                            </td>
                            <td><span class="status status-alert">Alerte</span></td>
                            <td>Aujourd'hui, 09:15</td>
                            <td class="vital-signs">
                                <span class="vital vital-alert"><i class="fas fa-heartbeat"></i> 120 bpm</span>
                                <span class="vital"><i class="fas fa-thermometer-half"></i> 38.1°C</span>
                            </td>
                            <td>
                                <button class="action-btn" id="viewAlertPatient"><i class="fas fa-eye"></i></button>
                                <button class="action-btn"><i class="fas fa-notes-medical"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td class="patient-info">
                                <img src="https://randomuser.me/api/portraits/men/67.jpg" alt="Patient">
                                <div>
                                    <p class="name">Michel Lefebvre</p>
                                    <p class="patient-id">ID: 54378</p>
                                </div>
                            </td>
                            <td><span class="status status-stable">Stable</span></td>
                            <td>Hier, 16:45</td>
                            <td class="vital-signs">
                                <span class="vital"><i class="fas fa-heartbeat"></i> 72 bpm</span>
                                <span class="vital"><i class="fas fa-thermometer-half"></i> 36.8°C</span>
                            </td>
                            <td>
                                <button class="action-btn"><i class="fas fa-eye"></i></button>
                                <button class="action-btn"><i class="fas fa-notes-medical"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Right Sidebar - Recent Activity and Quick Actions -->
        <div class="right-sidebar">
            <!-- Recent Activity -->
            <section class="data-card activity-container">
                <div class="card-header">
                    <h2><i class="icon-activity"></i> Activité récente</h2>
                    <a href="{{ route('history.index') }}" class="view-all-btn">Voir tout</a>
                </div>
                <div class="activity-list">
                    @forelse($recentActivities ?? [] as $activity)
                        <div class="activity-item">
                            <div class="activity-icon {{ $activity->type }}"><i class="icon-{{ $activity->icon }}"></i></div>
                            <div class="activity-details">
                                <div class="activity-title">{{ $activity->title }}</div>
                                <div class="activity-desc">{{ $activity->description }}</div>
                                <div class="activity-time">{{ $activity->time }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="activity-item">
                            <div class="activity-icon alert"><i class="icon-alert"></i></div>
                            <div class="activity-details">
                                <div class="activity-title">Alerte - Fréquence cardiaque</div>
                                <div class="activity-desc">Émilie Carter - 120 bpm détecté</div>
                                <div class="activity-time">Il y a 5 min</div>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon update"><i class="icon-update"></i></div>
                            <div class="activity-details">
                                <div class="activity-title">Mise à jour capteur</div>
                                <div class="activity-desc">Thomas Dubois - Capteur de température recalibré</div>
                                <div class="activity-time">Il y a 30 min</div>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon note"><i class="icon-note"></i></div>
                            <div class="activity-details">
                                <div class="activity-title">Note ajoutée</div>
                                <div class="activity-desc">Dr. Martin a ajouté une note au dossier de Robert Dupont</div>
                                <div class="activity-time">Il y a 45 min</div>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-icon patient"><i class="icon-patient-add"></i></div>
                            <div class="activity-details">
                                <div class="activity-title">Nouveau patient</div>
                                <div class="activity-desc">Pierre Lambert ajouté au système</div>
                                <div class="activity-time">Il y a 2h</div>
                            </div>
                        </div>
                    @endforelse
                </div>
            </section>

            <!-- Quick Actions -->
            <section class="data-card quick-actions-container">
                <div class="card-header">
                    <h2><i class="icon-quick-actions"></i> Actions rapides</h2>
                </div>
                <div class="quick-actions-grid">
                    <a href="{{ route('patients.create') }}" class="quick-action-btn">
                        <i class="icon-add-patient"></i>
                        <span>Ajouter patient</span>
                    </a>
                    <a href="{{ route('sensors.create') }}" class="quick-action-btn">
                        <i class="icon-add-sensor"></i>
                        <span>Configurer capteur</span>
                    </a>
                    <a href="{{ route('reports.generate') }}" class="quick-action-btn">
                        <i class="icon-generate-report"></i>
                        <span>Générer rapport</span>
                    </a>
                    <a href="{{ route('alertes.settings') }}" class="quick-action-btn">
                        <i class="icon-manage-alerts"></i>
                        <span>Gérer alertes</span>
                    </a>
                </div>
            </section>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/bord.js') }}"></script>
@endsection