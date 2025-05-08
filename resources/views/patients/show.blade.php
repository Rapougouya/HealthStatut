@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/patient1.css') }}">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

@endsection

@section('content')
<div class="patient-header">
    <button style="background-color: #3498db; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-size: 18px; font-weight: bold; display: inline-flex; align-items: center; gap: 8px;">
    <a href="{{ route('patients.liste') }}" style="color: white; text-decoration: none; display: inline-flex; align-items: center; font-weight: bold; font-size: 18px;">
        <i class="icon-back" style="margin-right: 6px;"></i> Liste des patients
    </a>
</button>

    <div class="patient-profile">
        @if ($patient)
        <div class="patient-avatar">
            <img src="{{ $patient->avatar ? asset('storage/'.$patient->avatar) : asset('images/patients/default.jpg') }}" 
                 alt="{{ $patient->prenom.' '.$patient->nom }}">
        </div>

        <div class="patient-info">
            <h2>{{ $patient->prenom }} {{ $patient->nom }}</h2>
            <div class="patient-meta">
                <span class="patient-id">ID: {{ $patient->id }}</span>
                <span class="patient-email">{{ $patient->email }}</span>
                <span class="patient-status">Statut: {{ ucfirst($patient->statut) }}</span>
            </div>
        </div>


            <div class="patient-actions">
                <a href="{{ route('patients.edit', $patient->id) }}" class="action-btn edit">
                    <i class="icon-edit"></i> Modifier
                </a>
                <a href="{{ route('rapports.create', ['patient_id' => $patient->id]) }}" class="action-btn report">
                    <i class="icon-report"></i> Rapport
                </a>
                <button class="action-btn contact">
                    <i class="icon-contact"></i> Contact
                </button>
            </div>
        @else
            <p class="no-patient">Aucun patient trouvé.</p>
        @endif
    </div>
</div>

