@extends('layouts.app')

@section('title', 'Assigner un Patient')

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h1><i class="fas fa-link"></i> Assigner un Patient au Capteur</h1>
        <div class="actions">
            <a href="{{ route('sensors.index') }}" class="btn outline-btn">
                <i class="fas fa-arrow-left"></i> Retour aux capteurs
            </a>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h2>Capteur: {{ $sensor->sensor_id }}</h2>
        </div>
        <div class="card-body">
            <div class="sensor-details mb-4">
                <div class="detail-row">
                    <div class="detail-label">Type:</div>
                    <div class="detail-value">{{ ucfirst($sensor->type) }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Modèle:</div>
                    <div class="detail-value">{{ $sensor->model }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Statut:</div>
                    <div class="detail-value">
                        @if($sensor->status == 'active')
                            <span class="status-badge active">Actif</span>
                        @elseif($sensor->status == 'inactive')
                            <span class="status-badge inactive">Inactif</span>
                        @elseif($sensor->status == 'error')
                            <span class="status-badge error">Erreur</span>
                        @else
                            <span class="status-badge ready">Prêt</span>
                        @endif
                    </div>
                </div>
            </div>
            
            @if($sensor->patient)
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Ce capteur est déjà assigné à <strong>{{ $sensor->patient->name }}</strong>.
                Assigner un nouveau patient remplacera cette association.
            </div>
            @endif
            
            <form action="{{ route('sensors.assign', $sensor->id) }}" method="POST" class="assign-patient-form">
                @csrf
                
                <div class="form-group">
                    <label for="patient_id">Sélectionner un patient:</label>
                    <select name="patient_id" id="patient_id" class="form-control" required>
                        <option value="">-- Choisir un patient --</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" @if($sensor->patient_id == $patient->id) selected @endif>
                                {{ $patient->name }} (ID: {{ $patient->id }})
                            </option>
                        @endforeach
                    </select>
                    @error('patient_id')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn primary-btn">
                        <i class="fas fa-check"></i> Assigner le patient
                    </button>
                    <a href="{{ route('sensors.index') }}" class="btn outline-btn">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .sensor-details {
        background: #f9f9f9;
        padding: 1.5rem;
        border-radius: 10px;
    }
    
    .detail-row {
        display: flex;
        margin-bottom: 0.75rem;
    }
    
    .detail-row:last-child {
        margin-bottom: 0;
    }
    
    .detail-label {
        font-weight: 600;
        width: 120px;
    }
    
    .assign-patient-form {
        max-width: 600px;
        margin-top: 2rem;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
    }
    
    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 1rem;
    }
    
    .form-error {
        color: #e74c3c;
        font-size: 0.875rem;
        margin-top: 0.5rem;
    }
    
    .form-actions {
        display: flex;
        gap: 1rem;
    }
    
    .alert {
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
    }
    
    .alert-info {
        background: rgba(52, 152, 219, 0.1);
        color: #3498db;
        border-left: 4px solid #3498db;
    }
    
    .mb-4 {
        margin-bottom: 1.5rem;
    }
    
    /* Status badges */
    .status-badge {
        display: inline-block;
        padding: 0.35rem 0.75rem;
        border-radius: 30px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .status-badge.active {
        background: rgba(46, 204, 113, 0.2);
        color: #2ecc71;
    }
    
    .status-badge.inactive {
        background: rgba(149, 165, 166, 0.2);
        color: #95a5a6;
    }
    
    .status-badge.error {
        background: rgba(231, 76, 60, 0.2);
        color: #e74c3c;
    }
    
    .status-badge.ready {
        background: rgba(52, 152, 219, 0.2);
        color: #3498db;
    }
</style>
@endsection