@extends('layouts.app')

@section('title', 'Configuration WiFi des capteurs')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/forms.css') }}">
<style>
    /* ... keep existing code (styles pour la config wifi et les capteurs) */
    
    /* Toast notifications */
    .toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    
    .toast {
        display: flex;
        align-items: flex-start;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        padding: 12px 15px;
        width: 300px;
        max-width: 100%;
        opacity: 1;
        transition: all 0.3s ease;
    }
    
    .toast.success {
        border-left: 4px solid #10B981;
    }
    
    .toast.error {
        border-left: 4px solid #EF4444;
    }
    
    .toast.info {
        border-left: 4px solid #3B82F6;
    }
    
    .toast.warning {
        border-left: 4px solid #F59E0B;
    }
    
    .toast-icon {
        margin-right: 12px;
        font-size: 18px;
    }
    
    .toast.success .toast-icon {
        color: #10B981;
    }
    
    .toast.error .toast-icon {
        color: #EF4444;
    }
    
    .toast.info .toast-icon {
        color: #3B82F6;
    }
    
    .toast.warning .toast-icon {
        color: #F59E0B;
    }
    
    .toast-content {
        flex: 1;
    }
    
    .toast-title {
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 4px;
    }
    
    .toast-message {
        font-size: 13px;
        color: #4B5563;
    }
    
    .toast-close {
        background: none;
        border: none;
        color: #9CA3AF;
        cursor: pointer;
        font-size: 18px;
        padding: 0;
        margin-left: 10px;
        line-height: 1;
    }
    
    .toast-close:hover {
        color: #6B7280;
    }
</style>
@endsection

@section('content')
<div class="wifi-config-container">
    <h1 class="page-title"><i class="fas fa-wifi"></i> Configuration WiFi des capteurs</h1>
    <p class="text-muted mb-4">Configurez et gérez vos capteurs connectés en WiFi pour collecter des données en temps réel.</p>
    
    <div class="control-panel">
        <h3 class="panel-title">Contrôle de la collecte</h3>
        <div class="panel-controls">
            <div class="global-controls">
                <button id="startCollectionBtn" class="btn"><i class="fas fa-play"></i> Démarrer la collecte</button>
                <button id="stopCollectionBtn" class="btn" disabled><i class="fas fa-stop"></i> Arrêter la collecte</button>
                <button id="addSensorBtn" class="btn"><i class="fas fa-plus"></i> Ajouter un capteur</button>
            </div>
            <div class="refresh-rate">
                <label for="refreshRateSelect">Taux de rafraîchissement:</label>
                <select id="refreshRateSelect">
                    <option value="1000">1 seconde</option>
                    <option value="5000" selected>5 secondes</option>
                    <option value="10000">10 secondes</option>
                    <option value="30000">30 secondes</option>
                    <option value="60000">1 minute</option>
                </select>
            </div>
        </div>
    </div>
    
    <div id="availableSensors" class="sensor-grid">
        <div class="loading-indicator">
            <i class="fas fa-spinner fa-spin"></i> Chargement des capteurs...
        </div>
    </div>
</div>

<!-- Modal pour la configuration des capteurs existants -->
<div id="sensorConfigModal" class="modal">
    <div class="modal-content">
        <h2 class="modal-title">Configurer un capteur</h2>
        <span class="close">&times;</span>
        
        <form id="sensorConfigForm">
            <input type="hidden" name="sensor_id" value="">
            
            <div class="form-grid">
                <div class="form-field">
                    <label for="interval">Intervalle de lecture (secondes)</label>
                    <input type="number" id="interval" name="interval" value="60" min="1" max="3600">
                </div>
                <div class="form-field">
                    <label for="active">Activer le capteur</label>
                    <div class="switch">
                        <input type="checkbox" id="active" name="active" checked>
                        <span class="slider"></span>
                        <label for="active">Activé</label>
                    </div>
                </div>
            </div>
            
            <div class="form-grid">
                <div class="form-field">
                    <label for="threshold_low">Seuil bas</label>
                    <input type="number" id="threshold_low" name="threshold_low" step="0.1">
                </div>
                <div class="form-field">
                    <label for="threshold_high">Seuil haut</label>
                    <input type="number" id="threshold_high" name="threshold_high" step="0.1">
                </div>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn-cancel">Annuler</button>
                <button type="submit" class="btn-save">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal pour l'ajout d'un nouveau capteur -->
