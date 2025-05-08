@extends('layouts.app')

@section('title', 'Détails Historique | Système de Surveillance Médicale')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/history.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .timeline {
        position: relative;
        margin: 20px 0;
        padding: 0;
    }
    
    .timeline:before {
        content: '';
        position: absolute;
        top: 0;
        left: 15px;
        height: 100%;
        width: 2px;
        background: #e0e0e0;
    }
    
    .timeline-item {
        position: relative;
        margin-bottom: 30px;
        padding-left: 40px;
    }
    
    .timeline-badge {
        position: absolute;
        top: 0;
        left: 0;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #1e40af;
        color: white;
        text-align: center;
        line-height: 30px;
        z-index: 1;
    }
    
    .timeline-badge.alert {
        background: #ef4444;
    }
    
    .timeline-panel {
        background: white;
        border-radius: 10px;
        padding: 15px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }
    
    .reading-cards {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
    }
    
    .reading-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
    }
    
    .reading-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        font-size: 24px;
        color: white;
    }
    
    .reading-icon.heart-rate {
        background: linear-gradient(135deg, #ef4444, #f87171);
    }
    
    .reading-icon.spo2 {
        background: linear-gradient(135deg, #3b82f6, #60a5fa);
    }
    
    .reading-icon.temperature {
        background: linear-gradient(135deg, #f59e0b, #fbbf24);
    }
    
    .reading-icon.blood-pressure {
        background: linear-gradient(135deg, #10b981, #34d399);
    }
    
    .reading-info .value {
        font-size: 24px;
        font-weight: 600;
    }
    
    .reading-info .label {
        color: #6b7280;
        font-size: 14px;
    }
    
    .patient-header {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .patient-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: #e5e7eb;
        overflow: hidden;
        margin-right: 15px;
        flex-shrink: 0;
    }
    
    .patient-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .pagination {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }
    
    .date-navigation {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
    }
    
    .date-nav-btn {
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 8px 16px;
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .date-nav-btn:hover {
        background: #f3f4f6;
    }
    
    .current-date {
        font-size: 20px;
        font-weight: 600;
    }
</style>
@endsection

@section('content')
<div class="app-container">
    @include('patials.sidebar')

    <div class="main-content">
        <div class="main-header">
            <h1>Détails de l'Historique</h1>
            <div class="user-info">
                <span>{{ $doctor->name ?? 'Dr. Ahmed' }}</span>
                <img src="{{ $doctor->avatar ?? 'https://randomuser.me/api/portraits/men/4.jpg' }}" alt="User" class="avatar">
            </div>
        </div>

        <div class="content-wrapper">
            <div class="actions-bar">
                <a href="{{ route('history.index') }}" class="back-btn">
                    <i class="fas fa-arrow-left"></i> Retour à l'historique
                </a>
                <div class="action-buttons">
                    <button class="action-btn" onclick="window.print()">
                        <i class="fas fa-print"></i> Imprimer
                    </button>
                    <a href="{{ route('history.export', ['date' => $date, 'patient_id' => $patient->id ?? '']) }}" class="action-btn">
                        <i class="fas fa-file-export"></i> Exporter
                    </a>
                </div>
            </div>

            @if($patient)
            <div class="patient-header">
                <div class="patient-avatar">
                    <img src="{{ $patient->avatar ?? 'https://randomuser.me/api/portraits/men/1.jpg' }}" alt="Patient">
                </div>
                <div class="patient-info">
                    <h2>{{ $patient->name }}</h2>
                    <div class="patient-meta">
                        <span><i class="fas fa-id-card"></i> ID: {{ $patient->id }}</span>
                        <span><i class="fas fa-calendar"></i> Âge: {{ $patient->age ?? '45' }} ans</span>
                    </div>
                </div>
            </div>
            @endif

            <div class="date-navigation">
                <a href="{{ route('history.details', ['patient' => $patient->id ?? '', 'date' => Carbon\Carbon::parse($date)->subDay()->toDateString()]) }}" class="date-nav-btn">
                    <i class="fas fa-chevron-left"></i> Jour précédent
                </a>
                <div class="current-date">
                    {{ Carbon\Carbon::parse($date)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                </div>
                <a href="{{ route('history.details', ['patient' => $patient->id ?? '', 'date' => Carbon\Carbon::parse($date)->addDay()->toDateString()]) }}" class="date-nav-btn">
                    Jour suivant <i class="fas fa-chevron-right"></i>
                </a>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Résumé des constantes vitales</h3>
                </div>
                <div class="card-body">
                    <div class="reading-cards">
                        <div class="reading-card">
                            <div class="reading-icon heart-rate">
                                <i class="fas fa-heartbeat"></i>
                            </div>
                            <div class="reading-info">
                                <div class="value">78 bpm</div>
                                <div class="label">Fréquence cardiaque moyenne</div>
                            </div>
                        </div>
                        
                        <div class="reading-card">
                            <div class="reading-icon spo2">
                                <i class="fas fa-lungs"></i>
                            </div>
                            <div class="reading-info">
                                <div class="value">97 %</div>
                                <div class="label">Oxygénation moyenne (SpO2)</div>
                            </div>
                        </div>
                        
                        <div class="reading-card">
                            <div class="reading-icon temperature">
                                <i class="fas fa-thermometer-half"></i>
                            </div>
                            <div class="reading-info">
                                <div class="value">37.1 °C</div>
                                <div class="label">Température moyenne</div>
                            </div>
                        </div>
                        
                        <div class="reading-card">
                            <div class="reading-icon blood-pressure">
                                <i class="fas fa-tachometer-alt"></i>
                            </div>
                            <div class="reading-info">
                                <div class="value">125/80</div>
                                <div class="label">Tension artérielle moyenne</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Chronologie de la journée</h3>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @if(count($readings) > 0 || count($alerts) > 0)
                            <!-- Combiner lectures et alertes et trier par timestamp -->
                            @php
                                $timeline = collect();
                                
                                foreach($readings as $reading) {
                                    $timeline->push([
                                        'time' => $reading->timestamp,
                                        'type' => 'reading',
                                        'data' => $reading
                                    ]);
                                }
                                
                                foreach($alerts as $alert) {
                                    $timeline->push([
                                        'time' => $alert->created_at,
                                        'type' => 'alert',
                                        'data' => $alert
                                    ]);
                                }
                                
                                $timeline = $timeline->sortBy('time');
                            @endphp
                            
                            @foreach($timeline as $item)
                                <div class="timeline-item">
                                    @if($item['type'] == 'reading')
                                        <div class="timeline-badge">
                                            <i class="fas fa-chart-line"></i>
                                        </div>
                                        <div class="timeline-panel">
                                            <div class="timeline-heading">
                                                <h4>Lecture de capteur</h4>
                                                <p>
                                                    <small class="text-muted">
                                                        <i class="fas fa-clock"></i> {{ Carbon\Carbon::parse($item['data']->timestamp)->format('H:i') }}
                                                    </small>
                                                </p>
                                            </div>
                                            <div class="timeline-body">
                                                <p>{{ $item['data']->sensor->type ?? 'Capteur' }}: {{ $item['data']->value }}</p>
                                            </div>
                                        </div>
                                    @else
                                        <div class="timeline-badge alert">
                                            <i class="fas fa-exclamation-triangle"></i>
                                        </div>
                                        <div class="timeline-panel">
                                            <div class="timeline-heading">
                                                <h4>Alerte: {{ $item['data']->title }}</h4>
                                                <p>
                                                    <small class="text-muted">
                                                        <i class="fas fa-clock"></i> {{ Carbon\Carbon::parse($item['data']->created_at)->format('H:i') }}
                                                    </small>
                                                </p>
                                                <span class="severity-badge {{ strtolower($item['data']->severity) }}">
                                                    {{ ucfirst($item['data']->severity) }}
                                                </span>
                                            </div>
                                            <div class="timeline-body">
                                                <p>{{ $item['data']->message }}</p>
                                            </div>
                                            @if($item['data']->resolved)
                                                <div class="timeline-footer resolved">
                                                    <i class="fas fa-check-circle"></i> Résolue à {{ Carbon\Carbon::parse($item['data']->resolved_at)->format('H:i') }}
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state">
                                <i class="fas fa-calendar-day"></i>
                                <p>Aucune donnée disponible pour cette journée.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Page de détails historique chargée');
    });
</script>
@endsection