@extends('layouts.app')

@section('title', 'Rapports - Système de Monitoring Médical')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/reports.css') }}">
    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Librairies externes pour PDF et QR -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
@endsection

@section('content')
<div class="reports-container">
    <div class="reports-header">
        <div class="reports-title">
            <div class="icon">
                <i class="fas fa-file-chart-line"></i>
            </div>
            <h1><i class="fas fa-file-medical-alt"></i> Rapports Médicaux</h1>
        </div>
        
        <!-- Actions d'export et QR avec icônes améliorées -->
        <div class="export-actions">
            <button id="export-csv" class="btn-export">
                <i class="fas fa-file-csv"></i>
                <span>Exporter CSV</span>
            </button>
            <button id="export-pdf" class="btn-export">
                <i class="fas fa-file-pdf"></i>
                <span>Exporter PDF</span>
            </button>
            <button id="scan-qr" class="btn-qr">
                <i class="fas fa-qrcode"></i>
                <span>Scanner QR</span>
            </button>
            <button id="generate-qr" class="btn-qr">
                <i class="fas fa-qr-code"></i>
                <span>Générer QR</span>
            </button>
        </div>
        
        <!-- Filtres existants avec icônes -->
        <form action="{{ route('rapports.index') }}" method="GET" class="filters-form">
            <div class="filters-section">
                <div class="filter-group">
                    <label for="patient_id">
                        <i class="fas fa-user"></i>
                        Patient
                    </label>
                    <select name="patient_id" id="patient_id" class="filter-input">
                        <option value="">Tous les patients</option>
                        @foreach($patients ?? [] as $patient)
                            <option value="{{ $patient->id }}" {{ request('patient_id') == $patient->id ? 'selected' : '' }}>
                                {{ $patient->nom_complet }}
                            </option>
                        @endforeach
                        @if(empty($patients))
                            <option value="1" {{ request('patient_id') == 1 ? 'selected' : '' }}>
                                <i class="fas fa-user"></i> Jean Dupont (#P001)
                            </option>
                            <option value="2" {{ request('patient_id') == 2 ? 'selected' : '' }}>
                                <i class="fas fa-user"></i> Marie Martin (#P002)
                            </option>
                            <option value="3" {{ request('patient_id') == 3 ? 'selected' : '' }}>
                                <i class="fas fa-user"></i> Ahmed Alaoui (#P003)
                            </option>
                        @endif
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="report_type">
                        <i class="fas fa-clipboard-list"></i>
                        Type de rapport
                    </label>
                    <select name="report_type" id="report_type" class="filter-input">
                        <option value="">Tous les types</option>
                        <option value="medical" {{ request('report_type') == 'medical' ? 'selected' : '' }}>
                            <i class="fas fa-user-md"></i> Médical
                        </option>
                        <option value="vitals" {{ request('report_type') == 'vitals' ? 'selected' : '' }}>
                            <i class="fas fa-heartbeat"></i> Signes vitaux
                        </option>
                        <option value="laboratory" {{ request('report_type') == 'laboratory' ? 'selected' : '' }}>
                            <i class="fas fa-flask"></i> Laboratoire
                        </option>
                        <option value="prescriptions" {{ request('report_type') == 'prescriptions' ? 'selected' : '' }}>
                            <i class="fas fa-prescription"></i> Prescriptions
                        </option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="date_from">
                        <i class="fas fa-calendar-alt"></i>
                        Date de début
                    </label>
                    <input type="date" name="date_from" id="date_from" class="filter-input" 
                           value="{{ request('date_from', Carbon\Carbon::now()->subMonth()->toDateString()) }}">
                </div>
                
                <div class="filter-group">
                    <label for="date_to">
                        <i class="fas fa-calendar-check"></i>
                        Date de fin
                    </label>
                    <input type="date" name="date_to" id="date_to" class="filter-input" 
                           value="{{ request('date_to', Carbon\Carbon::now()->toDateString()) }}">
                </div>
                
                <div class="filter-actions">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-search"></i> Rechercher
                    </button>
                    <a href="{{ route('reports.generate') }}" class="btn-secondary">
                        <i class="fas fa-plus"></i> Nouveau
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Grid de rapports avec icônes améliorées -->
    <div class="reports-grid">
        <!-- Rapport Médical -->
        <div class="report-card medical">
            <div class="report-header">
                <div class="report-title">
                    <div class="report-icon">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <span>Rapports Médicaux</span>
                </div>
            </div>
            <div class="report-stats">
                <div class="stat-item">
                    <div class="stat-value">{{ $stats['medical']['total'] ?? '24' }}</div>
                    <div class="stat-label">Total</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $stats['medical']['this_month'] ?? '8' }}</div>
                    <div class="stat-label">Ce mois</div>
                </div>
            </div>
            <div class="report-actions">
                <a href="{{ route('reports.medical') }}" class="btn-primary action-btn-sm">
                    <i class="fas fa-eye"></i> Voir tout
                </a>
                <a href="{{ route('reports.generate', ['type' => 'medical']) }}" class="btn-secondary action-btn-sm">
                    <i class="fas fa-plus"></i> Créer
                </a>
            </div>
        </div>

        <!-- Signes Vitaux -->
        <div class="report-card vitals">
            <div class="report-header">
                <div class="report-title">
                    <div class="report-icon">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <span>Signes Vitaux</span>
                </div>
            </div>
            <div class="report-stats">
                <div class="stat-item">
                    <div class="stat-value">{{ $stats['vitals']['total'] ?? '156' }}</div>
                    <div class="stat-label">Total</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $stats['vitals']['this_month'] ?? '42' }}</div>
                    <div class="stat-label">Ce mois</div>
                </div>
            </div>
            <div class="report-actions">
                <a href="{{ route('reports.vitals') }}" class="btn-primary action-btn-sm">
                    <i class="fas fa-eye"></i> Voir tout
                </a>
                <a href="{{ route('reports.generate', ['type' => 'vitals']) }}" class="btn-secondary action-btn-sm">
                    <i class="fas fa-plus"></i> Créer
                </a>
            </div>
        </div>

        <!-- Laboratoire -->
        <div class="report-card laboratory">
            <div class="report-header">
                <div class="report-title">
                    <div class="report-icon">
                        <i class="fas fa-flask"></i>
                    </div>
                    <span>Laboratoire</span>
                </div>
            </div>
            <div class="report-stats">
                <div class="stat-item">
                    <div class="stat-value">{{ $stats['laboratory']['total'] ?? '89' }}</div>
                    <div class="stat-label">Total</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $stats['laboratory']['this_month'] ?? '18' }}</div>
                    <div class="stat-label">Ce mois</div>
                </div>
            </div>
            <div class="report-actions">
                <a href="{{ route('reports.laboratory') }}" class="btn-primary action-btn-sm">
                    <i class="fas fa-eye"></i> Voir tout
                </a>
                <a href="{{ route('reports.generate', ['type' => 'laboratory']) }}" class="btn-secondary action-btn-sm">
                    <i class="fas fa-plus"></i> Créer
                </a>
            </div>
        </div>

        <!-- Prescriptions -->
        <div class="report-card prescriptions">
            <div class="report-header">
                <div class="report-title">
                    <div class="report-icon">
                        <i class="fas fa-prescription"></i>
                    </div>
                    <span>Prescriptions</span>
                </div>
            </div>
            <div class="report-stats">
                <div class="stat-item">
                    <div class="stat-value">{{ $stats['prescriptions']['total'] ?? '67' }}</div>
                    <div class="stat-label">Total</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $stats['prescriptions']['this_month'] ?? '15' }}</div>
                    <div class="stat-label">Ce mois</div>
                </div>
            </div>
            <div class="report-actions">
                <a href="{{ route('reports.prescriptions') }}" class="btn-primary action-btn-sm">
                    <i class="fas fa-eye"></i> Voir tout
                </a>
                <a href="{{ route('reports.generate', ['type' => 'prescriptions']) }}" class="btn-secondary action-btn-sm">
                    <i class="fas fa-plus"></i> Créer
                </a>
            </div>
        </div>
    </div>

    <!-- Section des rapports récents avec table améliorée -->
    <div class="recent-reports-section">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-clock"></i> Rapports Récents
            </h2>
        </div>
        
        <div class="table-responsive">
            <table class="reports-table">
                <thead>
                    <tr>
                        <th><i class="fas fa-calendar"></i> Date/Heure</th>
                        <th><i class="fas fa-user"></i> Patient</th>
                        <th><i class="fas fa-tag"></i> Type</th>
                        <th><i class="fas fa-file-alt"></i> Titre</th>
                        <th><i class="fas fa-info-circle"></i> Statut</th>
                        <th><i class="fas fa-user-md"></i> Créé par</th>
                        <th><i class="fas fa-cog"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports ?? [] as $report)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($report->created_at ?? now())->format('d/m/Y H:i') }}</td>
                            <td>{{ $report->patient->nom_complet ?? 'Patient Inconnu' }}</td>
                            <td>
                                <span class="type-badge type-{{ $report->type ?? 'medical' }}">
                                    {{ ucfirst($report->type ?? 'Médical') }}
                                </span>
                            </td>
                            <td>{{ $report->title ?? 'Rapport médical' }}</td>
                            <td>
                                <span class="status-badge status-{{ $report->status ?? 'completed' }}">
                                    @if(($report->status ?? 'completed') == 'completed')
                                        <i class="fas fa-check-circle"></i>
                                    @elseif(($report->status ?? 'completed') == 'pending')
                                        <i class="fas fa-clock"></i>
                                    @else
                                        <i class="fas fa-spinner"></i>
                                    @endif
                                    {{ $report->status_label ?? 'Complété' }}
                                </span>
                            </td>
                            <td>{{ $report->created_by_name ?? 'Dr. Ahmed' }}</td>
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
                        <!-- Données d'exemple avec icônes -->
                        <tr>
                            <td>{{ now()->format('d/m/Y H:i') }}</td>
                            <td><i class="fas fa-user"></i> Jean Dupont</td>
                            <td><span class="type-badge type-medical">Médical</span></td>
                            <td>Consultation cardiologique</td>
                            <td><span class="status-badge status-completed"><i class="fas fa-check-circle"></i> Complété</span></td>
                            <td><i class="fas fa-user-md"></i> Dr. Ahmed Benali</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-secondary action-btn-sm"><i class="fas fa-eye"></i></button>
                                    <button class="btn-secondary action-btn-sm"><i class="fas fa-download"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>{{ now()->subHours(2)->format('d/m/Y H:i') }}</td>
                            <td><i class="fas fa-user"></i> Marie Martin</td>
                            <td><span class="type-badge type-vitals">Signes vitaux</span></td>
                            <td>Monitoring quotidien</td>
                            <td><span class="status-badge status-processing"><i class="fas fa-spinner"></i> En cours</span></td>
                            <td><i class="fas fa-user-md"></i> Dr. Sophie Leroy</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-secondary action-btn-sm"><i class="fas fa-eye"></i></button>
                                    <button class="btn-secondary action-btn-sm"><i class="fas fa-download"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>{{ now()->subHours(4)->format('d/m/Y H:i') }}</td>
                            <td><i class="fas fa-user"></i> Ahmed Alaoui</td>
                            <td><span class="type-badge type-laboratory">Laboratoire</span></td>
                            <td>Analyses sanguines</td>
                            <td><span class="status-badge status-pending"><i class="fas fa-clock"></i> En attente</span></td>
                            <td><i class="fas fa-user-md"></i> Dr. Ahmed Benali</td>
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

