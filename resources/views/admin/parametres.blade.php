@extends('layouts.app')

@section('title', 'Administration - Système de Monitoring Médical')

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --card-bg: rgba(255, 255, 255, 0.95);
            --text-primary: #2d3748;
            --text-secondary: #718096;
            --border-color: rgba(255, 255, 255, 0.2);
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #3b82f6;
        }

        .admin-container {
            background: var(--primary-gradient);
            min-height: 100vh;
            padding: 20px;
        }

        .admin-header {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid var(--border-color);
        }

        .admin-title {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
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
            background: linear-gradient(135deg, #667eea, #764ba2);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .admin-title h1 {
            color: var(--text-primary);
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stats-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--card-bg);
            backdrop-filter: blur(15px);
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-left: 4px solid;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }

        .stat-card.users { border-left-color: var(--info-color); }
        .stat-card.patients { border-left-color: var(--success-color); }
        .stat-card.sensors { border-left-color: var(--warning-color); }
        .stat-card.alerts { border-left-color: var(--danger-color); }

        .stat-header {
            display: flex;
            align-items: center;
            justify-content: between;
            margin-bottom: 15px;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.4rem;
            margin-right: 15px;
        }

        .stat-icon.users { background: var(--info-color); }
        .stat-icon.patients { background: var(--success-color); }
        .stat-icon.sensors { background: var(--warning-color); }
        .stat-icon.alerts { background: var(--danger-color); }

        .stat-content h3 {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0 0 5px 0;
        }

        .stat-content p {
            color: var(--text-secondary);
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.9rem;
        }

        .admin-sections {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 25px;
        }

        .section-card {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid var(--border-color);
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid rgba(102, 126, 234, 0.1);
        }

        .section-icon {
            width: 45px;
            height: 45px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.3rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
        }

        .section-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
        }

        .btn {
            padding: 12px 20px;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            text-decoration: none;
            margin: 5px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .btn-warning {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.8);
            color: var(--text-primary);
            border: 2px solid rgba(102, 126, 234, 0.2);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .btn-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid rgba(102, 126, 234, 0.1);
            border-radius: 10px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
        }

        .form-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            background: white;
        }

        .form-select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid rgba(102, 126, 234, 0.1);
            border-radius: 10px;
            font-size: 0.9rem;
            background: rgba(255, 255, 255, 0.8);
            cursor: pointer;
        }

        .sensor-controls {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .sensor-item {
            background: rgba(248, 250, 252, 0.8);
            border-radius: 10px;
            padding: 20px;
            border: 2px solid rgba(102, 126, 234, 0.1);
        }

        .sensor-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .sensor-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sensor-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .sensor-status.active {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }

        .sensor-status.inactive {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: var(--success-color);
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .action-card {
            background: rgba(248, 250, 252, 0.5);
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
            border: 2px solid rgba(102, 126, 234, 0.1);
            cursor: pointer;
        }

        .action-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            background: rgba(255, 255, 255, 0.9);
        }

        .action-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 1.5rem;
            color: white;
            background: linear-gradient(135deg, #667eea, #764ba2);
        }

        @media (max-width: 768px) {
            .admin-container { padding: 15px; }
            .admin-header { padding: 20px; }
            .admin-title h1 { font-size: 2rem; }
            .admin-sections { grid-template-columns: 1fr; }
            .stats-overview { grid-template-columns: repeat(2, 1fr); }
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: black;
        }
    </style>
@endsection

@section('content')
<div class="admin-container">
    <!-- En-tête de l'administration -->
    <div class="admin-header">
        <div class="admin-title">
            <div class="title-icon">
                <i class="fas fa-users-cog"></i>
            </div>
            <h1>Administration Système</h1>
        </div>
        
        <!-- Statistiques générales -->
        <div class="stats-overview">
            <div class="stat-card users">
                <div class="stat-header">
                    <div class="stat-icon users">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <h3>{{ $stats['users'] ?? '45' }}</h3>
                        <p>Utilisateurs Actifs</p>
                    </div>
                </div>
            </div>

            <div class="stat-card patients">
                <div class="stat-header">
                    <div class="stat-icon patients">
                        <i class="fas fa-user-injured"></i>
                    </div>
                    <div class="stat-content">
                        <h3>{{ $stats['patients'] ?? '128' }}</h3>
                        <p>Patients Enregistrés</p>
                    </div>
                </div>
            </div>

            <div class="stat-card sensors">
                <div class="stat-header">
                    <div class="stat-icon sensors">
                        <i class="fas fa-microchip"></i>
                    </div>
                    <div class="stat-content">
                        <h3>{{ $stats['sensors'] ?? '24' }}</h3>
                        <p>Capteurs Connectés</p>
                    </div>
                </div>
            </div>

            <div class="stat-card alerts">
                <div class="stat-header">
                    <div class="stat-icon alerts">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stat-content">
                        <h3>{{ $stats['alerts'] ?? '7' }}</h3>
                        <p>Alertes Actives</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sections d'administration -->
    <div class="admin-sections">
        <!-- Gestion des Utilisateurs -->
        <div class="section-card">
            <div class="section-header">
                <div class="section-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="section-title">Gestion des Utilisateurs</h3>
            </div>
            
            <div class="btn-grid">
                <button class="btn btn-primary" onclick="openAddUserModal()">
                    <i class="fas fa-user-plus"></i>
                    Ajouter Utilisateur
                </button>
                <button class="btn btn-secondary" onclick="window.location.href='/admin/utilisateurs'">
                    <i class="fas fa-list"></i>
                    Liste Utilisateurs
                </button>
                <button class="btn btn-warning" onclick="manageRoles()">
                    <i class="fas fa-user-tag"></i>
                    Gérer Rôles
                </button>
                <button class="btn btn-danger" onclick="viewInactiveUsers()">
                    <i class="fas fa-user-slash"></i>
                    Comptes Inactifs
                </button>
            </div>
        </div>

        <!-- Gestion des Capteurs -->
        <div class="section-card">
            <div class="section-header">
                <div class="section-icon">
                    <i class="fas fa-microchip"></i>
                </div>
                <h3 class="section-title">Gestion des Capteurs</h3>
            </div>
            
            <div class="sensor-controls">
                @forelse($sensors ?? [] as $sensor)
                    <div class="sensor-item">
                        <div class="sensor-header">
                            <div class="sensor-info">
                                <i class="fas fa-heartbeat" style="color: #ef4444;"></i>
                                <div>
                                    <strong>{{ $sensor->sensor_id ?? 'SEN-001' }}</strong>
                                    <br>
                                    <small>{{ $sensor->model ?? 'CardioSense Pro' }}</small>
                                </div>
                            </div>
                            <span class="sensor-status {{ $sensor->active ?? true ? 'active' : 'inactive' }}">
                                {{ $sensor->active ?? true ? 'Actif' : 'Inactif' }}
                            </span>
                        </div>
                        
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <label class="toggle-switch">
                                <input type="checkbox" {{ $sensor->active ?? true ? 'checked' : '' }} 
                                       onchange="toggleSensor('{{ $sensor->id ?? 1 }}', this.checked)">
                                <span class="slider"></span>
                            </label>
                            <button class="btn btn-primary" onclick="configureSensor('{{ $sensor->id ?? 1 }}')">
                                <i class="fas fa-cog"></i>
                                Configurer
                            </button>
                        </div>
                    </div>
                @empty
                    <!-- Capteurs par défaut pour démonstration -->
                    <div class="sensor-item">
                        <div class="sensor-header">
                            <div class="sensor-info">
                                <i class="fas fa-heartbeat" style="color: #ef4444;"></i>
                                <div>
                                    <strong>SEN-001</strong>
                                    <br>
                                    <small>CardioSense Pro</small>
                                </div>
                            </div>
                            <span class="sensor-status active">Actif</span>
                        </div>
                        
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <label class="toggle-switch">
                                <input type="checkbox" checked onchange="toggleSensor('1', this.checked)">
                                <span class="slider"></span>
                            </label>
                            <button class="btn btn-primary" onclick="configureSensor('1')">
                                <i class="fas fa-cog"></i>
                                Configurer
                            </button>
                        </div>
                    </div>

                    <div class="sensor-item">
                        <div class="sensor-header">
                            <div class="sensor-info">
                                <i class="fas fa-thermometer-half" style="color: #f59e0b;"></i>
                                <div>
                                    <strong>SEN-002</strong>
                                    <br>
                                    <small>ThermoGuard Plus</small>
                                </div>
                            </div>
                            <span class="sensor-status inactive">Inactif</span>
                        </div>
                        
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <label class="toggle-switch">
                                <input type="checkbox" onchange="toggleSensor('2', this.checked)">
                                <span class="slider"></span>
                            </label>
                            <button class="btn btn-primary" onclick="configureSensor('2')">
                                <i class="fas fa-cog"></i>
                                Configurer
                            </button>
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="btn-grid" style="margin-top: 20px;">
                <button class="btn btn-success" onclick="window.location.href='/sensors/create'">
                    <i class="fas fa-plus"></i>
                    Ajouter Capteur
                </button>
                <button class="btn btn-warning" onclick="viewSensorDiagnostics()">
                    <i class="fas fa-stethoscope"></i>
                    Diagnostics
                </button>
            </div>
        </div>

        <!-- Saisie Manuelle de Données -->
        <div class="section-card">
            <div class="section-header">
                <div class="section-icon">
                    <i class="fas fa-keyboard"></i>
                </div>
                <h3 class="section-title">Saisie Manuelle de Données</h3>
            </div>
            
            <form id="manualDataForm" onsubmit="submitManualData(event)">
                <div class="form-group">
                    <label class="form-label">Patient</label>
                    <select class="form-select" name="patient_id" required>
                        <option value="">Sélectionner un patient</option>
                        <option value="1">Mehdi Rais</option>
                        <option value="2">Sara Tazi</option>
                        <option value="3">Karim Alaoui</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Type de Mesure</label>
                    <select class="form-select" name="measurement_type" required onchange="updateMeasurementFields(this.value)">
                        <option value="">Sélectionner le type</option>
                        <option value="heartrate">Rythme Cardiaque (bpm)</option>
                        <option value="temperature">Température (°C)</option>
                        <option value="blood_pressure">Pression Artérielle (mmHg)</option>
                        <option value="oxygen">Saturation O₂ (%)</option>
                    </select>
                </div>

                <div class="form-group" id="valueFields">
                    <label class="form-label">Valeur</label>
                    <input type="number" class="form-input" name="value" step="0.1" required>
                </div>

                <div class="form-group" id="bloodPressureFields" style="display: none;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <div>
                            <label class="form-label">Systolique</label>
                            <input type="number" class="form-input" name="systolic">
                        </div>
                        <div>
                            <label class="form-label">Diastolique</label>
                            <input type="number" class="form-input" name="diastolic">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Date et Heure</label>
                    <input type="datetime-local" class="form-input" name="timestamp" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Notes (optionnel)</label>
                    <input type="text" class="form-input" name="notes" placeholder="Commentaires sur la mesure">
                </div>

                <button type="submit" class="btn btn-success" style="width: 100%;">
                    <i class="fas fa-save"></i>
                    Enregistrer la Mesure
                </button>
            </form>
        </div>

        <!-- Paramètres Système -->
        <div class="section-card">
            <div class="section-header">
                <div class="section-icon">
                    <i class="fas fa-cogs"></i>
                </div>
                <h3 class="section-title">Paramètres Système</h3>
            </div>
            
            <div class="btn-grid">
                <button class="btn btn-primary" onclick="openSystemSettings()">
                    <i class="fas fa-server"></i>
                    Configuration Serveur
                </button>
                <button class="btn btn-warning" onclick="viewSystemLogs()">
                    <i class="fas fa-file-alt"></i>
                    Logs Système
                </button>
                <button class="btn btn-danger" onclick="systemMaintenance()">
                    <i class="fas fa-tools"></i>
                    Maintenance
                </button>
                <button class="btn btn-secondary" onclick="exportData()">
                    <i class="fas fa-download"></i>
                    Exporter Données
                </button>
            </div>
        </div>

        <!-- Actions Rapides -->
        <div class="section-card">
            <div class="section-header">
                <div class="section-icon">
                    <i class="fas fa-bolt"></i>
                </div>
                <h3 class="section-title">Actions Rapides</h3>
            </div>
            
            <div class="quick-actions">
                <div class="action-card" onclick="viewRecentAlerts()">
                    <div class="action-icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    <h4>Alertes Récentes</h4>
                    <p>Consulter les dernières alertes</p>
                </div>

                <div class="action-card" onclick="generateReport()">
                    <div class="action-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h4>Rapport Global</h4>
                    <p>Générer un rapport système</p>
                </div>

                <div class="action-card" onclick="systemBackup()">
                    <div class="action-icon">
                        <i class="fas fa-database"></i>
                    </div>
                    <h4>Sauvegarde</h4>
                    <p>Créer une sauvegarde</p>
                </div>

                <div class="action-card" onclick="viewStatistics()">
                    <div class="action-icon">
                        <i class="fas fa-analytics"></i>
                    </div>
                    <h4>Statistiques</h4>
                    <p>Analyser les performances</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour ajouter un utilisateur -->
<div id="addUserModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('addUserModal')">&times;</span>
        <h2><i class="fas fa-user-plus"></i> Ajouter un Nouvel Utilisateur</h2>
        
        <form id="addUserForm" onsubmit="submitNewUser(event)">
            <div class="form-group">
                <label class="form-label">Nom</label>
                <input type="text" class="form-input" name="nom" required>
            </div>

            <div class="form-group">
                <label class="form-label">Prénom</label>
                <input type="text" class="form-input" name="prenom" required>
            </div>

            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" class="form-input" name="email" required>
            </div>

            <div class="form-group">
                <label class="form-label">Rôle</label>
                <select class="form-select" name="role_id" required>
                    <option value="">Sélectionner un rôle</option>
                    <option value="1">Administrateur</option>
                    <option value="2">Médecin</option>
                    <option value="3">Technicien</option>
                    <option value="4">Patient</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Service</label>
                <select class="form-select" name="service_id" required>
                    <option value="">Sélectionner un service</option>
                    <option value="1">Cardiologie</option>
                    <option value="2">Pneumologie</option>
                    <option value="3">Endocrinologie</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success" style="width: 100%; margin-top: 20px;">
                <i class="fas fa-user-plus"></i>
                Créer l'Utilisateur
            </button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/admin-navigation.js') }}"></script>
<script>
    // Initialisation de la page
    document.addEventListener('DOMContentLoaded', function() {
        // Définir la date/heure actuelle dans le formulaire de saisie manuelle
        const timestampInput = document.querySelector('input[name="timestamp"]');
        if (timestampInput) {
            const now = new Date();
            timestampInput.value = now.toISOString().slice(0, 16);
        }

        // Animation d'entrée pour les cartes
        animateCards();
    });

    // Gestion des capteurs
    function toggleSensor(sensorId, isActive) {
        const status = isActive ? 'activer' : 'désactiver';
        
        if (confirm(`Êtes-vous sûr de vouloir ${status} ce capteur ?`)) {
            // Simulation de l'appel API
            fetch(`/api/sensors/${sensorId}/toggle`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ active: isActive })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(`Capteur ${status} avec succès`, 'success');
                    updateSensorStatus(sensorId, isActive);
                } else {
                    showNotification('Erreur lors de la modification du capteur', 'error');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showNotification('Erreur de connexion', 'error');
            });
        }
    }

    function configureSensor(sensorId) {
        window.location.href = `/sensors/${sensorId}/configure`;
    }

    function updateSensorStatus(sensorId, isActive) {
        const sensorItem = document.querySelector(`input[onchange*="${sensorId}"]`).closest('.sensor-item');
        const statusElement = sensorItem.querySelector('.sensor-status');
        
        if (isActive) {
            statusElement.textContent = 'Actif';
            statusElement.className = 'sensor-status active';
        } else {
            statusElement.textContent = 'Inactif';
            statusElement.className = 'sensor-status inactive';
        }
    }

    // Gestion de la saisie manuelle
    function updateMeasurementFields(type) {
        const valueFields = document.getElementById('valueFields');
        const bloodPressureFields = document.getElementById('bloodPressureFields');
        
        if (type === 'blood_pressure') {
            valueFields.style.display = 'none';
            bloodPressureFields.style.display = 'block';
        } else {
            valueFields.style.display = 'block';
            bloodPressureFields.style.display = 'none';
        }
    }

    function submitManualData(event) {
        event.preventDefault();
        
        const formData = new FormData(event.target);
        const data = Object.fromEntries(formData.entries());
        
        fetch('/api/manual-data', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                showNotification('Données enregistrées avec succès', 'success');
                event.target.reset();
                // Remettre la date/heure actuelle
                document.querySelector('input[name="timestamp"]').value = new Date().toISOString().slice(0, 16);
            } else {
                showNotification('Erreur lors de l\'enregistrement', 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showNotification('Erreur de connexion', 'error');
        });
    }

    // Gestion des modales
    function openAddUserModal() {
        document.getElementById('addUserModal').style.display = 'block';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }

    function submitNewUser(event) {
        event.preventDefault();
        
        const formData = new FormData(event.target);
        const data = Object.fromEntries(formData.entries());
        
        fetch('/admin/utilisateurs', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                showNotification('Utilisateur créé avec succès', 'success');
                closeModal('addUserModal');
                event.target.reset();
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification(result.message || 'Erreur lors de la création', 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showNotification('Erreur de connexion', 'error');
        });
    }

    // Actions système
    function manageRoles() {
        window.location.href = '/admin/roles';
    }

    function viewInactiveUsers() {
        window.location.href = '/admin/utilisateurs?status=inactive';
    }

    function viewSensorDiagnostics() {
        window.location.href = '/admin/sensors/diagnostics';
    }


    // Actions rapides
    function viewRecentAlerts() {
        window.location.href = '/alerts?recent=1';
    }

    function generateReport() {
        window.location.href = '/admin/reports/generate';
    }

    function systemBackup() {
        if (confirm('Créer une sauvegarde système ?')) {
            showNotification('Sauvegarde en cours...', 'info');
        }
    }

    function viewStatistics() {
        window.location.href = '/admin/statistics';
    }

    // Utilitaires
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            z-index: 10000;
            animation: slideIn 0.3s ease;
        `;
        
        switch(type) {
            case 'success':
                notification.style.background = 'linear-gradient(135deg, #10b981, #059669)';
                break;
            case 'error':
                notification.style.background = 'linear-gradient(135deg, #ef4444, #dc2626)';
                break;
            case 'info':
                notification.style.background = 'linear-gradient(135deg, #3b82f6, #1d4ed8)';
                break;
        }
        
        notification.textContent = message;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 4000);
    }

    function animateCards() {
        const cards = document.querySelectorAll('.section-card, .stat-card');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'all 0.6s ease';
            
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
    }

    // Fermer les modales en cliquant à l'extérieur
    window.onclick = function(event) {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    }

    // Fonction générique pour naviguer vers les pages système
function navigateToSystemPage(page) {
    const routes = {
        'server-config': '/admin/system/server-config',
        'logs': '/admin/system/logs',
        'maintenance': '/admin/system/maintenance',
        'export-data': '/admin/system/export-data'
    };
    
    if (routes[page]) {
        window.location.href = routes[page];
    } else {
        console.error('Page système inconnue:', page);
    }
}

// Event listeners pour les boutons de navigation système
document.addEventListener('DOMContentLoaded', function() {
    // Bouton Configuration Serveur
    const serverConfigBtn = document.querySelector('[data-action="server-config"]');
    if (serverConfigBtn) {
        serverConfigBtn.addEventListener('click', function(e) {
            e.preventDefault();
            navigateToSystemPage('server-config');
        });
    }
    
    // Bouton Logs Système
    const logsBtn = document.querySelector('[data-action="system-logs"]');
    if (logsBtn) {
        logsBtn.addEventListener('click', function(e) {
            e.preventDefault();
            navigateToSystemPage('logs');
        });
    }
    
    // Bouton Maintenance
    const maintenanceBtn = document.querySelector('[data-action="maintenance"]');
    if (maintenanceBtn) {
        maintenanceBtn.addEventListener('click', function(e) {
            e.preventDefault();
            navigateToSystemPage('maintenance');
        });
    }
    
    // Bouton Export Données
    const exportBtn = document.querySelector('[data-action="export-data"]');
    if (exportBtn) {
        exportBtn.addEventListener('click', function(e) {
            e.preventDefault();
            navigateToSystemPage('export-data');
        });
    }
});

    // Ajouter les styles d'animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    `;
    document.head.appendChild(style);
</script>
@endsection