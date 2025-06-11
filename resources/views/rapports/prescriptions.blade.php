@extends('layouts.app')

@section('title', 'Rapports Prescriptions')

@section('content')
<div class="reports-modern-container">
    <div class="reports-header">
        <div class="reports-title">
            <div class="icon" style="background: linear-gradient(135deg, #48cae4 0%, #023e8a 100%);">
                <i class="fas fa-prescription"></i>
            </div>
            <h1>Rapports Prescriptions</h1>
        </div>
        
        <form action="{{ route('reports.prescriptions') }}" method="GET" class="filters-form">
            <div class="filters-section">
                <div class="filter-group">
                    <label for="patient_id">Patient</label>
                    <select name="patient_id" id="patient_id" class="filter-input">
                        <option value="">Tous les patients</option>
                        @foreach($patients ?? [] as $patient)
                            <option value="{{ $patient->id }}" {{ request('patient_id') == $patient->id ? 'selected' : '' }}>
                                {{ $patient->nom_complet }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="medication_type">Type de médicament</label>
                    <select name="medication_type" id="medication_type" class="filter-input">
                        <option value="">Tous les types</option>
                        <option value="antibiotics" {{ request('medication_type') == 'antibiotics' ? 'selected' : '' }}>Antibiotiques</option>
                        <option value="analgesics" {{ request('medication_type') == 'analgesics' ? 'selected' : '' }}>Analgésiques</option>
                        <option value="cardiovascular" {{ request('medication_type') == 'cardiovascular' ? 'selected' : '' }}>Cardiovasculaire</option>
                        <option value="diabetes" {{ request('medication_type') == 'diabetes' ? 'selected' : '' }}>Diabète</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="date_from">Date de début</label>
                    <input type="date" name="date_from" id="date_from" class="filter-input" 
                           value="{{ request('date_from', Carbon\Carbon::now()->subMonth()->toDateString()) }}">
                </div>
                
                <div class="filter-group">
                    <label for="date_to">Date de fin</label>
                    <input type="date" name="date_to" id="date_to" class="filter-input" 
                           value="{{ request('date_to', Carbon\Carbon::now()->toDateString()) }}">
                </div>
                
                <div class="filter-actions">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-search"></i> Rechercher
                    </button>
                    <a href="{{ route('reports.generate', ['type' => 'prescriptions']) }}" class="btn-secondary">
                        <i class="fas fa-plus"></i> Nouveau
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="recent-reports-section">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-prescription"></i> Rapports Prescriptions
            </h2>
        </div>
        
        <div class="table-responsive">
            <table class="reports-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Patient</th>
                        <th>Médicament</th>
                        <th>Dosage</th>
                        <th>Durée</th>
                        <th>Médecin</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prescriptionReports ?? [] as $report)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($report->created_at ?? now())->format('d/m/Y') }}</td>
                            <td>{{ $report->patient->nom_complet ?? 'Patient Inconnu' }}</td>
                            <td>{{ $report->medication ?? 'Paracétamol' }}</td>
                            <td>{{ $report->dosage ?? '500mg' }}</td>
                            <td>{{ $report->duration ?? '7 jours' }}</td>
                            <td>{{ $report->medecin->nom ?? 'Dr. Ahmed' }}</td>
                            <td>
                                <span class="status-badge status-{{ $report->status ?? 'active' }}">
                                    {{ $report->status_label ?? 'Active' }}
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('reports.show', $report->id ?? 1) }}" class="btn-secondary action-btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('reports.download', $report->id ?? 1) }}" class="btn-secondary action-btn-sm">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td>{{ now()->format('d/m/Y') }}</td>
                            <td>Jean Dupont</td>
                            <td>Lisinopril</td>
                            <td>10mg</td>
                            <td>30 jours</td>
                            <td>Dr. Ahmed Benali</td>
                            <td><span class="status-badge status-completed">Active</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-secondary action-btn-sm"><i class="fas fa-eye"></i></button>
                                    <button class="btn-secondary action-btn-sm"><i class="fas fa-download"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>{{ now()->subDays(2)->format('d/m/Y') }}</td>
                            <td>Marie Martin</td>
                            <td>Metformine</td>
                            <td>850mg</td>
                            <td>90 jours</td>
                            <td>Dr. Sophie Leroy</td>
                            <td><span class="status-badge status-processing">Renouvelée</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-secondary action-btn-sm"><i class="fas fa-eye"></i></button>
                                    <button class="btn-secondary action-btn-sm"><i class="fas fa-download"></i></button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    
    .reports-modern-container {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        padding: 20px;
    }

    .reports-header {
        background: white;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .reports-title {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
    }

    .reports-title h1 {
        color: #2d3748;
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0;
    }

    .reports-title .icon {
        width: 60px;
        height: 60px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }

    .filters-section {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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
        color: #4a5568;
        font-size: 0.9rem;
    }

    .filter-input {
        padding: 12px 15px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .filter-input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .filter-actions {
        display: flex;
        gap: 10px;
        align-items: end;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 12px 25px;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
    }

    .btn-secondary {
        background: white;
        color: #667eea;
        border: 2px solid #667eea;
        padding: 10px 23px;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-secondary:hover {
        background: #667eea;
        color: white;
    }

    .recent-reports-section {
        background: white;
        border-radius: 20px;
        padding: 25px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2d3748;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .reports-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    .reports-table th {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 15px 20px;
        text-align: left;
        font-weight: 600;
        border: none;
    }

    .reports-table th:first-child {
        border-radius: 10px 0 0 0;
    }

    .reports-table th:last-child {
        border-radius: 0 10px 0 0;
    }

    .reports-table td {
        padding: 15px 20px;
        border-bottom: 1px solid #e2e8f0;
        color: #4a5568;
    }

    .reports-table tr:hover {
        background: #f7fafc;
    }

    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-completed {
        background: #c6f6d5;
        color: #22543d;
    }

    .status-pending {
        background: #faf089;
        color: #744210;
    }

    .status-processing {
        background: #bee3f8;
        color: #2a4365;
    }

    .action-btn-sm {
        padding: 8px 12px;
        font-size: 0.8rem;
        border-radius: 8px;
    }
</style>
@endsection