<div id="addSensorModal" class="modal">
    <div class="modal-content">
        <h2 class="modal-title">Ajouter un nouveau capteur</h2>
        <span class="close">&times;</span>
        
        <form id="addSensorForm" action="{{ route('sensors.store') }}" method="POST">
            @csrf
            <div class="form-grid">
                <div class="form-field">
                    <label for="sensor_id">Identifiant du capteur</label>
                    <input type="text" id="sensor_id" name="sensor_id" required>
                </div>
                <div class="form-field">
                    <label for="type">Type de capteur</label>
                    <select id="type" name="type" required>
                        <option value="">Sélectionner</option>
                        <option value="heartrate">Rythme cardiaque</option>
                        <option value="temperature">Température</option>
                        <option value="blood_pressure">Pression artérielle</option>
                        <option value="oxygen">Saturation O₂</option>
                    </select>
                </div>
            </div>
            
            <div class="form-grid">
                <div class="form-field">
                    <label for="model">Modèle</label>
                    <input type="text" id="model" name="model" required>
                </div>
                <div class="form-field">
                    <label for="location">Emplacement</label>
                    <input type="text" id="location" name="location">
                </div>
            </div>
            
            <div class="form-field">
                <label for="description">Description</label>
                <input type="text" id="description" name="description">
            </div>
            
            <div class="form-grid">
                <div class="form-field">
                    <label for="patient_id">Patient associé</label>
                    <select id="patient_id" name="patient_id">
                        <option value="">Aucun patient</option>
                        @foreach(\App\Models\User::whereHas('role', function ($query) {
                            $query->where('nom', 'patient');
                        })->get() as $patient)
                            <option value="{{ $patient->id }}">{{ $patient->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-field">
                    <label for="alert_level">Niveau d'alerte</label>
                    <select id="alert_level" name="alert_level">
                        <option value="low">Bas</option>
                        <option value="medium" selected>Moyen</option>
                        <option value="high">Élevé</option>
                        <option value="critical">Critique</option>
                    </select>
                </div>
            </div>
            
            <div class="form-grid">
                <div class="form-field">
                    <label for="threshold_low_new">Seuil bas</label>
                    <input type="number" id="threshold_low_new" name="threshold_low" step="0.1">
                </div>
                <div class="form-field">
                    <label for="threshold_high_new">Seuil haut</label>
                    <input type="number" id="threshold_high_new" name="threshold_high" step="0.1">
                </div>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn-cancel">Annuler</button>
                <button type="submit" class="btn-save">Ajouter le capteur</button>
            </div>
        </form>
    </div>
</div>

<!-- Container pour les notifications Toast -->
<div id="toastContainer" class="toast-container"></div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    // Vérification du chargement des scripts nécessaires
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Page chargée, vérification des scripts...');
        
        // Vérifier si le token CSRF est présent
        var metaToken = document.querySelector('meta[name="csrf-token"]');
        if (!metaToken) {
            console.error('Meta CSRF token manquant. Ajoutons-le.');
            var meta = document.createElement('meta');
            meta.name = 'csrf-token';
            meta.content = '{{ csrf_token() }}';
            document.head.appendChild(meta);
        }
        
        // Gérer l'affichage des modals
        const addSensorBtn = document.getElementById('addSensorBtn');
        const addSensorModal = document.getElementById('addSensorModal');
        const sensorConfigModal = document.getElementById('sensorConfigModal');
        const closeButtons = document.querySelectorAll('.close, .btn-cancel');
        
        if (addSensorBtn && addSensorModal) {
            addSensorBtn.addEventListener('click', function() {
                addSensorModal.style.display = 'block';
            });
        }
        
        closeButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                addSensorModal.style.display = 'none';
                sensorConfigModal.style.display = 'none';
            });
        });
        
        // Fermer les modals quand on clique en dehors
        window.addEventListener('click', function(event) {
            if (event.target === addSensorModal) {
                addSensorModal.style.display = 'none';
            } else if (event.target === sensorConfigModal) {
                sensorConfigModal.style.display = 'none';
            }
        });
        
        // Gestion du formulaire d'ajout de capteur
        const addSensorForm = document.getElementById('addSensorForm');
        if (addSensorForm) {
            addSensorForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(addSensorForm);
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                fetch(addSensorForm.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        createToast('Capteur ajouté', 'Le capteur a été ajouté avec succès', 'success');
                        addSensorModal.style.display = 'none';
                        addSensorForm.reset();
                        // Recharger la liste des capteurs
                        fetchSensors();
                    } else {
                        createToast('Erreur', data.message || 'Une erreur est survenue', 'error');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    createToast('Erreur', 'Une erreur est survenue lors de l\'ajout du capteur', 'error');
                });
            });
        }
        
        // Fonction pour afficher des notifications toast
        function createToast(title, message, type = 'info') {
            const toastContainer = document.getElementById('toastContainer');
            if (!toastContainer) return;
            
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            
            let iconName;
            switch(type) {
                case 'success': iconName = 'check-circle'; break;
                case 'warning': iconName = 'exclamation-triangle'; break;
                case 'error': iconName = 'times-circle'; break;
                default: iconName = 'info-circle';
            }
            
            toast.innerHTML = `
                <div class="toast-icon">
                    <i class="fas fa-${iconName}"></i>
                </div>
                <div class="toast-content">
                    <div class="toast-title">${title}</div>
                    <div class="toast-message">${message}</div>
                </div>
                <button class="toast-close">&times;</button>
            `;
            
            toastContainer.appendChild(toast);
            
            // Auto-fermer après 5 secondes
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => {
                    toastContainer.removeChild(toast);
                }, 300);
            }, 5000);
            
            // Bouton de fermeture
            const closeBtn = toast.querySelector('.toast-close');
            if (closeBtn) {
                closeBtn.addEventListener('click', function() {
                    toast.style.opacity = '0';
                    setTimeout(() => {
                        toastContainer.removeChild(toast);
                    }, 300);
                });
            }
        }
        
        // Fonction pour récupérer la liste des capteurs disponibles
        function fetchSensors() {
            const sensorsContainer = document.getElementById('availableSensors');
            if (!sensorsContainer) return;
            
            sensorsContainer.innerHTML = `
                <div class="loading-indicator">
                    <i class="fas fa-spinner fa-spin"></i> Chargement des capteurs...
                </div>
            `;
            
            fetch('{{ route("api.sensors.list") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.sensors) {
                        if (data.sensors.length === 0) {
                            sensorsContainer.innerHTML = `
                                <div class="no-sensors">
                                    <p>Aucun capteur disponible. Ajoutez-en un avec le bouton ci-dessus.</p>
                                </div>
                            `;
                            return;
                        }
                        
                        sensorsContainer.innerHTML = '';
                        data.sensors.forEach(sensor => {
                            const sensorEl = document.createElement('div');
                            sensorEl.className = 'sensor-item';
                            sensorEl.innerHTML = `
                                <div class="sensor-header">
                                    <h4>${sensor.type} - ${sensor.sensor_id}</h4>
                                    <span class="sensor-status" data-status="${sensor.status}">${getSensorStatusText(sensor.status)}</span>
                                </div>
                                <div class="sensor-body">
                                    <div class="sensor-info">
                                        <p><strong>Modèle:</strong> ${sensor.model || 'Non spécifié'}</p>
                                        <p><strong>Dernière valeur:</strong> <span class="last-value">${sensor.last_reading || 'N/A'}</span></p>
                                        <p><strong>Dernière transmission:</strong> ${sensor.last_reading_at ? formatDate(sensor.last_reading_at) : 'Jamais'}</p>
                                        <p><strong>Patient:</strong> ${sensor.patient ? sensor.patient.name : 'Non assigné'}</p>
                                    </div>
                                    <div class="sensor-controls">
                                        <button class="btn-config" data-sensor-id="${sensor.id}">Configurer</button>
                                        <button class="btn-start" data-sensor-id="${sensor.id}" ${sensor.active ? 'disabled' : ''}>Démarrer</button>
                                        <button class="btn-stop" data-sensor-id="${sensor.id}" ${!sensor.active ? 'disabled' : ''}>Arrêter</button>
                                    </div>
                                </div>
                                <div class="sensor-chart-container">
                                    <canvas id="chart-${sensor.id}"></canvas>
                                </div>
                            `;
                            sensorsContainer.appendChild(sensorEl);
                            
                            // Initialiser le graphique
                            initChart(sensor.id);
                            
                            // Ajouter les écouteurs d'événements pour les boutons
                            const configBtn = sensorEl.querySelector('.btn-config');
                            const startBtn = sensorEl.querySelector('.btn-start');
                            const stopBtn = sensorEl.querySelector('.btn-stop');
                            
                            if (configBtn) {
                                configBtn.addEventListener('click', function() {
                                    openConfigModal(sensor);
                                });
                            }
                            
                            if (startBtn) {
                                startBtn.addEventListener('click', function() {
                                    startSensor(sensor.id);
                                });
                            }
                            
                            if (stopBtn) {
                                stopBtn.addEventListener('click', function() {
                                    stopSensor(sensor.id);
                                });
                            }
                        });
                    } else {
                        sensorsContainer.innerHTML = `
                            <div class="error-message">
                                <p>Erreur lors du chargement des capteurs.</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    sensorsContainer.innerHTML = `
                        <div class="error-message">
                            <p>Erreur lors du chargement des capteurs: ${error.message}</p>
                        </div>
                    `;
                });
        }
        
        function getSensorStatusText(status) {
            switch(status) {
                case 'active': return 'Actif';
                case 'inactive': return 'Inactif';
                case 'error': return 'Erreur';
                case 'ready': return 'Prêt';
                case 'warning': return 'Attention';
                default: return status;
            }
        }
        
        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.round(diffMs / 60000);
            
            if (diffMins < 1) return 'Il y a quelques secondes';
            if (diffMins < 60) return `Il y a ${diffMins} min`;
            
            const diffHours = Math.floor(diffMins / 60);
            if (diffHours < 24) return `Il y a ${diffHours} h`;
            
            return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
        }
        
        function initChart(sensorId) {
            const canvas = document.getElementById(`chart-${sensorId}`);
            if (!canvas) return;
            
            const ctx = canvas.getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: Array(10).fill().map((_, i) => `-${10-i} min`),
                    datasets: [{
                        label: 'Valeur',
                        data: [],
                        borderColor: 'rgb(75, 192, 192)',
                        tension: 0.3,
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: false
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }
        
        function openConfigModal(sensor) {
            const modal = document.getElementById('sensorConfigModal');
            if (!modal) return;
            
            document.querySelector('#sensorConfigForm [name="sensor_id"]').value = sensor.id;
            document.getElementById('interval').value = sensor.interval;
            document.getElementById('active').checked = sensor.active;
            document.getElementById('threshold_low').value = sensor.threshold_low || '';
            document.getElementById('threshold_high').value = sensor.threshold_high || '';
            
            modal.style.display = 'block';
        }
        
        function startSensor(sensorId) {
            updateSensorStatus(sensorId, true);
        }
        
        function stopSensor(sensorId) {
            updateSensorStatus(sensorId, false);
        }
        
        function updateSensorStatus(sensorId, active) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            fetch(`{{ url('api/sensors') }}/${sensorId}/configure`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ active: active })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    createToast('Capteur mis à jour', `Le capteur a été ${active ? 'activé' : 'désactivé'}`, 'success');
                    fetchSensors(); // Recharger les capteurs
                } else {
                    createToast('Erreur', data.message || 'Une erreur est survenue', 'error');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                createToast('Erreur', 'Une erreur est survenue lors de la mise à jour du capteur', 'error');
            });
        }
        
        // Configurer le formulaire de configuration du capteur
        const sensorConfigForm = document.getElementById('sensorConfigForm');
        if (sensorConfigForm) {
            sensorConfigForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(sensorConfigForm);
                const sensorId = formData.get('sensor_id');
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                // Convertir FormData en objet
                const configData = {
                    interval: parseInt(formData.get('interval')),
                    active: formData.get('active') === 'on',
                    threshold_low: formData.get('threshold_low') ? parseFloat(formData.get('threshold_low')) : null,
                    threshold_high: formData.get('threshold_high') ? parseFloat(formData.get('threshold_high')) : null
                };
                
                fetch(`{{ url('api/sensors') }}/${sensorId}/configure`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(configData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        createToast('Configuration enregistrée', 'La configuration du capteur a été mise à jour', 'success');
                        sensorConfigModal.style.display = 'none';
                        fetchSensors();
                    } else {
                        createToast('Erreur', data.message || 'Une erreur est survenue', 'error');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    createToast('Erreur', 'Une erreur est survenue lors de la mise à jour de la configuration', 'error');
                });
            });
        }
        
        // Boutons de contrôle global
        const startCollectionBtn = document.getElementById('startCollectionBtn');
        const stopCollectionBtn = document.getElementById('stopCollectionBtn');
        const refreshRateSelect = document.getElementById('refreshRateSelect');
        
        if (startCollectionBtn && stopCollectionBtn) {
            startCollectionBtn.addEventListener('click', function() {
                startAllSensors();
                startCollectionBtn.disabled = true;
                stopCollectionBtn.disabled = false;
            });
            
            stopCollectionBtn.addEventListener('click', function() {
                stopAllSensors();
                stopCollectionBtn.disabled = true;
                startCollectionBtn.disabled = false;
            });
        }
        
        function startAllSensors() {
            fetch('{{ url("api/sensors") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.sensors) {
                        const promises = data.sensors.map(sensor => 
                            updateSensorStatus(sensor.id, true));
                        
                        Promise.all(promises)
                            .then(() => {
                                createToast('Collection démarrée', 'Tous les capteurs ont été activés', 'success');
                                fetchSensors();
                            })
                            .catch(error => {
                                console.error('Erreur:', error);
                                createToast('Erreur', 'Une erreur est survenue lors de l\'activation des capteurs', 'error');
                            });
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    createToast('Erreur', 'Une erreur est survenue lors de la récupération des capteurs', 'error');
                });
        }
        
        function stopAllSensors() {
            fetch('{{ url("api/sensors") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.sensors) {
                        const promises = data.sensors.map(sensor => 
                            updateSensorStatus(sensor.id, false));
                        
                        Promise.all(promises)
                            .then(() => {
                                createToast('Collection arrêtée', 'Tous les capteurs ont été désactivés', 'success');
                                fetchSensors();
                            })
                            .catch(error => {
                                console.error('Erreur:', error);
                                createToast('Erreur', 'Une erreur est survenue lors de la désactivation des capteurs', 'error');
                            });
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    createToast('Erreur', 'Une erreur est survenue lors de la récupération des capteurs', 'error');
                });
        }
        
        // Charger les capteurs au démarrage
        fetchSensors();
        
        // Rafraîchissement automatique des données
        let refreshInterval;
        
        function startRefresh() {
            const refreshRate = parseInt(refreshRateSelect.value);
            clearInterval(refreshInterval);
            refreshInterval = setInterval(fetchSensors, refreshRate);
        }
        
        if (refreshRateSelect) {
            refreshRateSelect.addEventListener('change', startRefresh);
            startRefresh();
        }
    });
</script>
<script src="{{ asset('js/sensor-simulator.js') }}"></script>
@endsection