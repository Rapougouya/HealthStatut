@extends('layouts.app')

@section('title', 'Historique et Statistiques - Système de Monitoring Médical')

@section('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --card-bg: rgba(255, 255, 255, 0.95);
            --text-primary: #2d3748;
            --text-secondary: #718096;
            --border-color: rgba(255, 255, 255, 0.2);
        }

        .history-container {
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
            gap: 20px;
            margin-bottom: 20px;
        }

        .page-title h1 {
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

        .filters-section {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .filter-group label {
            font-weight: 600;
            color: var(--text-primary);
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filter-input {
            padding: 12px 15px;
            border: 2px solid rgba(102, 126, 234, 0.1);
            border-radius: 10px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
        }

        .filter-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            background: white;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .chart-card {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .chart-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .chart-header {
            display: flex;
            align-items: center;
            justify-content: between;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .chart-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .chart-icon {
            width: 35px;
            height: 35px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            color: white;
        }

        .chart-icon.heart { background: linear-gradient(135deg, #ef4444, #dc2626); }
        .chart-icon.oxygen { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
        .chart-icon.temp { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .chart-icon.pressure { background: linear-gradient(135deg, #10b981, #059669); }

        .btn {
            padding: 10px 15px;
            border-radius: 8px;
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

        .chart-container {
            position: relative;
            height: 300px;
            margin-bottom: 20px;
        }

        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
            gap: 15px;
            padding-top: 15px;
            border-top: 1px solid rgba(102, 126, 234, 0.1);
        }

        .stat-item {
            text-align: center;
        }

        .stat-value {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .stat-label {
            font-size: 0.8rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .realtime-indicator {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.8rem;
            color: #10b981;
            margin-left: auto;
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }

        .data-table-section {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid var(--border-color);
        }

        .table-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }

        .data-table th,
        .data-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid rgba(102, 126, 234, 0.1);
        }

        .data-table th {
            background: rgba(102, 126, 234, 0.05);
            font-weight: 600;
            color: var(--text-primary);
        }

        .data-table tr:hover {
            background: rgba(102, 126, 234, 0.03);
        }

        @media (max-width: 768px) {
            .history-container { padding: 15px; }
            .page-header { padding: 20px; }
            .page-title h1 { font-size: 2rem; }
            .charts-grid { grid-template-columns: 1fr; }
            .filters-grid { grid-template-columns: 1fr; }
        }
    </style>
@endsection

@section('content')
<div class="history-container">
    <!-- En-tête de la page -->
    <div class="page-header">
        <div class="page-title">
            <div class="title-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <h1>Historique et Statistiques</h1>
            <div class="realtime-indicator">
                <div class="pulse-dot"></div>
                Temps réel
            </div>
        </div>
    </div>

    <!-- Section de filtrage -->
    <div class="filters-section">
        <form action="{{ route('history.index') }}" method="GET">
            <div class="filters-grid">
                <div class="filter-group">
                    <label for="patient_id">
                        <i class="fas fa-user"></i>
                        Patient
                    </label>
                    <select name="patient_id" id="patient_id" class="filter-input">
                        <option value="">Tous les patients</option>
                        @foreach($patients ?? [] as $patient)
                            <option value="{{ $patient->id }}" {{ request('patient_id') == $patient->id ? 'selected' : '' }}>
                                {{ $patient->nom_complet ?? $patient->nom . ' ' . $patient->prenom }}
                            </option>
                        @endforeach
                        @if(empty($patients))
                            <option value="1" {{ request('patient_id') == 1 ? 'selected' : '' }}>Mehdi Rais</option>
                            <option value="2" {{ request('patient_id') == 2 ? 'selected' : '' }}>Sara Tazi</option>
                            <option value="3" {{ request('patient_id') == 3 ? 'selected' : '' }}>Karim Alaoui</option>
                        @endif
                    </select>
                </div>

                <div class="filter-group">
                    <label for="date_from">
                        <i class="fas fa-calendar-alt"></i>
                        Date de début
                    </label>
                    <input type="date" name="date_from" id="date_from" class="filter-input" 
                           value="{{ $dateFrom ?? date('Y-m-d', strtotime('-7 days')) }}">
                </div>

                <div class="filter-group">
                    <label for="date_to">
                        <i class="fas fa-calendar-alt"></i>
                        Date de fin
                    </label>
                    <input type="date" name="date_to" id="date_to" class="filter-input" 
                           value="{{ $dateTo ?? date('Y-m-d') }}">
                </div>

                <div class="filter-group">
                    <label for="view">
                        <i class="fas fa-eye"></i>
                        Vue
                    </label>
                    <select name="view" id="view" class="filter-input">
                        <option value="daily" {{ ($view ?? 'daily') == 'daily' ? 'selected' : '' }}>Quotidienne</option>
                        <option value="weekly" {{ ($view ?? 'daily') == 'weekly' ? 'selected' : '' }}>Hebdomadaire</option>
                        <option value="monthly" {{ ($view ?? 'daily') == 'monthly' ? 'selected' : '' }}>Mensuelle</option>
                    </select>
                </div>
            </div>

            <div style="display: flex; gap: 15px; margin-top: 20px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                    Appliquer les filtres
                </button>
                <a href="{{ route('history.index') }}" class="btn btn-secondary">
                    <i class="fas fa-refresh"></i>
                    Réinitialiser
                </a>
            </div>
        </form>
    </div>

    <!-- Grille des graphiques -->
    <div class="charts-grid">
        <!-- Graphique Fréquence Cardiaque -->
        <div class="chart-card">
            <div class="chart-header">
                <div class="chart-title">
                    <div class="chart-icon heart">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    Fréquence Cardiaque
                </div>
                <button class="btn btn-secondary btn-sm" onclick="exportChart('heartRateChart')">
                    <i class="fas fa-download"></i>
                    Exporter
                </button>
            </div>
            <div class="chart-container">
                <canvas id="heartRateChart"></canvas>
            </div>
            <div class="stats-row">
                <div class="stat-item">
                    <div class="stat-value">{{ $stats['heartRate']['avg'] ?? '78' }}</div>
                    <div class="stat-label">Moyenne</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $stats['heartRate']['min'] ?? '62' }}</div>
                    <div class="stat-label">Min</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $stats['heartRate']['max'] ?? '95' }}</div>
                    <div class="stat-label">Max</div>
                </div>
            </div>
        </div>

        <!-- Graphique Saturation O2 -->
        <div class="chart-card">
            <div class="chart-header">
                <div class="chart-title">
                    <div class="chart-icon oxygen">
                        <i class="fas fa-lungs"></i>
                    </div>
                    Saturation O₂
                </div>
                <button class="btn btn-secondary btn-sm" onclick="exportChart('spo2Chart')">
                    <i class="fas fa-download"></i>
                    Exporter
                </button>
            </div>
            <div class="chart-container">
                <canvas id="spo2Chart"></canvas>
            </div>
            <div class="stats-row">
                <div class="stat-item">
                    <div class="stat-value">{{ $stats['spo2']['avg'] ?? '97' }}%</div>
                    <div class="stat-label">Moyenne</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $stats['spo2']['min'] ?? '94' }}%</div>
                    <div class="stat-label">Min</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $stats['spo2']['max'] ?? '99' }}%</div>
                    <div class="stat-label">Max</div>
                </div>
            </div>
        </div>

        <!-- Graphique Température -->
        <div class="chart-card">
            <div class="chart-header">
                <div class="chart-title">
                    <div class="chart-icon temp">
                        <i class="fas fa-thermometer-half"></i>
                    </div>
                    Température
                </div>
                <button class="btn btn-secondary btn-sm" onclick="exportChart('temperatureChart')">
                    <i class="fas fa-download"></i>
                    Exporter
                </button>
            </div>
            <div class="chart-container">
                <canvas id="temperatureChart"></canvas>
            </div>
            <div class="stats-row">
                <div class="stat-item">
                    <div class="stat-value">{{ $stats['temperature']['avg'] ?? '37.1' }}°C</div>
                    <div class="stat-label">Moyenne</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $stats['temperature']['min'] ?? '36.5' }}°C</div>
                    <div class="stat-label">Min</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $stats['temperature']['max'] ?? '37.8' }}°C</div>
                    <div class="stat-label">Max</div>
                </div>
            </div>
        </div>

        <!-- Graphique Tension Artérielle -->
        <div class="chart-card">
            <div class="chart-header">
                <div class="chart-title">
                    <div class="chart-icon pressure">
                        <i class="fas fa-tachometer-alt"></i>
                    </div>
                    Tension Artérielle
                </div>
                <button class="btn btn-secondary btn-sm" onclick="exportChart('bloodPressureChart')">
                    <i class="fas fa-download"></i>
                    Exporter
                </button>
            </div>
            <div class="chart-container">
                <canvas id="bloodPressureChart"></canvas>
            </div>
            <div class="stats-row">
                <div class="stat-item">
                    <div class="stat-value">{{ $stats['bloodPressure']['avg'] ?? '125/80' }}</div>
                    <div class="stat-label">Moyenne</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $stats['bloodPressure']['min'] ?? '115/75' }}</div>
                    <div class="stat-label">Min</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $stats['bloodPressure']['max'] ?? '145/90' }}</div>
                    <div class="stat-label">Max</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section tableau de données -->
    <div class="data-table-section">
        <div class="table-header">
            <h3 style="margin: 0; color: var(--text-primary);">
                <i class="fas fa-table"></i>
                Données Brutes
            </h3>
            <a href="{{ route('history.export') }}" class="btn btn-primary">
                <i class="fas fa-file-csv"></i>
                Exporter CSV
            </a>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Date/Heure</th>
                    <th>Patient</th>
                    <th>Fréq. Card.</th>
                    <th>SpO₂</th>
                    <th>Température</th>
                    <th>Tension Art.</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($readings ?? [] as $reading)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($reading->timestamp ?? now())->format('d/m/Y H:i') }}</td>
                    <td>{{ $reading->sensor->patient->nom_complet ?? 'Patient' }}</td>
                    <td>{{ $reading->type == 'heart_rate' ? $reading->value . ' bpm' : '-' }}</td>
                    <td>{{ $reading->type == 'spo2' ? $reading->value . '%' : '-' }}</td>
                    <td>{{ $reading->type == 'temperature' ? $reading->value . '°C' : '-' }}</td>
                    <td>{{ $reading->type == 'blood_pressure' ? $reading->value : '-' }}</td>
                    <td>
                        <button class="btn btn-secondary btn-sm" onclick="viewDetails('{{ $reading->id ?? 1 }}')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <!-- Données d'exemple -->
                <tr>
                    <td>{{ date('d/m/Y H:i') }}</td>
                    <td>Mehdi Rais</td>
                    <td>78 bpm</td>
                    <td>97%</td>
                    <td>37.1°C</td>
                    <td>125/80</td>
                    <td>
                        <button class="btn btn-secondary btn-sm" onclick="viewDetails('1')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td>{{ date('d/m/Y H:i', strtotime('-1 hour')) }}</td>
                    <td>Sara Tazi</td>
                    <td>82 bpm</td>
                    <td>96%</td>
                    <td>36.9°C</td>
                    <td>130/85</td>
                    <td>
                        <button class="btn btn-secondary btn-sm" onclick="viewDetails('2')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td>{{ date('d/m/Y H:i', strtotime('-2 hours')) }}</td>
                    <td>Karim Alaoui</td>
                    <td>75 bpm</td>
                    <td>98%</td>
                    <td>36.8°C</td>
                    <td>120/78</td>
                    <td>
                        <button class="btn btn-secondary btn-sm" onclick="viewDetails('3')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Configuration des couleurs pour les graphiques
    const chartColors = {
        heartRate: '#ef4444',
        spo2: '#3b82f6',
        temperature: '#f59e0b',
        bloodPressure: {
            systolic: '#10b981',
            diastolic: '#059669'
        }
    };

    // Données pour les graphiques (à remplacer par les vraies données)
    @php
    $chartData = $chartData ?? [
        'heartRate' => [
            'labels' => ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
            'data' => [75, 78, 82, 79, 76, 80, 77]
        ],
        'spo2' => [
            'labels' => ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
            'data' => [97, 98, 96, 97, 98, 97, 96]
        ],
        'temperature' => [
            'labels' => ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
            'data' => [36.8, 36.9, 37.1, 37.0, 36.7, 36.8, 36.9]
        ],
        'bloodPressure' => [
            'labels' => ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
            'systolic' => [125, 130, 128, 135, 127, 126, 129],
            'diastolic' => [80, 82, 79, 84, 78, 80, 81]
        ]
    ];
