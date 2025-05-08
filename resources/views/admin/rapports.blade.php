@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/rapports.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('content')
<div class="reports-container">

    <h1 class="page-title"><i class="fas fa-file-medical"></i> Rapports des Patients</h1>

    <!-- Filtres des rapports -->
    <section class="reports-filter">
        <h2><i class="fas fa-filter"></i> Filtres des rapports</h2>
        <div class="filter-form">
            <form action="{{ route('rapports.index') }}" method="GET" id="filterForm">
                <div class="filter-row">
                    <div class="filter-group">
                        <label for="patient-search"><i class="fas fa-user"></i> Patient</label>
                        <input type="text" id="patient-search" name="patient_search" placeholder="Rechercher un patient..." value="{{ request('patient_search') }}">
                        <div id="patient-search-results" class="search-results"></div>
                    </div>
                    <div class="filter-group">
                        <label for="report-type"><i class="fas fa-file-alt"></i> Type de rapport</label>
                        <select id="report-type" name="report_type">
                            <option value="all" {{ request('report_type') == 'all' ? 'selected' : '' }}>Tous les types</option>
                            <option value="medical" {{ request('report_type') == 'medical' ? 'selected' : '' }}>Médical</option>
                            <option value="vitals" {{ request('report_type') == 'vitals' ? 'selected' : '' }}>Signes vitaux</option>
                            <option value="lab" {{ request('report_type') == 'lab' ? 'selected' : '' }}>Résultats de laboratoire</option>
                            <option value="prescription" {{ request('report_type') == 'prescription' ? 'selected' : '' }}>Prescriptions</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="date-range"><i class="far fa-calendar-alt"></i> Période</label>
                        <select id="date-range" name="date_range">
                            <option value="all" {{ request('date_range') == 'all' ? 'selected' : '' }}>Toute la période</option>
                            <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Aujourd'hui</option>
                            <option value="week" {{ request('date_range') == 'week' ? 'selected' : '' }}>Cette semaine</option>
                            <option value="month" {{ request('date_range') == 'month' ? 'selected' : '' }}>Ce mois</option>
                            <option value="year" {{ request('date_range') == 'year' ? 'selected' : '' }}>Cette année</option>
                            <option value="custom" {{ request('date_range') == 'custom' ? 'selected' : '' }}>Personnalisé</option>
                        </select>
                    </div>
                </div>
                
                <div class="date-custom-row" id="date-custom-row" style="{{ request('date_range') == 'custom' ? '' : 'display: none;' }}">
                    <div class="filter-group">
                        <label for="date-from"><i class="far fa-calendar"></i> Du</label>
                        <input type="date" id="date-from" name="date_from" value="{{ request('date_from') }}">
                    </div>
                    <div class="filter-group">
                        <label for="date-to"><i class="far fa-calendar"></i> Au</label>
                        <input type="date" id="date-to" name="date_to" value="{{ request('date_to') }}">
                    </div>
                </div>
                
                <div class="filter-actions">
                    <button type="submit" class="action-btn primary" id="apply-filters">
                        <i class="fas fa-search"></i> Appliquer
                    </button>
                    <a href="{{ route('rapports.index') }}" class="action-btn" id="reset-filters">
                        <i class="fas fa-undo"></i> Réinitialiser
                    </a>
                    <button type="button" class="action-btn" id="scan-qr">
                        <i class="fas fa-qrcode"></i> Scanner QR Code
                    </button>
                </div>
            </form>
        </div>
    </section>
    
    <!-- Résultats -->
    <section class="reports-results">
        <div class="section-header">
            <h2><i class="fas fa-file-medical-alt"></i> Résultats</h2>
            <div class="results-actions">
                @if($selectedPatient)
                <button class="action-btn" id="export-csv" data-patient-id="{{ $selectedPatient->id }}">
                    <i class="fas fa-file-csv"></i> Exporter CSV
                </button>
                <button class="action-btn" id="export-pdf" data-patient-id="{{ $selectedPatient->id }}">
                    <i class="fas fa-file-pdf"></i> Exporter PDF
                </button>
                <button class="action-btn" id="generate-qr" data-patient-id="{{ $selectedPatient->id }}">
                    <i class="fas fa-qrcode"></i> Générer QR Code
                </button>
                @else
                <button class="action-btn disabled" disabled>
                    <i class="fas fa-file-csv"></i> Exporter CSV
                </button>
                <button class="action-btn disabled" disabled>
                    <i class="fas fa-file-pdf"></i> Exporter PDF
                </button>
                <button class="action-btn disabled" disabled>
                    <i class="fas fa-qrcode"></i> Générer QR Code
                </button>
                @endif
            </div>
        </div>
        
        @if(!$selectedPatient)
        <div class="no-patient-selected" id="no-patient-message">
            <i class="fas fa-user-md fa-3x"></i>
            <p>Veuillez sélectionner un patient pour afficher ses rapports</p>
        </div>
        @else
        <div class="patient-data" id="patient-data">
            <div class="patient-header">
                <div class="patient-info">
                    <h3 id="patient-name"><i class="fas fa-user-circle"></i> {{ $selectedPatient->nom_complet }}</h3>
                    <div class="patient-details">
                        <span id="patient-id"><i class="fas fa-id-card"></i> ID: {{ $selectedPatient->numero_dossier }}</span>
                        <span id="patient-age"><i class="fas fa-birthday-cake"></i> Âge: {{ $selectedPatient->age }} ans</span>
                        <span id="patient-gender"><i class="fas fa-venus-mars"></i> Sexe: {{ $selectedPatient->sexe == 'M' ? 'Masculin' : 'Féminin' }}</span>
                    </div>
                </div>
                <div class="patient-actions">
                    <span class="badge-status {{ $selectedPatient->statut }}" id="patient-status">
                        @if($selectedPatient->statut == 'active')
                        <i class="fas fa-check-circle"></i> Actif
                        @else
                        <i class="fas fa-times-circle"></i> Inactif
                        @endif
                    </span>
                </div>
            </div>
            
            <div class="reports-tabs">
                <button class="tab-btn active" data-tab="overview"><i class="fas fa-chart-pie"></i> Vue d'ensemble</button>
                <button class="tab-btn" data-tab="vitals"><i class="fas fa-heartbeat"></i> Signes vitaux</button>
                <button class="tab-btn" data-tab="lab"><i class="fas fa-flask"></i> Laboratoire</button>
                <button class="tab-btn" data-tab="prescriptions"><i class="fas fa-prescription-bottle-alt"></i> Prescriptions</button>
                <button class="tab-btn" data-tab="history"><i class="fas fa-history"></i> Historique</button>
            </div>
            
            <div class="tab-content active" id="overview-tab">
                <div class="overview-metrics">
                    @if($dernierSigneVital)
                    <div class="metric-card">
                        <div class="metric-icon heart-rate">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                        <div class="metric-data">
                            <h4>Fréquence cardiaque</h4>
                            <div class="metric-value">{{ $dernierSigneVital->rythme_cardiaque }} <span>bpm</span></div>
                            <div class="metric-range"><i class="fas fa-info-circle"></i> Normal: 60-100</div>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon blood-pressure">
                            <i class="fas fa-tachometer-alt"></i>
                        </div>
                        <div class="metric-data">
                            <h4>Pression artérielle</h4>
                            <div class="metric-value">{{ $dernierSigneVital->pression_arterielle }} <span>mmHg</span></div>
                            <div class="metric-range"><i class="fas fa-info-circle"></i> Normal: 90-120/60-80</div>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon temperature">
                            <i class="fas fa-thermometer-half"></i>
                        </div>
                        <div class="metric-data">
                            <h4>Température</h4>
                            <div class="metric-value">{{ $dernierSigneVital->temperature }} <span>°C</span></div>
                            <div class="metric-range"><i class="fas fa-info-circle"></i> Normal: 36.1-37.2</div>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon oxygen">
                            <i class="fas fa-lungs"></i>
                        </div>
                        <div class="metric-data">
                            <h4>Saturation en O2</h4>
                            <div class="metric-value">{{ $dernierSigneVital->saturation_oxygene }} <span>%</span></div>
                            <div class="metric-range"><i class="fas fa-info-circle"></i> Normal: 95-100</div>
                        </div>
                    </div>
                    @else
                    <div class="no-metrics">
                        <i class="fas fa-chart-line fa-2x"></i>
                        <p>Aucune donnée récente disponible pour ce patient</p>
                    </div>
                    @endif
                </div>
                
                <div class="recent-reports">
                    <h3><i class="fas fa-clock"></i> Rapports récents</h3>
                    <div class="reports-timeline">
                        @forelse($rapportsRecents as $rapport)
                        <div class="timeline-item">
                            <div class="timeline-date">
                                <div class="date-day">{{ $rapport->created_at->format('d') }}</div>
                                <div class="date-month">{{ $rapport->created_at->format('M') }}</div>
                            </div>
                            <div class="timeline-content">
                                <h4>{{ $rapport->titre }}</h4>
                                <p>{{ $rapport->description }}</p>
                                <div class="timeline-meta">
                                    <span><i class="{{ $rapport->type_icon }}"></i> {{ $rapport->source }}</span>
                                    <a href="{{ route('rapports.show', $rapport->id) }}" class="view-report-btn"><i class="fas fa-eye"></i> Voir</a>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="no-reports-message">
                            <i class="fas fa-file-alt fa-2x"></i>
                            <p>Aucun rapport récent disponible pour ce patient</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
            
            <div class="tab-content" id="vitals-tab">
                <div class="chart-container">
                    <canvas id="vitalsChart"></canvas>
                </div>
                <div class="vitals-history">
                    <h3><i class="fas fa-history"></i> Historique des signes vitaux</h3>
                    <table class="vitals-table">
                        <thead>
                            <tr>
                                <th><i class="far fa-calendar"></i> Date</th>
                                <th><i class="fas fa-heartbeat"></i> FC</th>
                                <th><i class="fas fa-tachometer-alt"></i> PA</th>
                                <th><i class="fas fa-thermometer-half"></i> Temp</th>
                                <th><i class="fas fa-lungs"></i> SpO2</th>
                                <th><i class="fas fa-weight"></i> Poids</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($signesVitaux as $signe)
                            <tr>
                                <td>{{ $signe->created_at->format('d/m/Y') }}</td>
                                <td>{{ $signe->rythme_cardiaque }} bpm</td>
                                <td>{{ $signe->pression_arterielle }} mmHg</td>
                                <td>{{ $signe->temperature }} °C</td>
                                <td>{{ $signe->saturation_oxygene }} %</td>
                                <td>{{ $signe->poids }} kg</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="no-data">
                                    <i class="fas fa-database"></i> Aucun historique disponible
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="tab-content" id="lab-tab">
                <div class="lab-filters">
                    <select id="lab-type-filter">
                        <option value="all"><i class="fas fa-vial"></i> Tous les tests</option>
                        <option value="blood"><i class="fas fa-tint"></i> Sang</option>
                        <option value="urine"><i class="fas fa-flask"></i> Urine</option>
                        <option value="imaging"><i class="fas fa-x-ray"></i> Imagerie</option>
                    </select>
                </div>
                <div class="lab-results">
                    @forelse($laboratoires as $labo)
                    <div class="lab-card" data-type="{{ $labo->type }}">
                        <div class="lab-header">
                            <h4><i class="fas fa-vial"></i> {{ $labo->titre }}</h4>
                            <span class="lab-date"><i class="far fa-calendar-alt"></i> {{ $labo->date->format('d/m/Y') }}</span>
                        </div>
                        <div class="lab-body">
                            <table class="lab-values">
                                @foreach($labo->resultats as $resultat)
                                <tr>
                                    <td><i class="fas fa-microscope"></i> {{ $resultat->parametre }}</td>
                                    <td>{{ $resultat->valeur }} {{ $resultat->unite }}</td>
                                    <td>
                                        @if($resultat->statut == 'normal')
                                        <span class="normal"><i class="fas fa-check-circle"></i> Normal</span>
                                        @elseif($resultat->statut == 'abnormal')
                                        <span class="abnormal"><i class="fas fa-exclamation-circle"></i> Anormal</span>
                                        @else
                                        <span class="warning"><i class="fas fa-exclamation-triangle"></i> Avertissement</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </table>
                        </div>
                        <div class="lab-footer">
                            <span><i class="fas fa-hospital"></i> {{ $labo->laboratoire }}</span>
                            <a href="{{ route('laboratoires.show', $labo->id) }}" class="view-details-btn"><i class="fas fa-arrow-right"></i> Détails</a>
                        </div>
                    </div>
                    @empty
                    <div class="no-lab-results">
                        <i class="fas fa-vial fa-2x"></i>
                        <p>Aucun résultat de laboratoire disponible</p>
                    </div>
                    @endforelse
                </div>
            </div>
            
            <div class="tab-content" id="prescriptions-tab">
                <div class="prescriptions-list">
                    @forelse($prescriptions as $prescription)
                    <div class="prescription-card">
                        <div class="prescription-header">
                            <h4><i class="fas fa-prescription-bottle-alt"></i> {{ $prescription->categorie }}</h4>
                            <span class="prescription-status {{ $prescription->statut }}">
                                @if($prescription->statut == 'active')
                                <i class="fas fa-spinner"></i> En cours
                                @else
                                <i class="fas fa-check"></i> Terminé
                                @endif
                            </span>
                        </div>
                        <div class="prescription-body">
                            <p class="medication-name"><i class="fas fa-pills"></i> {{ $prescription->medicament }}</p>
                            <p class="medication-instruction"><i class="fas fa-info-circle"></i> {{ $prescription->posologie }}</p>
                            <div class="prescription-dates">
                                <span><i class="far fa-calendar-check"></i> Début: {{ $prescription->date_debut->format('d/m/Y') }}</span>
                                <span><i class="far fa-calendar-times"></i> Fin: {{ $prescription->date_fin->format('d/m/Y') }}</span>
                            </div>
                        </div>
                        <div class="prescription-footer">
                            <span><i class="fas fa-user-md"></i> {{ $prescription->medecin }}</span>
                            <a href="{{ route('prescriptions.show', $prescription->id) }}" class="view-details-btn"><i class="fas fa-arrow-right"></i> Détails</a>
                        </div>
                    </div>
                    @empty
                    <div class="no-prescriptions">
                        <i class="fas fa-prescription-bottle-alt fa-2x"></i>
                        <p>Aucune prescription disponible</p>
                    </div>
                    @endforelse
                </div>
            </div>
            
            <div class="tab-content" id="history-tab">
                <div class="medical-history">
                    <div class="history-section">
                        <h3><i class="fas fa-notes-medical"></i> Antécédents médicaux</h3>
                        <ul class="history-list">
                            @forelse($antecedents as $antecedent)
                            <li>
                                <span class="history-date">{{ $antecedent->annee }}</span>
                                <div class="history-item">
                                    <h4><i class="fas fa-file-medical"></i> {{ $antecedent->titre }}</h4>
                                    <p>{{ $antecedent->description }}</p>
                                </div>
                            </li>
                            @empty
                            <li class="no-data">
                                <i class="fas fa-file-medical"></i> Aucun antécédent médical enregistré
                            </li>
                            @endforelse
                        </ul>
                    </div>
                    
                    <div class="history-section">
                        <h3><i class="fas fa-allergies"></i> Allergies</h3>
                        <ul class="allergy-list">
                            @forelse($allergies as $allergie)
                            <li>
                                <div class="allergy-item">
                                    <span class="allergy-icon"><i class="fas fa-allergies"></i></span>
                                    <div class="allergy-details">
                                        <h4>{{ $allergie->substance }}</h4>
                                        <p>{{ $allergie->reaction }}</p>
                                    </div>
                                    <span class="allergy-severity {{ $allergie->severite }}">
                                        @if($allergie->severite == 'high')
                                        <i class="fas fa-exclamation-triangle"></i> Élevée
                                        @elseif($allergie->severite == 'medium')
                                        <i class="fas fa-exclamation-circle"></i> Moyenne
                                        @else
                                        <i class="fas fa-info-circle"></i> Faible
                                        @endif
                                    </span>
                                </div>
                            </li>
                            @empty
                            <li class="no-data">
                                <i class="fas fa-allergies"></i> Aucune allergie enregistrée
                            </li>
                            @endforelse
                        </ul>
                    </div>
                    
                    <div class="history-section">
                        <h3><i class="fas fa-sticky-note"></i> Notes médicales</h3>
                        <div class="notes-list">
                            @forelse($notes as $note)
                            <div class="note-card">
                                <div class="note-header">
                                    <h4><i class="fas fa-file-alt"></i> {{ $note->titre }}</h4>
                                    <span class="note-date"><i class="far fa-calendar-alt"></i> {{ $note->created_at->format('d/m/Y') }}</span>
                                </div>
                                <div class="note-body">
                                    <p>{{ $note->contenu }}</p>
                                </div>
                                <div class="note-footer">
                                    <span><i class="fas fa-user-md"></i> {{ $note->medecin }}</span>
                                </div>
                            </div>
                            @empty
                            <div class="no-data">
                                <i class="fas fa-sticky-note"></i> Aucune note médicale enregistrée
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </section>
</div>

