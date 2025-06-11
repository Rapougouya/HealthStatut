@extends('layouts.app')

@section('title', 'Gestion des Capteurs - Système de Monitoring Médical')

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --card-bg: rgba(255, 255, 255, 0.95);
            --text-primary: #2d3748;
            --text-secondary: #718096;
            --border-color: rgba(255, 255, 255, 0.2);
            --sensor-active: #10b981;
            --sensor-inactive: #ef4444;
            --sensor-warning: #f59e0b;
        }

        .sensors-container {
            background: var(--primary-gradient);
            min-height: 100vh;
            padding: 20px;
        }

        .page-header {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid var(--border-color);
        }

        .page-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .title-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .title-left h1 {
            color: var(--text-primary);
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
            background: linear-gradient(135deg, #667eea, #764ba2);
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
            background: linear-gradient(135deg, #667eea, #764ba2);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: var(--card-bg);
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

        .stat-card.active { border-left-color: var(--sensor-active); }
        .stat-card.inactive { border-left-color: var(--sensor-inactive); }
        .stat-card.warning { border-left-color: var(--sensor-warning); }
        .stat-card.total { border-left-color: #667eea; }

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

        .stat-icon.active { background: var(--sensor-active); }
        .stat-icon.inactive { background: var(--sensor-inactive); }
        .stat-icon.warning { background: var(--sensor-warning); }
        .stat-icon.total { background: #667eea; }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .stat-label {
            font-size: 0.9rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .controls-section {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .controls-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .controls-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .search-box {
            position: relative;
            width: 300px;
        }

        .search-input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 2px solid rgba(102, 126, 234, 0.1);
            border-radius: 10px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
        }

        .search-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            background: white;
        }

        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
        }

        .filter-dropdown {
            padding: 12px 15px;
            border: 2px solid rgba(102, 126, 234, 0.1);
            border-radius: 10px;
            font-size: 0.9rem;
            background: rgba(255, 255, 255, 0.8);
            cursor: pointer;
        }

        .btn {
            padding: 12px 20px;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            border: none;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
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

        .sensors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .sensor-card {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .sensor-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
        }

        .sensor-card.active::before { background: var(--sensor-active); }
        .sensor-card.inactive::before { background: var(--sensor-inactive); }
        .sensor-card.warning::before { background: var(--sensor-warning); }

        .sensor-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .sensor-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .sensor-type {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sensor-type-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }

        .sensor-type-icon.heartrate { background: linear-gradient(135deg, #ef4444, #dc2626); }
        .sensor-type-icon.temperature { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .sensor-type-icon.oxygen { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
        .sensor-type-icon.pressure { background: linear-gradient(135deg, #10b981, #059669); }

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
            color: var(--sensor-active);
        }

        .sensor-status.inactive {
            background: rgba(239, 68, 68, 0.1);
            color: var(--sensor-inactive);
        }

        .sensor-status.warning {
            background: rgba(245, 158, 11, 0.1);
            color: var(--sensor-warning);
        }

        .sensor-info {
            margin-bottom: 15px;
        }

        .sensor-id {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 5px;
        }

        .sensor-model {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .sensor-patient {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 15px;
            padding: 15px;
            background: rgba(248, 250, 252, 0.8);
            border-radius: 10px;
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
            color: var(--text-primary);
        }

        .patient-info p {
            margin: 0;
            font-size: 0.8rem;
            color: var(--text-secondary);
        }

        .sensor-metrics {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        .metric-item {
            text-align: center;
            padding: 10px;
            background: rgba(248, 250, 252, 0.5);
            border-radius: 8px;
        }

        .metric-value {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .metric-label {
            font-size: 0.8rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .sensor-actions {
            display: flex;
            gap: 10px;
        }

        .btn-sm {
            padding: 8px 12px;
            font-size: 0.8rem;
        }

        @media (max-width: 768px) {
            .sensors-container { padding: 15px; }
            .page-header { padding: 20px; }
            .title-left h1 { font-size: 2rem; }
            .sensors-grid { grid-template-columns: 1fr; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .controls-left { flex-direction: column; align-items: stretch; }
            .search-box { width: 100%; }
        }
    </style>
@endsection

@section('content')
<div class="sensors-container">
    <!-- En-tête de la page -->
    <div class="page-header">
        <div class="page-title">
            <div class="title-left">
                <div class="title-icon">
                    <i class="fas fa-microchip"></i>
                </div>
                <h1>Gestion des Capteurs</h1>
            </div>
            <button class="btn btn-primary" onclick="openAddSensorModal()">
                <i class="fas fa-plus"></i>
                Ajouter un capteur
            </button>
        </div>

        <!-- Statistiques des capteurs -->
        <div class="stats-grid">
            <div class="stat-card total">
                <div class="stat-header">
                    <div class="stat-icon total">
                        <i class="fas fa-microchip"></i>
                    </div>
                    <div class="stat-value">{{ $stats['total'] ?? '24' }}</div>
                </div>
                <div class="stat-label">Total Capteurs</div>
            </div>

            <div class="stat-card active">
                <div class="stat-header">
                    <div class="stat-icon active">
                        <i class="fas fa-circle"></i>
                    </div>
                    <div class="stat-value">{{ $stats['active'] ?? '18' }}</div>
                </div>
                <div class="stat-label">Capteurs Actifs</div>
            </div>

            <div class="stat-card warning">
                <div class="stat-header">
                    <div class="stat-icon warning">
                        <i class="fas fa-battery-quarter"></i>
                    </div>
                    <div class="stat-value">{{ $stats['warning'] ?? '4' }}</div>
                </div>
                <div class="stat-label">Batterie Faible</div>
            </div>

            <div class="stat-card inactive">
                <div class="stat-header">
                    <div class="stat-icon inactive">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="stat-value">{{ $stats['inactive'] ?? '2' }}</div>
                </div>
                <div class="stat-label">Capteurs Inactifs</div>
            </div>
        </div>
    </div>

    <!-- Section de contrôles -->
    <div class="controls-section">
        <div class="controls-header">
            <div class="controls-left">
                <div class="search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="Rechercher un capteur..." 
                           onkeyup="filterSensors(this.value)">
                </div>
                <select class="filter-dropdown" onchange="filterByType(this.value)">
                    <option value="">Tous les types</option>
                    <option value="heartrate">Fréquence cardiaque</option>
                    <option value="temperature">Température</option>
                    <option value="oxygen">Saturation O₂</option>
                    <option value="pressure">Pression artérielle</option>
                </select>
                <select class="filter-dropdown" onchange="filterByStatus(this.value)">
                    <option value="">Tous les statuts</option>
                    <option value="active">Actif</option>
                    <option value="inactive">Inactif</option>
                    <option value="warning">Batterie faible</option>
                </select>
            </div>
            <button class="btn btn-secondary" onclick="refreshSensors()">
                <i class="fas fa-sync-alt"></i>
                Actualiser
            </button>
        </div>
    </div>

    <!-- Grille des capteurs -->
    <div class="sensors-grid" id="sensorsGrid">
        @forelse($sensors ?? [] as $sensor)
            <div class="sensor-card {{ $sensor->status ?? 'active' }}" data-type="{{ $sensor->type ?? 'heartrate' }}" data-status="{{ $sensor->status ?? 'active' }}">
                <div class="sensor-header">
                    <div class="sensor-type">
                        <div class="sensor-type-icon {{ $sensor->type ?? 'heartrate' }}">
                            @switch($sensor->type ?? 'heartrate')
                                @case('heartrate')
                                    <i class="fas fa-heartbeat"></i>
                                    @break
                                @case('temperature')
                                    <i class="fas fa-thermometer-half"></i>
                                    @break
                                @case('oxygen')
                                    <i class="fas fa-lungs"></i>
                                    @break
                                @case('pressure')
                                    <i class="fas fa-tachometer-alt"></i>
                                    @break
                                @default
                                    <i class="fas fa-microchip"></i>
                            @endswitch
                        </div>
                        <div>
                            <div class="sensor-id">{{ $sensor->sensor_id ?? 'SEN-001' }}</div>
                            <div class="sensor-model">{{ $sensor->model ?? 'CardioSense Pro' }}</div>
                        </div>
                    </div>
                    <span class="sensor-status {{ $sensor->status ?? 'active' }}">
                        @switch($sensor->status ?? 'active')
                            @case('active')
                                <i class="fas fa-circle"></i> Actif
                                @break
                            @case('inactive')
                                <i class="fas fa-times-circle"></i> Inactif
                                @break
                            @case('warning')
                                <i class="fas fa-battery-quarter"></i> Batterie faible
                                @break
                            @default
                                <i class="fas fa-question-circle"></i> Inconnu
                        @endswitch
                    </span>
                </div>

                @if($sensor->patient ?? true)
                    <div class="sensor-patient">
                        <div class="patient-avatar">
                            {{ substr($sensor->patient->prenom ?? 'M', 0, 1) }}{{ substr($sensor->patient->nom ?? 'R', 0, 1) }}
                        </div>
                        <div class="patient-info">
                            <h4>{{ $sensor->patient->nom_complet ?? 'Mehdi Rais' }}</h4>
                            <p><i class="fas fa-id-card"></i> ID: {{ $sensor->patient->id ?? '001' }}</p>
                        </div>
                    </div>
                @else
                    <div class="sensor-patient" style="justify-content: center; color: var(--text-secondary);">
                        <i class="fas fa-user-slash"></i>
                        <span>Aucun patient assigné</span>
                    </div>
                @endif

                <div class="sensor-metrics">
                    <div class="metric-item">
                        <div class="metric-value">{{ $sensor->battery_level ?? '85' }}%</div>
                        <div class="metric-label">Batterie</div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-value">{{ $sensor->signal_strength ?? '92' }}%</div>
                        <div class="metric-label">Signal</div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-value">{{ $sensor->last_reading ?? '36.8' }}</div>
                        <div class="metric-label">Dernière lecture</div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-value">{{ \Carbon\Carbon::parse($sensor->last_reading_at ?? now())->diffForHumans() }}</div>
                        <div class="metric-label">Mise à jour</div>
                    </div>
                </div>

                <div class="sensor-actions">
                    <button class="btn btn-primary btn-sm" onclick="configSensor('{{ $sensor->id ?? 1 }}')">
                        <i class="fas fa-cog"></i>
                        Configurer
                    </button>
                    <button class="btn btn-secondary btn-sm" onclick="viewSensorHistory('{{ $sensor->id ?? 1 }}')">
                        <i class="fas fa-chart-line"></i>
                        Historique
                    </button>
                    <button class="btn btn-secondary btn-sm" onclick="editSensor('{{ $sensor->id ?? 1 }}')">
                        <i class="fas fa-edit"></i>
                        Modifier
                    </button>
                </div>
            </div>
        @empty
            <!-- Capteurs d'exemple si aucune donnée -->
            <div class="sensor-card active" data-type="heartrate" data-status="active">
                <div class="sensor-header">
                    <div class="sensor-type">
                        <div class="sensor-type-icon heartrate">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                        <div>
                            <div class="sensor-id">SEN-001</div>
                            <div class="sensor-model">CardioSense Pro</div>
                        </div>
                    </div>
                    <span class="sensor-status active">
                        <i class="fas fa-circle"></i> Actif
                    </span>
                </div>

                <div class="sensor-patient">
                    <div class="patient-avatar">MR</div>
                    <div class="patient-info">
                        <h4>Mehdi Rais</h4>
                        <p><i class="fas fa-id-card"></i> ID: 001</p>
                    </div>
                </div>

                <div class="sensor-metrics">
                    <div class="metric-item">
                        <div class="metric-value">85%</div>
                        <div class="metric-label">Batterie</div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-value">92%</div>
                        <div class="metric-label">Signal</div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-value">78 bpm</div>
                        <div class="metric-label">Dernière lecture</div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-value">Il y a 2 min</div>
                        <div class="metric-label">Mise à jour</div>
                    </div>
                </div>

                <div class="sensor-actions">
                    <button class="btn btn-primary btn-sm" onclick="configSensor('1')">
                        <i class="fas fa-cog"></i>
                        Configurer
                    </button>
                    <button class="btn btn-secondary btn-sm" onclick="viewSensorHistory('1')">
                        <i class="fas fa-chart-line"></i>
                        Historique
                    </button>
                    <button class="btn btn-secondary btn-sm" onclick="editSensor('1')">
                        <i class="fas fa-edit"></i>
                        Modifier
                    </button>
                </div>
            </div>

            <div class="sensor-card warning" data-type="temperature" data-status="warning">
                <div class="sensor-header">
                    <div class="sensor-type">
                        <div class="sensor-type-icon temperature">
                            <i class="fas fa-thermometer-half"></i>
                        </div>
                        <div>
                            <div class="sensor-id">SEN-002</div>
                            <div class="sensor-model">ThermoGuard Plus</div>
                        </div>
                    </div>
                    <span class="sensor-status warning">
                        <i class="fas fa-battery-quarter"></i> Batterie faible
                    </span>
                </div>

                <div class="sensor-patient">
                    <div class="patient-avatar">ST</div>
                    <div class="patient-info">
                        <h4>Sara Tazi</h4>
                        <p><i class="fas fa-id-card"></i> ID: 002</p>
                    </div>
                </div>

                <div class="sensor-metrics">
                    <div class="metric-item">
                        <div class="metric-value">15%</div>
                        <div class="metric-label">Batterie</div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-value">88%</div>
                        <div class="metric-label">Signal</div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-value">37.2°C</div>
                        <div class="metric-label">Dernière lecture</div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-value">Il y a 5 min</div>
                        <div class="metric-label">Mise à jour</div>
                    </div>
                </div>

                <div class="sensor-actions">
                    <button class="btn btn-primary btn-sm" onclick="configSensor('2')">
                        <i class="fas fa-cog"></i>
                        Configurer
                    </button>
                    <button class="btn btn-secondary btn-sm" onclick="viewSensorHistory('2')">
                        <i class="fas fa-chart-line"></i>
                        Historique
                    </button>
                    <button class="btn btn-secondary btn-sm" onclick="editSensor('2')">
                        <i class="fas fa-edit"></i>
                        Modifier
                    </button>
                </div>
            </div>

            <div class="sensor-card active" data-type="oxygen" data-status="active">
                <div class="sensor-header">
                    <div class="sensor-type">
                        <div class="sensor-type-icon oxygen">
                            <i class="fas fa-lungs"></i>
                        </div>
                        <div>
                            <div class="sensor-id">SEN-003</div>
                            <div class="sensor-model">OxyTrack Elite</div>
                        </div>
                    </div>
                    <span class="sensor-status active">
                        <i class="fas fa-circle"></i> Actif
                    </span>
                </div>

                <div class="sensor-patient">
                    <div class="patient-avatar">KA</div>
                    <div class="patient-info">
                        <h4>Karim Alaoui</h4>
                        <p><i class="fas fa-id-card"></i> ID: 003</p>
                    </div>
                </div>

                <div class="sensor-metrics">
                    <div class="metric-item">
                        <div class="metric-value">92%</div>
                        <div class="metric-label">Batterie</div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-value">96%</div>
                        <div class="metric-label">Signal</div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-value">98%</div>
                        <div class="metric-label">Dernière lecture</div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-value">Il y a 1 min</div>
                        <div class="metric-label">Mise à jour</div>
                    </div>
                </div>

                <div class="sensor-actions">
                    <button class="btn btn-primary btn-sm" onclick="configSensor('3')">
                        <i class="fas fa-cog"></i>
                        Configurer
                    </button>
                    <button class="btn btn-secondary btn-sm" onclick="viewSensorHistory('3')">
                        <i class="fas fa-chart-line"></i>
                        Historique
                    </button>
                    <button class="btn btn-secondary btn-sm" onclick="editSensor('3')">
                        <i class="fas fa-edit"></i>
                        Modifier
                    </button>
                </div>
            </div>

            <div class="sensor-card inactive" data-type="pressure" data-status="inactive">
                <div class="sensor-header">
                    <div class="sensor-type">
                        <div class="sensor-type-icon pressure">
                            <i class="fas fa-tachometer-alt"></i>
                        </div>
                        <div>
                            <div class="sensor-id">SEN-004</div>
                            <div class="sensor-model">PressureMax Pro</div>
                        </div>
                    </div>
                    <span class="sensor-status inactive">
                        <i class="fas fa-times-circle"></i> Inactif
                    </span>
                </div>

                <div class="sensor-patient" style="justify-content: center; color: var(--text-secondary);">
                    <i class="fas fa-user-slash"></i>
                    <span>Aucun patient assigné</span>
                </div>

                <div class="sensor-metrics">
                    <div class="metric-item">
                        <div class="metric-value">0%</div>
                        <div class="metric-label">Batterie</div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-value">0%</div>
                        <div class="metric-label">Signal</div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-value">--</div>
                        <div class="metric-label">Dernière lecture</div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-value">Il y a 2j</div>
                        <div class="metric-label">Mise à jour</div>
                    </div>
                </div>

                <div class="sensor-actions">
                    <button class="btn btn-primary btn-sm" onclick="configSensor('4')">
                        <i class="fas fa-cog"></i>
                        Configurer
                    </button>
                    <button class="btn btn-secondary btn-sm" onclick="viewSensorHistory('4')">
                        <i class="fas fa-chart-line"></i>
                        Historique
                    </button>
                    <button class="btn btn-secondary btn-sm" onclick="editSensor('4')">
                        <i class="fas fa-edit"></i>
                        Modifier
                    </button>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection

@section('scripts')
<script>
    function filterSensors(searchTerm) {
        const cards = document.querySelectorAll('.sensor-card');
        const lowerSearchTerm = searchTerm.toLowerCase();
        
        cards.forEach(card => {
            const sensorId = card.querySelector('.sensor-id').textContent.toLowerCase();
            const sensorModel = card.querySelector('.sensor-model').textContent.toLowerCase();
            const patientName = card.querySelector('.patient-info h4')?.textContent.toLowerCase() || '';
            
            if (sensorId.includes(lowerSearchTerm) || 
                sensorModel.includes(lowerSearchTerm) || 
                patientName.includes(lowerSearchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function filterByType(type) {
        const cards = document.querySelectorAll('.sensor-card');
        
        cards.forEach(card => {
            if (!type || card.dataset.type === type) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function filterByStatus(status) {
        const cards = document.querySelectorAll('.sensor-card');
        
        cards.forEach(card => {
            if (!status || card.dataset.status === status) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function refreshSensors() {
        location.reload();
    }

    function openAddSensorModal() {
        // Redirection vers la page d'ajout de capteur
        window.location.href = '/sensors/create';
    }

    function configSensor(sensorId) {
        // Redirection vers la page de configuration
        window.location.href = `/sensors/${sensorId}/configure`;
    }

    function viewSensorHistory(sensorId) {
        // Redirection vers l'historique du capteur
        window.location.href = `/sensors/${sensorId}/history`;
    }

    function editSensor(sensorId) {
        // Redirection vers la page d'édition
        window.location.href = `/sensors/${sensorId}/edit`;
    }

    // Animation d'entrée pour les cartes
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.sensor-card');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'all 0.6s ease';
            
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
    });
</script>
@endsection