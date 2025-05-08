@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/capteurs.css') }}">
@endsection

@section('content')
<!-- Stats Grid -->
<section class="stats-grid">
    <div class="stats-card">
        <i class="icon-sensor"></i>
        <h3>Total Capteurs</h3>
        <p class="value">{{ $statsData['total'] ?? 321 }}</p>
        <p class="trend up">+{{ $statsData['nouveaux'] ?? 15 }} <span>ce mois</span></p>
    </div>
    <div class="stats-card">
        <i class="icon-connection"></i>
        <h3>Capteurs actifs</h3>
        <p class="value">{{ $statsData['actifs'] ?? 298 }}</p>
        <p class="trend up">{{ $statsData['tauxConnection'] ?? 93 }}% <span>taux de connexion</span></p>
    </div>
    <div class="stats-card">
        <i class="icon-battery"></i>
        <h3>Batterie faible</h3>
        <p class="value">{{ $statsData['batterieFaible'] ?? 12 }}</p>
        <p class="trend down">{{ $statsData['batterieTendance'] ?? -3 }} <span>vs semaine dernière</span></p>
    </div>
    <div class="stats-card">
        <i class="icon-maintenance"></i>
        <h3>En maintenance</h3>
        <p class="value">{{ $statsData['maintenance'] ?? 8 }}</p>
        <p class="trend neutral">{{ $statsData['maintenanceTendance'] ?? 0 }} <span>changement</span></p>
    </div>
</section>

