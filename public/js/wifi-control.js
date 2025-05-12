/**
 * Contrôleur de capteurs WiFi pour la configuration et la collecte de données
 */
class WiFiSensorController {
    constructor() {
        this.sensors = {};
        this.apiEndpoint = '/api/sensors/data';
        this.configEndpoint = '/api/sensors/configuration';
        this.chartContainers = {};
        this.charts = {};
        this.simulationTimer = null;
        
        this.init();
    }
    
    init() {
        document.addEventListener('DOMContentLoaded', () => {
            this.setupEventListeners();
            this.loadAvailableSensors();
        });
    }
    
    setupEventListeners() {
        const startBtn = document.getElementById('startCollectionBtn');
        const stopBtn = document.getElementById('stopCollectionBtn');
        const addSensorBtn = document.getElementById('addSensorBtn');
        const configForm = document.getElementById('sensorConfigForm');
        
        if (startBtn) startBtn.addEventListener('click', () => this.startDataCollection());
        if (stopBtn) stopBtn.addEventListener('click', () => this.stopDataCollection());
        if (addSensorBtn) addSensorBtn.addEventListener('click', () => this.showSensorConfigModal());
        if (configForm) configForm.addEventListener('submit', (e) => this.configureSensor(e));
        
        // Configuration du rafraîchissement automatique
        const refreshRateSelect = document.getElementById('refreshRateSelect');
        if (refreshRateSelect) {
            refreshRateSelect.addEventListener('change', (e) => {
                this.setRefreshRate(parseInt(e.target.value));
            });
        }
    }
    
    async loadAvailableSensors() {
        try {
            const response = await fetch('/api/sensors?active=1');
            const data = await response.json();
            
            if (data.success && data.sensors) {
                const sensorList = document.getElementById('availableSensors');
                if (sensorList) {
                    sensorList.innerHTML = '';
                    
                    data.sensors.forEach(sensor => {
                        const sensorItem = document.createElement('div');
                        sensorItem.className = 'sensor-item';
                        sensorItem.dataset.sensorId = sensor.sensor_id;
                        
                        sensorItem.innerHTML = `
                            <div class="sensor-header">
                                <h4>${sensor.type} - ${sensor.sensor_id}</h4>
                                <div class="sensor-status" data-status="inactive">Inactif</div>
                            </div>
                            <div class="sensor-body">
                                <div class="sensor-info">
                                    <p><strong>Modèle:</strong> ${sensor.model}</p>
                                    <p><strong>Patient:</strong> ${sensor.patient ? sensor.patient.name : 'Non assigné'}</p>
                                    <p><strong>Dernière lecture:</strong> <span class="last-reading">N/A</span></p>
                                </div>
                                <div class="sensor-controls">
                                    <button class="btn-config" data-sensor-id="${sensor.sensor_id}">Configurer</button>
                                    <button class="btn-start" data-sensor-id="${sensor.sensor_id}">Démarrer</button>
                                    <button class="btn-stop" data-sensor-id="${sensor.sensor_id}" disabled>Arrêter</button>
                                </div>
                            </div>
                            <div class="sensor-chart-container" id="chart-${sensor.sensor_id}"></div>
                        `;
                        
                        sensorList.appendChild(sensorItem);
                        
                        // Ajouter les écouteurs d'événements pour les boutons du capteur
                        const configBtn = sensorItem.querySelector('.btn-config');
                        const startBtn = sensorItem.querySelector('.btn-start');
                        const stopBtn = sensorItem.querySelector('.btn-stop');
                        
                        configBtn.addEventListener('click', () => this.showSensorConfigModal(sensor));
                        startBtn.addEventListener('click', () => this.startSensorDataCollection(sensor.sensor_id));
                        stopBtn.addEventListener('click', () => this.stopSensorDataCollection(sensor.sensor_id));
                        
                        // Initialiser un graphique pour ce capteur
                        this.initSensorChart(sensor.sensor_id);
                    });
                }
            }
        } catch (error) {
            console.error('Erreur lors du chargement des capteurs:', error);
            this.showNotification('Erreur', 'Impossible de charger les capteurs disponibles.', 'error');
        }
    }
    
    initSensorChart(sensorId) {
        const chartContainer = document.getElementById(`chart-${sensorId}`);
        if (!chartContainer) return;
        
        // Initialiser un graphique vide
        const ctx = document.createElement('canvas');
        ctx.id = `chart-canvas-${sensorId}`;
        chartContainer.appendChild(ctx);
        
        // Stocker les références pour une utilisation ultérieure
        this.chartContainers[sensorId] = chartContainer;
    }
    