<!-- Modal Scanner QR avec design amélioré -->
<div id="qr-scan-modal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="fas fa-qrcode"></i>
                Scanner un QR Code
            </h3>
            <button class="close-modal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="scanner-container">
            <video id="qr-video" class="scanner-video" autoplay playsinline style="display: none;"></video>
            
            <div id="scanner-placeholder" class="scanner-placeholder">
                <i class="fas fa-camera"></i>
                <p>Activation de la caméra...</p>
            </div>
            
            <div class="scanner-overlay">
                <div class="scanner-corners">
                    <div class="scanner-corner top-left"></div>
                    <div class="scanner-corner top-right"></div>
                    <div class="scanner-corner bottom-left"></div>
                    <div class="scanner-corner bottom-right"></div>
                </div>
                <div class="scanning-line" style="display: none;"></div>
            </div>
        </div>
        
        <div style="text-align: center; margin-top: 20px;">
            <p><i class="fas fa-info-circle"></i> Positionnez le QR code dans le cadre pour le scanner</p>
            <button class="btn-secondary" onclick="closeQRScanner()">
                <i class="fas fa-times"></i> Annuler
            </button>
        </div>
    </div>
</div>

<!-- Modal Générateur QR avec design amélioré -->
<div id="qr-generate-modal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="fas fa-qr-code"></i>
                Générer un QR Code
            </h3>
            <button class="close-modal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="qr-container">
            <div class="qr-display">
                <canvas id="qr-canvas"></canvas>
            </div>
            
            <div class="qr-info">
                <h3><i class="fas fa-user"></i> <span id="qr-patient-name">Patient</span></h3>
                <p><i class="fas fa-id-card"></i> <span id="qr-patient-id">ID: </span></p>
                <p><i class="fas fa-tag"></i> <span id="qr-report-type">Type: </span></p>
                <p><i class="fas fa-calendar"></i> <span id="qr-report-date">Date: </span></p>
            </div>
            
            <div style="margin-top: 20px;">
                <button class="btn-export" onclick="downloadQR()">
                    <i class="fas fa-download"></i>
                    Télécharger
                </button>
                <button class="btn-export" onclick="printQR()">
                    <i class="fas fa-print"></i>
                    Imprimer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Conteneur pour les notifications toast -->
<div class="toast-container"></div>
@endsection

@section('scripts')
<script src="{{ asset('js/reports-enhanced.js') }}"></script>
<script>
    // Script d'initialisation spécifique à la page
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Page de rapports chargée avec fonctionnalités avancées et design moderne');
        
        // Animation d'entrée pour les éléments
        const cards = document.querySelectorAll('.report-card');
        cards.forEach((card, index) => {
            setTimeout(() => {
                card.style.animation = `slideInUp 0.6s ease-out ${index * 0.1}s both`;
            }, 100);
        });
    });
</script>
@endsection