@extends('layouts.app')

@section('title', 'Historique et Statistiques | Système de Surveillance Médicale')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/historique.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('content')
<div class="app-container">
    @include('patials.sidebar')

    <div class="main-content">
        <div class="main-header">
            <h1>Historique et Statistiques</h1>
            <div class="user-info">
                <span>{{ $doctor->name ?? 'Dr. Ahmed' }}</span>
                <img src="{{ $doctor->avatar ?? 'https://randomuser.me/api/portraits/men/4.jpg' }}" alt="User" class="avatar">
            </div>
        </div>

        <div class="history-container">
            <div class="filter-section">
                <div class="patient-selector">
                    <label for="patient-select">Patient:</label>
                    <select id="patient-select">
                        <option value="">Tous les patients</option>
                        @foreach($patients ?? [] as $patient)
                            <option value="{{ $patient->id }}" {{ isset($selectedPatient) && $selectedPatient->id == $patient->id ? 'selected' : '' }}>
                                {{ $patient->name }} (#{{ $patient->patient_code }})
                            </option>
                        @endforeach
                        @if(empty($patients))
                            <option value="1" selected>Mehdi Rais (#P001)</option>
                            <option value="2">Sara Tazi (#P002)</option>
                            <option value="3">Karim Alaoui (#P003)</option>
                        @endif
                    </select>
                </div>
                <div class="date-range">
                    <label for="date-from">Période:</label>
                    <input type="date" id="date-from" value="{{ $dateFrom ?? '2023-08-01' }}">
                    <span>à</span>
                    <input type="date" id="date-to" value="{{ $dateTo ?? '2023-08-31' }}">
                    <button id="apply-filter">Appliquer</button>
                </div>
                <div class="view-selector">
                    <button class="view-btn active" data-view="daily"><i class="fas fa-calendar-day"></i> Quotidien</button>
                    <button class="view-btn" data-view="weekly"><i class="fas fa-calendar-week"></i> Hebdomadaire</button>
                    <button class="view-btn" data-view="monthly"><i class="fas fa-calendar-alt"></i> Mensuel</button>
                </div>
            </div>

            <div class="charts-container">
                <div class="chart-card">
                    <div class="card-header">
                        <h3><i class="fas fa-heartbeat"></i> Fréquence Cardiaque</h3>
                        <div class="card-actions">
                            <button class="export-btn"><i class="fas fa-download"></i> Exporter</button>
                        </div>
                    </div>
                    <div class="chart-area">
                        <canvas id="heart-rate-chart"></canvas>
                    </div>
                    <div class="stats-summary">
                        <div class="stat">
                            <span class="stat-label">Moyenne</span>
                            <span class="stat-value">{{ $heartRateStats->avg ?? '78' }} bpm</span>
                        </div>
                        <div class="stat">
                            <span class="stat-label">Min</span>
                            <span class="stat-value">{{ $heartRateStats->min ?? '62' }} bpm</span>
                        </div>
                        <div class="stat">
                            <span class="stat-label">Max</span>
                            <span class="stat-value">{{ $heartRateStats->max ?? '95' }} bpm</span>
                        </div>
                    </div>
                </div>

                <div class="chart-card">
                    <div class="card-header">
                        <h3><i class="fas fa-lungs"></i> Oxygénation (SpO2)</h3>
                        <div class="card-actions">
                            <button class="export-btn"><i class="fas fa-download"></i> Exporter</button>
                        </div>
                    </div>
                    <div class="chart-area">
                        <canvas id="spo2-chart"></canvas>
                    </div>
                    <div class="stats-summary">
                        <div class="stat">
                            <span class="stat-label">Moyenne</span>
                            <span class="stat-value">{{ $spo2Stats->avg ?? '97' }}%</span>
                        </div>
                        <div class="stat">
                            <span class="stat-label">Min</span>
                            <span class="stat-value">{{ $spo2Stats->min ?? '94' }}%</span>
                        </div>
                        <div class="stat">
                            <span class="stat-label">Max</span>
                            <span class="stat-value">{{ $spo2Stats->max ?? '99' }}%</span>
                        </div>
                    </div>
                </div>

                <div class="chart-card">
                    <div class="card-header">
                        <h3><i class="fas fa-thermometer-half"></i> Température</h3>
                        <div class="card-actions">
                            <button class="export-btn"><i class="fas fa-download"></i> Exporter</button>
                        </div>
                    </div>
                    <div class="chart-area">
                        <canvas id="temperature-chart"></canvas>
                    </div>
                    <div class="stats-summary">
                        <div class="stat">
                            <span class="stat-label">Moyenne</span>
                            <span class="stat-value">{{ $temperatureStats->avg ?? '37.1' }}°C</span>
                        </div>
                        <div class="stat">
                            <span class="stat-label">Min</span>
                            <span class="stat-value">{{ $temperatureStats->min ?? '36.5' }}°C</span>
                        </div>
                        <div class="stat">
                            <span class="stat-label">Max</span>
                            <span class="stat-value">{{ $temperatureStats->max ?? '37.8' }}°C</span>
                        </div>
                    </div>
                </div>

                <div class="chart-card">
                    <div class="card-header">
                        <h3><i class="fas fa-tachometer-alt"></i> Tension Artérielle</h3>
                        <div class="card-actions">
                            <button class="export-btn"><i class="fas fa-download"></i> Exporter</button>
                        </div>
                    </div>
                    <div class="chart-area">
                        <canvas id="blood-pressure-chart"></canvas>
                    </div>
                    <div class="stats-summary">
                        <div class="stat">
                            <span class="stat-label">Moyenne</span>
                            <span class="stat-value">{{ $bpStats->avg ?? '125/80' }}</span>
                        </div>
                        <div class="stat">
                            <span class="stat-label">Min</span>
                            <span class="stat-value">{{ $bpStats->min ?? '115/75' }}</span>
                        </div>
                        <div class="stat">
                            <span class="stat-label">Max</span>
                            <span class="stat-value">{{ $bpStats->max ?? '145/90' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="data-table-section">
                <div class="section-header">
                    <h3>Données Brutes</h3>
                    <div class="section-actions">
                        <button class="export-btn"><i class="fas fa-file-csv"></i> Exporter CSV</button>
                    </div>
                </div>
                <div class="data-table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Heure</th>
                                <th>Fréq. Card.</th>
                                <th>SpO2</th>
                                <th>Température</th>
                                <th>Tension Art.</th>
                                <th>Commentaire</th>
                            </tr>
                        </thead>
                        <tbody id="data-table-body">
                            @if(isset($measurements) && count($measurements) > 0)
                                @foreach($measurements as $measure)
                                <tr>
                                    <td>{{ $measure->date }}</td>
                                    <td>{{ $measure->time }}</td>
                                    <td>{{ $measure->heart_rate }} bpm</td>
                                    <td>{{ $measure->spo2 }}%</td>
                                    <td>{{ $measure->temperature }}°C</td>
                                    <td>{{ $measure->blood_pressure }}</td>
                                    <td>{{ $measure->comment }}</td>
                                </tr>
                                @endforeach
                            @else
                                <!-- Data will be loaded dynamically via JavaScript -->
                            @endif
                        </tbody>
                    </table>
                </div>
                
                @if(isset($measurements) && $measurements instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    {{ $measurements->links('pagination.custun') }}
                @else
                <div class="pagination">
                    <button class="pagination-btn prev-page"><i class="fas fa-chevron-left"></i></button>
                    <span class="page-info">Page <span id="current-page">1</span> sur <span id="total-pages">5</span></span>
                    <button class="pagination-btn next-page"><i class="fas fa-chevron-right"></i></button>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Initial chart data that will be replaced by actual data
    const chartData = @json($chartData ?? []);
</script>
<script src="{{ asset('js/historique.js') }}"></script>
@endsection
