@extends('layouts.app')

@section('title', 'Historique et Statistiques | Système de Surveillance Médicale')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/history.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('content')
<div class="app-container">

        <div class="content-wrapper">
            <div class="card">
                <div class="card-header">
                    <h3>Filtres</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('history.index') }}" method="GET" class="filter-form">
                        <div class="filter-row">
                            <div class="filter-group">
                                <label for="patient_id">Patient:</label>
                                <select id="patient_id" name="patient_id" class="form-control">
                                    <option value="">Tous les patients</option>
                                    @foreach($patients ?? [] as $p)
                                        <option value="{{ $p->id }}" {{ $patientId == $p->id ? 'selected' : '' }}>
                                            {{ $p->name }} (#{{ $p->id }})
                                        </option>
                                    @endforeach
                                    @if(empty($patients) || count($patients) == 0)
                                        <option value="1" {{ $patientId == 1 ? 'selected' : '' }}>Mehdi Rais (#P001)</option>
                                        <option value="2" {{ $patientId == 2 ? 'selected' : '' }}>Sara Tazi (#P002)</option>
                                        <option value="3" {{ $patientId == 3 ? 'selected' : '' }}>Karim Alaoui (#P003)</option>
                                    @endif
                                </select>
                            </div>
                            <div class="filter-group">
                                <label for="date_from">Période:</label>
                                <div class="date-range">
                                    <input type="date" id="date_from" name="date_from" value="{{ $dateFrom ?? Carbon\Carbon::now()->subMonth()->toDateString() }}">
                                    <span>à</span>
                                    <input type="date" id="date_to" name="date_to" value="{{ $dateTo ?? Carbon\Carbon::now()->toDateString() }}">
                                </div>
                            </div>
                            <div class="filter-actions">
                                <button type="submit" class="primary-btn">
                                    <i class="fas fa-filter"></i> Appliquer
                                </button>
                                <a href="{{ route('history.export', ['patient_id' => $patientId, 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}" class="outline-btn">
                                    <i class="fas fa-file-export"></i> Exporter
                                </a>
                            </div>
                        </div>
                        <div class="view-selector">
                            <div class="btn-group">
                                <button type="button" class="view-btn {{ $view == 'daily' ? 'active' : '' }}" data-view="daily" onclick="changeView('daily')">
                                    <i class="fas fa-calendar-day"></i> Quotidien
                                </button>
                                <button type="button" class="view-btn {{ $view == 'weekly' ? 'active' : '' }}" data-view="weekly" onclick="changeView('weekly')">
                                    <i class="fas fa-calendar-week"></i> Hebdomadaire
                                </button>
                                <button type="button" class="view-btn {{ $view == 'monthly' ? 'active' : '' }}" data-view="monthly" onclick="changeView('monthly')">
                                    <i class="fas fa-calendar-alt"></i> Mensuel
                                </button>
                            </div>
                            <input type="hidden" name="view" id="view-input" value="{{ $view ?? 'daily' }}">
                        </div>
                    </form>
                </div>
            </div>

            <div class="charts-container">
                <div class="chart-card">
                    <div class="card-header">
                        <h3><i class="fas fa-heartbeat"></i> Fréquence Cardiaque</h3>
                        <div class="card-actions">
                            <button class="export-btn" onclick="exportChart('heart-rate-chart')">
                                <i class="fas fa-download"></i> Exporter
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-area">
                            <canvas id="heart-rate-chart"></canvas>
                        </div>
                        <div class="stats-summary">
                            <div class="stat">
                                <span class="stat-label">Moyenne</span>
                                <span class="stat-value">{{ $stats['heartRate']['avg'] ?? '78' }} bpm</span>
                            </div>
                            <div class="stat">
                                <span class="stat-label">Min</span>
                                <span class="stat-value">{{ $stats['heartRate']['min'] ?? '62' }} bpm</span>
                            </div>
                            <div class="stat">
                                <span class="stat-label">Max</span>
                                <span class="stat-value">{{ $stats['heartRate']['max'] ?? '95' }} bpm</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="chart-card">
                    <div class="card-header">
                        <h3><i class="fas fa-lungs"></i> Oxygénation (SpO2)</h3>
                        <div class="card-actions">
                            <button class="export-btn" onclick="exportChart('spo2-chart')">
                                <i class="fas fa-download"></i> Exporter
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-area">
                            <canvas id="spo2-chart"></canvas>
                        </div>
                        <div class="stats-summary">
                            <div class="stat">
                                <span class="stat-label">Moyenne</span>
                                <span class="stat-value">{{ $stats['spo2']['avg'] ?? '97' }}%</span>
                            </div>
                            <div class="stat">
                                <span class="stat-label">Min</span>
                                <span class="stat-value">{{ $stats['spo2']['min'] ?? '94' }}%</span>
                            </div>
                            <div class="stat">
                                <span class="stat-label">Max</span>
                                <span class="stat-value">{{ $stats['spo2']['max'] ?? '99' }}%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="chart-card">
                    <div class="card-header">
                        <h3><i class="fas fa-thermometer-half"></i> Température</h3>
                        <div class="card-actions">
                            <button class="export-btn" onclick="exportChart('temperature-chart')">
                                <i class="fas fa-download"></i> Exporter
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-area">
                            <canvas id="temperature-chart"></canvas>
                        </div>
                        <div class="stats-summary">
                            <div class="stat">
                                <span class="stat-label">Moyenne</span>
                                <span class="stat-value">{{ $stats['temperature']['avg'] ?? '37.1' }}°C</span>
                            </div>
                            <div class="stat">
                                <span class="stat-label">Min</span>
                                <span class="stat-value">{{ $stats['temperature']['min'] ?? '36.5' }}°C</span>
                            </div>
                            <div class="stat">
                                <span class="stat-label">Max</span>
                                <span class="stat-value">{{ $stats['temperature']['max'] ?? '37.8' }}°C</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="chart-card">
                    <div class="card-header">
                        <h3><i class="fas fa-tachometer-alt"></i> Tension Artérielle</h3>
                        <div class="card-actions">
                            <button class="export-btn" onclick="exportChart('blood-pressure-chart')">
                                <i class="fas fa-download"></i> Exporter
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-area">
                            <canvas id="blood-pressure-chart"></canvas>
                        </div>
                        <div class="stats-summary">
                            <div class="stat">
                                <span class="stat-label">Moyenne</span>
                                <span class="stat-value">{{ $stats['bloodPressure']['avg'] ?? '125/80' }}</span>
                            </div>
                            <div class="stat">
                                <span class="stat-label">Min</span>
                                <span class="stat-value">{{ $stats['bloodPressure']['min'] ?? '115/75' }}</span>
                            </div>
                            <div class="stat">
                                <span class="stat-label">Max</span>
                                <span class="stat-value">{{ $stats['bloodPressure']['max'] ?? '145/90' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-table"></i> Données Brutes</h3>
                    <div class="card-actions">
                        <button class="export-btn" onclick="exportTableData()">
                            <i class="fas fa-file-csv"></i> Exporter CSV
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Heure</th>
                                    <th>Patient</th>
                                    <th>Type</th>
                                    <th>Valeur</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($readings) && count($readings) > 0)
                                    @foreach($readings as $reading)
                                        <tr>
                                            <td>{{ Carbon\Carbon::parse($reading->timestamp)->format('d/m/Y') }}</td>
                                            <td>{{ Carbon\Carbon::parse($reading->timestamp)->format('H:i') }}</td>
                                            <td>
                                                @if($reading->sensor && $reading->sensor->patient)
                                                    {{ $reading->sensor->patient->name }}
                                                @else
                                                    Patient Inconnu
                                                @endif
                                            </td>
                                            <td>{{ $reading->sensor->type ?? 'Inconnu' }}</td>
                                            <td>{{ $reading->value }}</td>
                                            <td>
                                                <a href="{{ route('history.details', [
                                                    'patient' => $reading->sensor->patient_id ?? '', 
                                                    'date' => Carbon\Carbon::parse($reading->timestamp)->toDateString()
                                                ]) }}" class="btn-icon" title="Voir détails">
                                                    <i class="fas fa-search"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6" class="empty-cell">Aucune donnée disponible pour la période sélectionnée.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    
                    @if(isset($readings) && $readings instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        <div class="pagination-container">
                            {{ $readings->appends(request()->query())->links('pagination.custun') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Données pour les graphiques
    const chartData = @json($chartData ?? []);
    
    // Fonction pour changer la vue (quotidien, hebdomadaire, mensuel)
    function changeView(view) {
        document.getElementById('view-input').value = view;
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.classList.remove('active');
            if (btn.getAttribute('data-view') === view) {
                btn.classList.add('active');
            }
        });
        document.querySelector('.filter-form').submit();
    }
    
    // Initialiser les graphiques
    document.addEventListener('DOMContentLoaded', function() {
        // Configuration des graphiques
        initHeartRateChart();
        initSpo2Chart();
        initTemperatureChart();
        initBloodPressureChart();
    });
    
    function initHeartRateChart() {
        const ctx = document.getElementById('heart-rate-chart').getContext('2d');
        
        if (!chartData.heartRate) return;
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.heartRate.labels,
                datasets: [{
                    label: 'BPM',
                    data: chartData.heartRate.data,
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    borderColor: 'rgba(239, 68, 68, 1)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(239, 68, 68, 1)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(255, 255, 255, 0.9)',
                        titleColor: '#1f2937',
                        bodyColor: '#1f2937',
                        borderColor: 'rgba(0, 0, 0, 0.1)',
                        borderWidth: 1,
                        padding: 10,
                        displayColors: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
    
    function initSpo2Chart() {
        const ctx = document.getElementById('spo2-chart').getContext('2d');
        
        if (!chartData.spo2) return;
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.spo2.labels,
                datasets: [{
                    label: '%',
                    data: chartData.spo2.data,
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(59, 130, 246, 1)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(255, 255, 255, 0.9)',
                        titleColor: '#1f2937',
                        bodyColor: '#1f2937',
                        borderColor: 'rgba(0, 0, 0, 0.1)',
                        borderWidth: 1,
                        padding: 10,
                        displayColors: false
                    }
                },
                scales: {
                    y: {
                        min: 90,
                        max: 100,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
    
    function initTemperatureChart() {
        const ctx = document.getElementById('temperature-chart').getContext('2d');
        
        if (!chartData.temperature) return;
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.temperature.labels,
                datasets: [{
                    label: '°C',
                    data: chartData.temperature.data,
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    borderColor: 'rgba(245, 158, 11, 1)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(245, 158, 11, 1)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(255, 255, 255, 0.9)',
                        titleColor: '#1f2937',
                        bodyColor: '#1f2937',
                        borderColor: 'rgba(0, 0, 0, 0.1)',
                        borderWidth: 1,
                        padding: 10,
                        displayColors: false
                    }
                },
                scales: {
                    y: {
                        min: 36,
                        max: 38,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
    
    function initBloodPressureChart() {
        const ctx = document.getElementById('blood-pressure-chart').getContext('2d');
        
        if (!chartData.bloodPressure) return;
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.bloodPressure.labels,
                datasets: [
                    {
                        label: 'Systolique',
                        data: chartData.bloodPressure.systolic,
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderColor: 'rgba(16, 185, 129, 1)',
                        borderWidth: 2,
                        pointBackgroundColor: 'rgba(16, 185, 129, 1)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Diastolique',
                        data: chartData.bloodPressure.diastolic,
                        backgroundColor: 'rgba(52, 211, 153, 0.1)',
                        borderColor: 'rgba(52, 211, 153, 1)',
                        borderWidth: 2,
                        pointBackgroundColor: 'rgba(52, 211, 153, 1)',
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        backgroundColor: 'rgba(255, 255, 255, 0.9)',
                        titleColor: '#1f2937',
                        bodyColor: '#1f2937',
                        borderColor: 'rgba(0, 0, 0, 0.1)',
                        borderWidth: 1,
                        padding: 10
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
    
    // Fonctions pour l'export
    function exportChart(chartId) {
        const canvas = document.getElementById(chartId);
        const image = canvas.toDataURL('image/png');
        
        // Créer un lien temporaire pour télécharger l'image
        const link = document.createElement('a');
        link.download = `${chartId}-export-${new Date().toISOString().slice(0, 10)}.png`;
        link.href = image;
        link.click();
    }
    
    function exportTableData() {
        // Simuler un téléchargement
        alert("Export CSV en cours de développement...");
    }
</script>
@endsection