<!-- Modals -->
<div class="modal-overlay" id="qr-scan-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-qrcode"></i> Scanner un code QR</h3>
            <button class="close-modal"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div class="qr-scanner-container">
                <div id="scanner-placeholder">
                    <i class="fas fa-qrcode fa-4x"></i>
                    <p>Caméra en cours d'initialisation...</p>
                </div>
                <video id="qr-video" style="display: none;"></video>
            </div>
            <div class="scanner-instructions">
                <p><i class="fas fa-info-circle"></i> Pointez votre appareil photo vers le code QR du patient pour accéder à ses informations.</p>
            </div>
        </div>
    </div>
</div>

<div class="modal-overlay" id="qr-generate-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-qrcode"></i> Code QR du patient</h3>
            <button class="close-modal"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div class="generated-qr-container">
                <div id="qrcode"></div>
                <div class="qr-patient-info">
                    <h4 id="qr-patient-name"><i class="fas fa-user"></i> {{ $selectedPatient->nom_complet ?? 'Nom du patient' }}</h4>
                    <p id="qr-patient-id"><i class="fas fa-id-card"></i> ID: {{ $selectedPatient->numero_dossier ?? 'P-12345' }}</p>
                </div>
            </div>
            <div class="qr-actions">
                <button class="action-btn" id="download-qr">
                    <i class="fas fa-download"></i> Télécharger
                </button>
                <button class="action-btn" id="print-qr">
                    <i class="fas fa-print"></i> Imprimer
                </button>
            </div>
            <div class="qr-instructions">
                <p><i class="fas fa-info-circle"></i> Ce code QR contient un identifiant unique pour ce patient. Il peut être scanné pour accéder rapidement à ses informations médicales.</p>
            </div>
        </div>
    </div>
</div>

<!-- Toast notifications -->
<div class="toast-container"></div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/qrcode-generator/qrcode.js"></script>
<script src="https://cdn.jsdelivr.net/npm/html5-qrcode/html5-qrcode.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="{{ asset('js/rapports.js') }}"></script>
@endsection