@if ($patient)
<div class="patient-content">
    <!-- Signes vitaux -->
    <section class="vital-signs-section section-card">
        <div class="section-header">
            <h3><i class="icon-vitals"></i> Signes vitaux</h3>
            <div class="section-actions">
                <span class="last-updated">
                    Dernière mise à jour :
                    @if ($lastVitalUpdate)
                        {{ $lastVitalUpdate->diffForHumans() }}
                    @else
                        Aucune mise à jour disponible
                    @endif
                </span>                
                <button class="refresh-btn"><i class="icon-refresh"></i></button>
            </div>
        </div>

        <div class="vital-signs-grid">
            @foreach($vitalSigns as $type => $sign)
                <div class="vital-card {{ $sign['alert'] ? 'alert' : '' }}">
                    <div class="vital-icon"><i class="icon-{{ $sign['icon'] }}"></i></div>
                    <div class="vital-info">
                        <div class="vital-value">{{ $sign['value'] }} <span class="vital-unit">{{ $sign['unit'] }}</span></div>
                        <div class="vital-name">{{ $sign['name'] }}</div>
                    </div>
                    @if($sign['trend'])
                        <div class="vital-trend {{ $sign['trend'] > 0 ? 'up' : 'down' }}">
                            <i class="icon-{{ $sign['trend'] > 0 ? 'trend-up' : 'trend-down' }}"></i>
                            {{ abs($sign['trend']) }}%
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </section>

    <!-- Historique graphique -->
    <section class="charts-section section-card">
        <div class="section-header">
            <h3><i class="icon-chart"></i> Historique des signes vitaux</h3>
            <div class="section-actions">
                <div class="period-selector">
                    <button class="period-btn active" data-period="day">Jour</button>
                    <button class="period-btn" data-period="week">Semaine</button>
                    <button class="period-btn" data-period="month">Mois</button>
                </div>
            </div>
        </div>

        <div class="chart-tabs">
            <div class="chart-tab-nav">
                <button class="chart-tab-btn active" data-chart="heart-rate">Rythme cardiaque</button>
                <button class="chart-tab-btn" data-chart="temperature">Température</button>
                <button class="chart-tab-btn" data-chart="oxygen">Saturation O₂</button>
                <button class="chart-tab-btn" data-chart="blood-pressure">Pression artérielle</button>
            </div>

            <div class="chart-content">
                @foreach(['heart-rate' => 'heart_rate', 'temperature' => 'temperature', 'oxygen' => 'saturation_oxygene', 'blood-pressure' => 'pression_arterielle'] as $id => $type)
                    <div class="chart-container {{ $loop->first ? 'active' : '' }}" 
                         id="{{ $id }}-chart" 
                         data-type="{{ $type }}" 
                         data-patient-id="{{ $patient->id }}">
                        <!-- Graphique chargé par JS -->
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Informations patient + Capteurs -->
    <div class="patient-details-row">
        <section class="patient-details-section section-card">
            <div class="section-header">
                <h3><i class="icon-details"></i> Informations du patient</h3>
            </div>
            <div class="details-grid">
                <x-patient.detail label="Nom complet" :value="$patient->nom_complet" />
                <x-patient.detail label="Date de naissance" :value="$patient->date_naissance->format('d/m/Y')" />
                <x-patient.detail label="Téléphone" :value="$patient->telephone ?? 'Non spécifié'" />
                <x-patient.detail label="Email" :value="$patient->email ?? 'Non spécifié'" />
                <x-patient.detail label="Adresse" :value="$patient->adresse ?? 'Non spécifiée'" />
                <x-patient.detail label="Taille" :value="$patient->taille ? $patient->taille . ' cm' : 'Non spécifiée'" />
                <x-patient.detail label="Poids" :value="$patient->poids ? $patient->poids . ' kg' : 'Non spécifié'" />
                <x-patient.detail label="Service" :value="$patient->service->nom ?? 'Non assigné'" />
            </div>
        </section>

        <section class="sensors-section section-card">
            <div class="section-header">
                <h3><i class="icon-sensors"></i> Capteurs associés</h3>
                <div class="section-actions">
                    <a href="{{ route('capteurs.create', ['patient_id' => $patient->id]) }}" class="add-sensor-btn">
                        <i class="icon-add-sensor"></i> Ajouter
                    </a>
                </div>
            </div>
            <div class="sensors-list">
                @forelse($patient->capteurs as $capteur)
                    <div class="sensor-item {{ $capteur->statut === 'actif' ? 'active' : 'inactive' }}">
                        <div class="sensor-icon"><i class="icon-{{ $capteur->type }}"></i></div>
                        <div class="sensor-info">
                            <div class="sensor-name">{{ $capteur->nom }}</div>
                            <div class="sensor-meta">
                                <span class="sensor-id">ID: {{ $capteur->numero_serie }}</span>
                                <span class="battery-level">Batterie: {{ $capteur->niveau_batterie }}%</span>
                            </div>
                        </div>
                        <div class="sensor-status">
                            <span class="status-indicator"></span>
                            {{ ucfirst($capteur->statut) }}
                        </div>
                        <div class="sensor-actions">
                            <button class="action-btn config" data-sensor-id="{{ $capteur->id }}">
                                <i class="icon-settings"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="no-sensors">
                        <p>Aucun capteur associé à ce patient</p>
                        <a href="{{ route('capteurs.create', ['patient_id' => $patient->id]) }}" class="add-sensor-link">
                            Ajouter un capteur
                        </a>
                    </div>
                @endforelse
            </div>
        </section>
    </div>

    <!-- Alertes -->
    <div class="alerts-notes-row">
        <section class="alerts-section section-card">
            <div class="section-header">
                <h3><i class="icon-alerts"></i> Historique des alertes</h3>
                <div class="section-actions">
                    <a href="{{ route('alertes.index', ['patient_id' => $patient->id]) }}" class="view-all-btn">
                        Voir tout
                    </a>
                </div>
            </div>
            <div class="alerts-list">
                @forelse($patient->alertes()->latest()->take(5)->get() as $alerte)
                    <div class="alert-item {{ $alerte->severite }}">
                        <div class="alert-header">
                            <div class="alert-type">
                                <i class="icon-{{ $alerte->capteur->type }}"></i> {{ $alerte->type }}
                            </div>
                            <div class="alert-time">{{ $alerte->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                        <div class="alert-message">{{ $alerte->message }}</div>
                    </div>
                @empty
                    <p>Aucune alerte récente.</p>
                @endforelse
            </div>
        </section>
    </div>
</div>
@endif
@endsection
@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/patient.js') }}"></script>
@endsection