@endphp


    let charts = {};

    document.addEventListener('DOMContentLoaded', function() {
        initializeCharts();
        startRealTimeUpdates();
    });

    function initializeCharts() {
        // Graphique Fréquence Cardiaque (Courbe)
        const heartRateCtx = document.getElementById('heartRateChart').getContext('2d');
        charts.heartRate = new Chart(heartRateCtx, {
            type: 'line',
            data: {
                labels: chartData.heartRate.labels,
                datasets: [{
                    label: 'Fréquence Cardiaque (bpm)',
                    data: chartData.heartRate.data,
                    borderColor: chartColors.heartRate,
                    backgroundColor: chartColors.heartRate + '20',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 60,
                        max: 100
                    }
                }
            }
        });

        // Graphique SpO2 (Histogramme)
        const spo2Ctx = document.getElementById('spo2Chart').getContext('2d');
        charts.spo2 = new Chart(spo2Ctx, {
            type: 'bar',
            data: {
                labels: chartData.spo2.labels,
                datasets: [{
                    label: 'Saturation O₂ (%)',
                    data: chartData.spo2.data,
                    backgroundColor: chartColors.spo2 + '80',
                    borderColor: chartColors.spo2,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 90,
                        max: 100
                    }
                }
            }
        });

        // Graphique Température (Courbe)
        const temperatureCtx = document.getElementById('temperatureChart').getContext('2d');
        charts.temperature = new Chart(temperatureCtx, {
            type: 'line',
            data: {
                labels: chartData.temperature.labels,
                datasets: [{
                    label: 'Température (°C)',
                    data: chartData.temperature.data,
                    borderColor: chartColors.temperature,
                    backgroundColor: chartColors.temperature + '20',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 36,
                        max: 38
                    }
                }
            }
        });

        // Graphique Tension Artérielle (Courbes multiples)
        const bloodPressureCtx = document.getElementById('bloodPressureChart').getContext('2d');
        charts.bloodPressure = new Chart(bloodPressureCtx, {
            type: 'line',
            data: {
                labels: chartData.bloodPressure.labels,
                datasets: [{
                    label: 'Systolique',
                    data: chartData.bloodPressure.systolic,
                    borderColor: chartColors.bloodPressure.systolic,
                    backgroundColor: chartColors.bloodPressure.systolic + '20',
                    borderWidth: 3,
                    tension: 0.4
                }, {
                    label: 'Diastolique',
                    data: chartData.bloodPressure.diastolic,
                    borderColor: chartColors.bloodPressure.diastolic,
                    backgroundColor: chartColors.bloodPressure.diastolic + '20',
                    borderWidth: 3,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 70,
                        max: 150
                    }
                }
            }
        });
    }

    function startRealTimeUpdates() {
        // Simulation de mise à jour en temps réel
        setInterval(function() {
            updateChartsWithNewData();
        }, 30000); // Mise à jour toutes les 30 secondes
    }

    function updateChartsWithNewData() {
        // Simulation de nouvelles données
        const newHeartRate = Math.floor(Math.random() * (95 - 65) + 65);
        const newSpo2 = Math.floor(Math.random() * (99 - 94) + 94);
        const newTemp = (Math.random() * (37.5 - 36.5) + 36.5).toFixed(1);
        const newSystolic = Math.floor(Math.random() * (140 - 110) + 110);
        const newDiastolic = Math.floor(Math.random() * (90 - 70) + 70);

        // Mettre à jour les graphiques
        const currentTime = new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });

        // Ajouter les nouvelles données et supprimer les anciennes
        Object.keys(charts).forEach(chartKey => {
            const chart = charts[chartKey];
            chart.data.labels.push(currentTime);
            chart.data.labels.shift();

            if (chartKey === 'heartRate') {
                chart.data.datasets[0].data.push(newHeartRate);
                chart.data.datasets[0].data.shift();
            } else if (chartKey === 'spo2') {
                chart.data.datasets[0].data.push(newSpo2);
                chart.data.datasets[0].data.shift();
            } else if (chartKey === 'temperature') {
                chart.data.datasets[0].data.push(parseFloat(newTemp));
                chart.data.datasets[0].data.shift();
            } else if (chartKey === 'bloodPressure') {
                chart.data.datasets[0].data.push(newSystolic);
                chart.data.datasets[0].data.shift();
                chart.data.datasets[1].data.push(newDiastolic);
                chart.data.datasets[1].data.shift();
            }

            chart.update('none');
        });

        console.log('Données mises à jour:', { newHeartRate, newSpo2, newTemp, newSystolic, newDiastolic });
    }

    function exportChart(chartId) {
        const chartKey = chartId.replace('Chart', '');
        if (charts[chartKey]) {
            const url = charts[chartKey].toBase64Image();
            const link = document.createElement('a');
            link.download = `graphique-${chartKey}-${new Date().toISOString().split('T')[0]}.png`;
            link.href = url;
            link.click();
            
            console.log(`Export du graphique: ${chartKey}`);
        }
    }

    function viewDetails(readingId) {
        // Redirection vers la page de détails
        console.log(`Affichage des détails pour la lecture: ${readingId}`);
        // window.location.href = `/history/details/${readingId}`;
    }
</script>
<script>
    const chartData = @json($chartData);
</script>
@endsection