    configureSensor(event) {
        event.preventDefault();
        
        const form = event.target;
        const sensorId = form.querySelector('[name="sensor_id"]').value;
        const interval = parseInt(form.querySelector('[name="interval"]').value);
        const thresholdLow = parseFloat(form.querySelector('[name="threshold_low"]').value);
        const thresholdHigh = parseFloat(form.querySelector('[name="threshold_high"]').value);
        const active = form.querySelector('[name="active"]').checked;
        
        // Envoi des configurations au serveur
        fetch(`/api/sensors/${sensorId}/configure`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            },
            body: JSON.stringify({
                interval,
                threshold_low: thresholdLow,
                threshold_high: thresholdHigh,
                active
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.showNotification('Succès', 'Configuration du capteur enregistrée.', 'success');
                
                // Si le capteur est maintenant actif, initialiser le simulateur
                if (active) {
                    // Initialiser ou mettre à jour un simulateur pour ce capteur
                    this.sensors[sensorId] = new SensorSimulator(sensorId, data.sensor.type, this.apiEndpoint);
                    this.sensors[sensorId].setInterval(interval * 1000); // Convertir en millisecondes
                }
                
                // Fermer le modal
                const modal = document.getElementById('sensorConfigModal');
                modal.style.display = 'none';
            } else {
                this.showNotification('Erreur', data.message || 'Échec de la configuration.', 'error');
            }
        })
        .catch(error => {
            console.error('Erreur lors de la configuration du capteur:', error);
            this.showNotification('Erreur', 'Problème de connexion au serveur.', 'error');
        });
    }
    
    showSensorConfigModal(sensor = null) {
        const modal = document.getElementById('sensorConfigModal');
        if (!modal) return;
        
        const form = modal.querySelector('form');
        const title = modal.querySelector('.modal-title');
        
        if (sensor) {
            // Mode édition
            title.textContent = `Configurer le capteur ${sensor.sensor_id}`;
            form.querySelector('[name="sensor_id"]').value = sensor.sensor_id;
            form.querySelector('[name="interval"]').value = sensor.interval || 60;
            form.querySelector('[name="threshold_low"]').value = sensor.threshold_low || '';
            form.querySelector('[name="threshold_high"]').value = sensor.threshold_high || '';
            form.querySelector('[name="active"]').checked = sensor.active;
        } else {
            // Mode ajout - réinitialiser le formulaire
            title.textContent = 'Ajouter un nouveau capteur';
            form.reset();
        }
        
        modal.style.display = 'block';
        
        // Fermer le modal avec le bouton de fermeture
        const closeBtn = modal.querySelector('.close');
        if (closeBtn) {
            closeBtn.onclick = function() {
                modal.style.display = 'none';
            }
        }
        
        // Fermer le modal si on clique à l'extérieur
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    }
    
    startDataCollection() {
        // Démarrer la collection pour tous les capteurs
        Object.values(this.sensors).forEach(sensor => {
            sensor.start();
        });
        
        this.showNotification('Collection démarrée', 'La collecte des données a commencé pour tous les capteurs actifs.', 'info');
        
        // Mettre à jour l'interface
        const startBtn = document.getElementById('startCollectionBtn');
        const stopBtn = document.getElementById('stopCollectionBtn');
        if (startBtn) startBtn.disabled = true;
        if (stopBtn) stopBtn.disabled = false;
    }
    
    stopDataCollection() {
        // Arrêter la collection pour tous les capteurs
        Object.values(this.sensors).forEach(sensor => {
            sensor.stop();
        });
        
        this.showNotification('Collection arrêtée', 'La collecte des données a été arrêtée pour tous les capteurs.', 'info');
        
        // Mettre à jour l'interface
        const startBtn = document.getElementById('startCollectionBtn');
        const stopBtn = document.getElementById('stopCollectionBtn');
        if (startBtn) startBtn.disabled = false;
        if (stopBtn) stopBtn.disabled = true;
    }
    
    startSensorDataCollection(sensorId) {
        if (this.sensors[sensorId]) {
            this.sensors[sensorId].start();
            
            // Mettre à jour l'interface
            const sensorItem = document.querySelector(`.sensor-item[data-sensor-id="${sensorId}"]`);
            if (sensorItem) {
                const statusElement = sensorItem.querySelector('.sensor-status');
                const startBtn = sensorItem.querySelector('.btn-start');
                const stopBtn = sensorItem.querySelector('.btn-stop');
                
                if (statusElement) statusElement.dataset.status = 'active';
                if (statusElement) statusElement.textContent = 'Actif';
                if (startBtn) startBtn.disabled = true;
                if (stopBtn) stopBtn.disabled = false;
            }
            
            this.showNotification('Capteur activé', `Le capteur ${sensorId} a commencé à collecter des données.`, 'success');
        } else {
            // Le capteur n'est pas encore initialisé - récupérer sa configuration et l'initialiser
            fetch(`/api/sensors/configuration?capteur_id=${sensorId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.config) {
                        this.sensors[sensorId] = new SensorSimulator(sensorId, data.config.type, this.apiEndpoint);
                        this.sensors[sensorId].setInterval(data.config.interval * 1000);
                        this.startSensorDataCollection(sensorId); // Réessayer maintenant que le capteur est initialisé
                    }
                })
                .catch(error => {
                    console.error(`Erreur lors de l'initialisation du capteur ${sensorId}:`, error);
                    this.showNotification('Erreur', `Impossible d'initialiser le capteur ${sensorId}.`, 'error');
                });
        }
    }
    
    stopSensorDataCollection(sensorId) {
        if (this.sensors[sensorId]) {
            this.sensors[sensorId].stop();
            
            // Mettre à jour l'interface
            const sensorItem = document.querySelector(`.sensor-item[data-sensor-id="${sensorId}"]`);
            if (sensorItem) {
                const statusElement = sensorItem.querySelector('.sensor-status');
                const startBtn = sensorItem.querySelector('.btn-start');
                const stopBtn = sensorItem.querySelector('.btn-stop');
                
                if (statusElement) statusElement.dataset.status = 'inactive';
                if (statusElement) statusElement.textContent = 'Inactif';
                if (startBtn) startBtn.disabled = false;
                if (stopBtn) stopBtn.disabled = true;
            }
            
            this.showNotification('Capteur désactivé', `La collecte de données du capteur ${sensorId} a été arrêtée.`, 'info');
        }
    }
    
    setRefreshRate(milliseconds) {
        Object.values(this.sensors).forEach(sensor => {
            if (sensor.active) {
                sensor.setInterval(milliseconds);
            }
        });
        
        this.showNotification('Taux rafraîchissement', `Le taux de rafraîchissement a été défini à ${milliseconds/1000} secondes.`, 'info');
    }
    
    updateSensorUI(sensorId, value) {
        // Mettre à jour l'affichage de la dernière lecture
        const sensorItem = document.querySelector(`.sensor-item[data-sensor-id="${sensorId}"]`);
        if (sensorItem) {
            const lastReadingElement = sensorItem.querySelector('.last-reading');
            if (lastReadingElement) {
                lastReadingElement.textContent = value;
            }
        }
    }
    
    showNotification(title, message, type = 'info') {
        // S'il y a un conteneur de toasts sur la page, l'utiliser
        const toastContainer = document.getElementById('toastContainer');
        if (toastContainer) {
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            
            toast.innerHTML = `
                <div class="toast-icon">
                    <i class="icon-${type === 'success' ? 'check-circle' : type === 'error' ? 'x-circle' : 'info'}"></i>
                </div>
                <div class="toast-content">
                    <div class="toast-title">${title}</div>
                    <div class="toast-message">${message}</div>
                </div>
                <button class="toast-close">&times;</button>
            `;
            
            toastContainer.appendChild(toast);
            
            const closeBtn = toast.querySelector('.toast-close');
            if (closeBtn) {
                closeBtn.addEventListener('click', () => {
                    toast.style.opacity = '0';
                    setTimeout(() => {
                        toast.remove();
                    }, 300);
                });
            }
            
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }, 5000);
        } else {
            // Sinon, utiliser l'alerte du navigateur
            alert(`${title}: ${message}`);
        }
    }
}

// Initialiser le contrôleur lorsque le script est chargé
const wifiSensorController = new WiFiSensorController();

// S'assurer que le simulateur est disponible
document.addEventListener('DOMContentLoaded', function() {
    // Vérifier si le script du simulateur est chargé
    if (typeof SensorSimulator === 'undefined') {
        console.warn('Le script SensorSimulator n\'est pas chargé. Le chargement est en cours...');
        
        const script = document.createElement('script');
        script.src = '/js/sensor-simulator.js';
        script.onload = function() {
            console.log('SensorSimulator chargé avec succès.');
        };
        script.onerror = function() {
            console.error('Impossible de charger le script SensorSimulator.');
        };
        document.head.appendChild(script);
    }
});