<!-- Sensors Management -->
<div class="content-grid">
    <!-- Sensors Table -->
    <section class="data-card sensors-container">
        <div class="card-header">
            <h2><i class="icon-all-sensors"></i> Liste des capteurs</h2>
            <div class="card-actions">
                <div class="search-container">
                    <form action="{{ route('capteurs.index') }}" method="GET">
                        <input type="text" name="search" placeholder="Rechercher un capteur..." class="search-input" value="{{ request('search') }}">
                        <button type="submit" class="search-btn"><i class="icon-search"></i></button>
                    </form>
                </div>
                <a href="{{ route('capteurs.create') }}" class="primary-btn">
                    <i class="icon-add"></i> Ajouter un capteur
                </a>
            </div>
        </div>
        
        <div class="filter-bar">
            <form action="{{ route('capteurs.index') }}" method="GET" id="filterForm">
                <input type="hidden" name="search" value="{{ request('search') }}">
                
                <select name="type" class="filter-select" onchange="document.getElementById('filterForm').submit()">
                    <option value="">Tous les types</option>
                    <option value="cardiac" {{ request('type') == 'cardiac' ? 'selected' : '' }}>Cardiaque</option>
                    <option value="temperature" {{ request('type') == 'temperature' ? 'selected' : '' }}>Température</option>
                    <option value="oximeter" {{ request('type') == 'oximeter' ? 'selected' : '' }}>Oxymétrie</option>
                    <option value="blood-pressure" {{ request('type') == 'blood-pressure' ? 'selected' : '' }}>Tension artérielle</option>
                    <option value="glucose" {{ request('type') == 'glucose' ? 'selected' : '' }}>Glucose</option>
                </select>
                
                <select name="statut" class="filter-select" onchange="document.getElementById('filterForm').submit()">
                    <option value="">Tous les statuts</option>
                    <option value="active" {{ request('statut') == 'active' ? 'selected' : '' }}>Actif</option>
                    <option value="inactive" {{ request('statut') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                    <option value="maintenance" {{ request('statut') == 'maintenance' ? 'selected' : '' }}>En maintenance</option>
                    <option value="low-battery" {{ request('statut') == 'low-battery' ? 'selected' : '' }}>Batterie faible</option>
                </select>
                
                <button type="submit" class="filter-btn">Filtrer</button>
                <button type="button" class="filter-reset" onclick="window.location.href='{{ route('capteurs.index') }}'">Réinitialiser</button>
            </form>
        </div>
        
        <div class="table-wrapper">
            <table class="sensors-table">
                <thead>
                    <tr>
                        <th>ID Capteur</th>
                        <th>Type</th>
                        <th>Patient assigné</th>
                        <th>Statut</th>
                        <th>Batterie</th>
                        <th>Dernière mise à jour</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($capteurs as $capteur)
                    <tr>
                        <td><span class="sensor-id">{{ $capteur->numero_serie }}</span></td>
                        <td><span class="sensor-type {{ $capteur->type }}">{{ ucfirst($capteur->type_nom) }}</span></td>
                        <td class="patient-info">
                            @if($capteur->patients->isNotEmpty())
                                @foreach($capteur->patients as $patient)
                                    <img src="{{ asset('images/patients/' . $patient->id . '.jpg') }}" 
                                        onerror="this.src='{{ asset('images/patients/default.jpg') }}'" 
                                        alt="{{ $patient->nom_complet }}">
                                    <div>
                                        <p class="name">{{ $patient->nom_complet }}</p>
                                        <p class="patient-id">ID: {{ $patient->numero_dossier }}</p>
                                    </div>
                                @endforeach
                            @else
                                <span class="unassigned">Non assigné</span>
                            @endif
                        </td>
                        <td><span class="status status-{{ $capteur->statut }}">{{ ucfirst($capteur->statut_fr) }}</span></td>
                        <td>
                            <div class="battery-indicator">
                                <div class="battery-level {{ $capteur->niveau_batterie < 20 ? 'low' : '' }}" style="width: {{ $capteur->niveau_batterie }}%;"></div>
                            </div>
                            <span class="battery-percent">{{ $capteur->niveau_batterie }}%</span>
                        </td>
                        <td>{{ $capteur->derniere_transmission }}</td>
                        <td>
                            <a href="{{ route('capteurs.edit', $capteur->id) }}" class="action-btn">
                                <i class="icon-edit"></i>
                            </a>
                            <button class="action-btn configure-sensor" data-sensor-id="{{ $capteur->id }}">
                                <i class="icon-configure"></i>
                            </button>
                            <button class="action-btn view-history" data-sensor-id="{{ $capteur->id }}">
                                <i class="icon-history"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="no-data">
                            <div class="empty-state">
                                <i class="icon-no-sensors"></i>
                                <h3>Aucun capteur trouvé</h3>
                                <p>Il n'y a actuellement aucun capteur qui correspond à vos critères.</p>
                                <a href="{{ route('capteurs.create') }}" class="primary-btn">Ajouter un capteur</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="pagination">
            @if($capteurs->onFirstPage())
                <button class="page-btn prev disabled"><i class="icon-chevron-left"></i></button>
            @else
                <a href="{{ $capteurs->previousPageUrl() }}" class="page-btn prev"><i class="icon-chevron-left"></i></a>
            @endif
            
            @for($i = 1; $i <= $capteurs->lastPage(); $i++)
                @if($i == $capteurs->currentPage())
                    <span class="page-btn active">{{ $i }}</span>
                @elseif($i <= 3 || $i > $capteurs->lastPage() - 3 || abs($i - $capteurs->currentPage()) < 2)
                    <a href="{{ $capteurs->url($i) }}" class="page-btn">{{ $i }}</a>
                @elseif(abs($i - $capteurs->currentPage()) == 2)
                    <span class="page-ellipsis">...</span>
                @endif
            @endfor
            
            @if($capteurs->hasMorePages())
                <a href="{{ $capteurs->nextPageUrl() }}" class="page-btn next"><i class="icon-chevron-right"></i></a>
            @else
                <button class="page-btn next disabled"><i class="icon-chevron-right"></i></button>
            @endif
        </div>
    </section>

    <!-- Right Sidebar - Sensor Details -->
    <section class="data-card sensor-details" id="sensorDetailsSidebar">
        <div class="card-header">
            <h2><i class="icon-sensor-details"></i> Détails du capteur</h2>
            <button class="close-details-btn"><i class="icon-close"></i></button>
        </div>
        
        <div class="sensor-details-content">
            <!-- Le contenu sera chargé dynamiquement via JavaScript -->
            @if(isset($selectedSensor))
            <div class="sensor-image">
                <img src="{{ asset('images/sensors/' . $selectedSensor->type . '.png') }}" 
                     onerror="this.src='{{ asset('images/sensors/default.png') }}'" 
                     alt="{{ ucfirst($selectedSensor->type_nom) }}">
            </div>
            
            <div class="detail-group">
                <h3>Informations générales</h3>
                <div class="detail-item">
                    <span class="detail-label">ID Capteur:</span>
                    <span class="detail-value">{{ $selectedSensor->numero_serie }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Type:</span>
                    <span class="detail-value">{{ ucfirst($selectedSensor->type_nom) }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Modèle:</span>
                    <span class="detail-value">{{ $selectedSensor->modele }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Fabricant:</span>
                    <span class="detail-value">{{ $selectedSensor->fabricant }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Date d'installation:</span>
                    <span class="detail-value">{{ $selectedSensor->date_installation->format('d/m/Y') }}</span>
                </div>
            </div>
            
            <div class="detail-group">
                <h3>Patient assigné</h3>
                @if($selectedSensor->patient)
                <div class="assigned-patient">
                    <img src="{{ asset('images/patients/' . $selectedSensor->patient->id . '.jpg') }}" 
                         onerror="this.src='{{ asset('images/patients/default.jpg') }}'" 
                         alt="{{ $selectedSensor->patient->nom_complet }}">
                    <div>
                        <p class="name">{{ $selectedSensor->patient->nom_complet }}</p>
                        <p class="patient-id">ID: {{ $selectedSensor->patient->numero_dossier }}</p>
                        <p class="patient-info">{{ $selectedSensor->patient->age }} ans, Chambre {{ $selectedSensor->patient->chambre }}</p>
                    </div>
                </div>
                @else
                <div class="no-patient">
                    <p>Aucun patient assigné</p>
                    <button class="assign-patient-btn" data-sensor-id="{{ $selectedSensor->id }}">Assigner un patient</button>
                </div>
                @endif
            </div>
            
            <div class="detail-group">
                <h3>Paramètres et seuils</h3>
                <form action="{{ route('sensors.update', $selectedSensor->id) }}" method="POST" class="threshold-settings">
                    @csrf
                    @method('PUT')
                    
                    @if($selectedSensor->type == 'cardiac')
                    <div class="threshold-item">
                        <label for="heartRateMin">Fréquence cardiaque min:</label>
                        <div class="threshold-input">
                            <input type="number" id="heartRateMin" name="heart_rate_min" 
                                   value="{{ $selectedSensor->thresholds->heart_rate_min ?? 50 }}" 
                                   min="30" max="220">
                            <span class="unit">bpm</span>
                        </div>
                    </div>
                    <div class="threshold-item">
                        <label for="heartRateMax">Fréquence cardiaque max:</label>
                        <div class="threshold-input">
                            <input type="number" id="heartRateMax" name="heart_rate_max" 
                                   value="{{ $selectedSensor->thresholds->heart_rate_max ?? 120 }}" 
                                   min="30" max="220">
                            <span class="unit">bpm</span>
                        </div>
                    </div>
                    @endif
                    
                    <div class="threshold-item">
                        <label for="transmissionInterval">Intervalle de transmission:</label>
                        <div class="threshold-input">
                            <input type="number" id="transmissionInterval" name="transmission_interval" 
                                   value="{{ $selectedSensor->transmission_interval ?? 5 }}" 
                                   min="1" max="60">
                            <span class="unit">min</span>
                        </div>
                    </div>
                    <div class="threshold-item">
                        <label for="lowBatteryAlert">Alerte batterie faible:</label>
                        <div class="threshold-input">
                            <input type="number" id="lowBatteryAlert" name="low_battery_threshold" 
                                   value="{{ $selectedSensor->low_battery_threshold ?? 20 }}" 
                                   min="5" max="50">
                            <span class="unit">%</span>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="detail-group">
                <h3>Historique de maintenance</h3>
                <div class="maintenance-history">
                    @forelse($selectedSensor->maintenances as $maintenance)
                    <div class="maintenance-item">
                        <div class="maintenance-date">{{ $maintenance->date->format('d/m/Y') }}</div>
                        <div class="maintenance-desc">{{ $maintenance->description }}</div>
                    </div>
                    @empty
                    <div class="no-maintenance">
                        <p>Aucun historique de maintenance</p>
                    </div>
                    @endforelse
                </div>
            </div>
            
            <div class="sensor-actions">
                <button type="submit" form="sensor-thresholds-form" class="action-button primary">
                    <i class="icon-save"></i> Enregistrer les modifications
                </button>
                <button class="action-button secondary reset-sensor" data-sensor-id="{{ $selectedSensor->id }}">
                    <i class="icon-reset"></i> Réinitialiser le capteur
                </button>
                @if($selectedSensor->patient)
                <button class="action-button warning unassign-patient" data-sensor-id="{{ $selectedSensor->id }}">
                    <i class="icon-unassign"></i> Désassigner le patient
                </button>
                @endif
            </div>
            @else
            <div class="no-sensor-selected">
                <div class="empty-state">
                    <i class="icon-sensor-large"></i>
                    <h3>Aucun capteur sélectionné</h3>
                    <p>Sélectionnez un capteur pour voir ses détails.</p>
                </div>
            </div>
            @endif
        </div>
    </section>
</div>

<!-- Add Sensor Modal -->
<div id="addSensorModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Ajouter un nouveau capteur</h2>
            <button class="close-modal"><i class="icon-close"></i></button>
        </div>
        <div class="modal-body">
            <form id="addSensorForm" action="{{ route('capteurs.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="sensorId">ID du capteur</label>
                    <input type="text" id="sensorId" name="numero_serie" required>
                </div>
                <div class="form-group">
                    <label for="sensorType">Type de capteur</label>
                    <select id="sensorType" name="type" required>
                        <option value="">Sélectionner un type</option>
                        <option value="cardiac">Cardiaque</option>
                        <option value="temperature">Température</option>
                        <option value="oximeter">Oxymétrie</option>
                        <option value="blood-pressure">Tension artérielle</option>
                        <option value="glucose">Glucose</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="sensorModel">Modèle</label>
                    <input type="text" id="sensorModel" name="modele" required>
                </div>
                <div class="form-group">
                    <label for="sensorManufacturer">Fabricant</label>
                    <input type="text" id="sensorManufacturer" name="fabricant" required>
                </div>
                <div class="form-group">
                    <label for="patientAssign">Assigner à un patient (optionnel)</label>
                    <select id="patientAssign" name="patient_id">
                        <option value="">Aucun patient</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}">{{ $patient->nom_complet }} (ID: {{ $patient->numero_dossier }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-actions">
                    <button type="submit" class="primary-btn">Ajouter le capteur</button>
                    <button type="button" class="cancel-btn">Annuler</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/capteurs.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Variables pour les routes
        const routes = {
            sensorDetails: "{{ route('capteurs.show', '_ID_') }}",
            updateThresholds: "{{ route('sensors.update', '_ID_') }}",
            assignPatient: "{{ route('capteurs.assign-patient', '_ID_') }}",
            unassignPatient: "{{ route('capteurs.unassign-patient', '_ID_') }}",
            resetSensor: "{{ route('capteurs.reset', '_ID_') }}"
        };
        
        // Initialisation des événements et fonctionnalités
        initSensorDetailsSidebar();
        initAddSensorModal();
        initToasts();
        
        // Fonctions d'initialisation
        function initSensorDetailsSidebar() {
            const viewButtons = document.querySelectorAll('.view-history');
            const configureBtns = document.querySelectorAll('.configure-sensor');
            const sidebar = document.getElementById('sensorDetailsSidebar');
            const closeBtn = sidebar.querySelector('.close-details-btn');
            
            // Événements pour ouvrir et fermer la sidebar
            [...viewButtons, ...configureBtns].forEach(btn => {
                btn.addEventListener('click', function() {
                    const sensorId = this.dataset.sensorId;
                    loadSensorDetails(sensorId);
                });
            });
            
            closeBtn.addEventListener('click', function() {
                sidebar.classList.remove('active');
            });
        }
        
        function initAddSensorModal() {
            const openBtn = document.querySelector('.primary-btn');
            const modal = document.getElementById('addSensorModal');
            const closeBtn = modal.querySelector('.close-modal');
            const cancelBtn = modal.querySelector('.cancel-btn');
            
            openBtn.addEventListener('click', function(e) {
                if (e.target.tagName !== 'A') { // Si ce n'est pas un lien (pour éviter d'interférer avec le bouton "Ajouter un capteur")
                    e.preventDefault();
                    modal.classList.add('active');
                }
            });
            
            [closeBtn, cancelBtn].forEach(btn => {
                btn.addEventListener('click', function() {
                    modal.classList.remove('active');
                });
            });
        }
        
        function initToasts() {
            // Initialisation du système de notifications toast
            // Code pour gérer l'affichage des messages de succès, erreur, etc.
        }
        
        // Fonction pour charger les détails d'un capteur
        function loadSensorDetails(sensorId) {
            const url = routes.sensorDetails.replace('_ID_', sensorId);
            
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur lors du chargement des détails du capteur');
                }
                return response.json();
            })
            .then(data => {
                // Mettre à jour l'affichage des détails du capteur
                updateSensorDetails(data);
                
                // Afficher la sidebar
                document.getElementById('sensorDetailsSidebar').classList.add('active');
            })
            .catch(error => {
                console.error('Erreur:', error);
                // Afficher un message d'erreur
                showToast('error', 'Erreur', 'Impossible de charger les détails du capteur');
            });
        }
        
        // Fonction pour mettre à jour l'affichage des détails du capteur
        function updateSensorDetails(data) {
            // Code pour mettre à jour le contenu de la sidebar avec les détails du capteur
        }
        
        // Fonction pour afficher des messages toast
        function showToast(type, title, message) {
            const toastContainer = document.getElementById('toastContainer');
            
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            
            toast.innerHTML = `
                <div class="toast-icon">
                    <i class="icon-${type === 'error' ? 'error' : (type === 'warning' ? 'warning' : 'success')}"></i>
                </div>
                <div class="toast-content">
                    <div class="toast-title">${title}</div>
                    <div class="toast-message">${message}</div>
                </div>
                <button class="toast-close">&times;</button>
            `;
            
            toastContainer.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.add('show');
            }, 10);
            
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => {
                    toastContainer.removeChild(toast);
                }, 300);
            }, 5000);
            
            const closeBtn = toast.querySelector('.toast-close');
            closeBtn.addEventListener('click', function() {
                toast.classList.remove('show');
                setTimeout(() => {
                    toastContainer.removeChild(toast);
                }, 300);
            });
        }
    });
</script>
@endsection
