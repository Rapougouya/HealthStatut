@extends('layouts.app')

@section('title', 'Gestion des Capteurs')

@section('content')
<div class="content-wrapper">
    <!-- En-tête de la page -->
    <div class="page-header">
        <h1><i class="fas fa-microchip"></i> Gestion des Capteurs</h1>
        <div class="actions">
            <a href="{{ route('sensors.create') }}" class="btn primary-btn">
                <i class="fas fa-plus-circle"></i> Nouveau Capteur
            </a>
        </div>
    </div>

    <!-- Statistiques des capteurs -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon sensor-active">
                <i class="fas fa-wifi"></i>
            </div>
            <div class="stat-content">
                <h3>Capteurs Actifs</h3>
                <p class="stat-value">{{ $capteurs->where('status', 'active')->count() }}</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon sensor-warning">
                <i class="fas fa-battery-quarter"></i>
            </div>
            <div class="stat-content">
                <h3>Batterie Faible</h3>
                <p class="stat-value">{{ $lowBatteryCount ?? 0 }}</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon sensor-inactive">
                <i class="fas fa-power-off"></i>
            </div>
            <div class="stat-content">
                <h3>Capteurs Inactifs</h3>
                <p class="stat-value">{{ $capteurs->where('status', 'inactive')->count() }}</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon sensor-error">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-content">
                <h3>Capteurs en Erreur</h3>
                <p class="stat-value">{{ $capteurs->where('status', 'error')->count() }}</p>
            </div>
        </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="filters-section">
        <form action="{{ route('sensors.index') }}" method="GET" class="filter-form">
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Rechercher un capteur..." value="{{ request('search') }}">
            </div>
            <div class="filters">
                <select name="type" class="filter-select">
                    <option value="">Type de capteur</option>
                    <option value="temperature" {{ request('type') == 'temperature' ? 'selected' : '' }}>Température</option>
                    <option value="pulse" {{ request('type') == 'pulse' ? 'selected' : '' }}>Rythme cardiaque</option>
                    <option value="oxygen" {{ request('type') == 'oxygen' ? 'selected' : '' }}>Oxygène</option>
                    <option value="blood_pressure" {{ request('type') == 'blood_pressure' ? 'selected' : '' }}>Tension artérielle</option>
                    <option value="glucose" {{ request('type') == 'glucose' ? 'selected' : '' }}>Glucose</option>
                </select>
                <select name="status" class="filter-select">
                    <option value="">État</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actifs</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactifs</option>
                    <option value="error" {{ request('status') == 'error' ? 'selected' : '' }}>En erreur</option>
                    <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>Prêts</option>
                </select>
                <button type="submit" class="btn filter-btn">
                    <i class="fas fa-filter"></i> Filtrer
                </button>
                <a href="{{ route('sensors.index') }}" class="btn outline-btn">
                    <i class="fas fa-times"></i> Réinitialiser
                </a>
            </div>
        </form>
    </div>

    <!-- Liste des capteurs -->
    <div class="card sensors-card">
        <div class="card-header">
            <h2><i class="fas fa-list"></i> Liste des Capteurs</h2>
            <div class="card-tools">
                <button class="btn-icon toggle-view" data-view="grid">
                    <i class="fas fa-th-large"></i>
                </button>
                <button class="btn-icon toggle-view active" data-view="list">
                    <i class="fas fa-list"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table sensors-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Type</th>
                            <th>Modèle</th>
                            <th>Patient</th>
                            <th>Statut</th>
                            <th>Batterie</th>
                            <th>Dernière lecture</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($capteurs as $sensor)
                            <tr>
                                <td>{{ $sensor->sensor_id }}</td>
                                <td>
                                    <div class="sensor-type">
                                        @if($sensor->type == 'temperature')
                                            <span class="sensor-icon temperature">
                                                <i class="fas fa-thermometer-half"></i>
                                            </span>
                                            <span>Température</span>
                                        @elseif($sensor->type == 'pulse')
                                            <span class="sensor-icon pulse">
                                                <i class="fas fa-heartbeat"></i>
                                            </span>
                                            <span>Rythme cardiaque</span>
                                        @elseif($sensor->type == 'oxygen')
                                            <span class="sensor-icon oxygen">
                                                <i class="fas fa-lungs"></i>
                                            </span>
                                            <span>Oxygène</span>
                                        @elseif($sensor->type == 'blood_pressure')
                                            <span class="sensor-icon blood-pressure">
                                                <i class="fas fa-stethoscope"></i>
                                            </span>
                                            <span>Tension artérielle</span>
                                        @elseif($sensor->type == 'glucose')
                                            <span class="sensor-icon glucose">
                                                <i class="fas fa-tint"></i>
                                            </span>
                                            <span>Glucose</span>
                                        @else
                                            <span class="sensor-icon">
                                                <i class="fas fa-microchip"></i>
                                            </span>
                                            <span>{{ ucfirst($sensor->type) }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $sensor->model }}</td>
                                <td>
                                    @if($sensor->patient)
                                        <div class="patient-info">
                                            <div class="avatar">
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($sensor->patient->name) }}&background=random" alt="{{ $sensor->patient->name }}">
                                            </div>
                                            <span>{{ $sensor->patient->name }}</span>
                                        </div>
                                    @else
                                        <form action="{{ route('sensors.assign', $sensor->id) }}" method="POST" class="assign-form">
                                            @csrf
                                            <button type="submit" class="btn mini-btn">
                                                <i class="fas fa-user-plus"></i> Assigner
                                            </button>
                                        </form>
                                    @endif
                                </td>
                                <td>
                                    @if($sensor->status == 'active')
                                        <span class="status-badge active">Actif</span>
                                    @elseif($sensor->status == 'inactive')
                                        <span class="status-badge inactive">Inactif</span>
                                    @elseif($sensor->status == 'error')
                                        <span class="status-badge error">Erreur</span>
                                    @else
                                        <span class="status-badge ready">Prêt</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="battery-indicator">
                                        @php
                                            $batteryLevel = rand(10, 100); // Simulation - à remplacer par la vraie valeur
                                            $batteryClass = $batteryLevel > 70 ? 'high' : ($batteryLevel > 30 ? 'medium' : 'low');
                                        @endphp
                                        <div class="battery-bar {{ $batteryClass }}" style="width: {{ $batteryLevel }}%"></div>
                                        <span class="battery-text">{{ $batteryLevel }}%</span>
                                    </div>
                                </td>
                                <td>
                                    @if($sensor->last_reading_at)
                                        <div class="last-reading">
                                            <div class="reading-value">{{ $sensor->last_reading }}</div>
                                            <div class="reading-time">{{ $sensor->last_reading_at->diffForHumans() }}</div>
                                        </div>
                                    @else
                                        <span class="no-data">Aucune donnée</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="actions">
                                        <a href="{{ route('sensors.show', $sensor->id) }}" class="btn-icon" title="Voir les détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('sensors.edit', $sensor->id) }}" class="btn-icon" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('sensors.configure', $sensor->id) }}" class="btn-icon configure" title="Configurer">
                                            <i class="fas fa-cog"></i>
                                        </a>
                                        <a href="{{ route('sensors.history', $sensor->id) }}" class="btn-icon history" title="Historique">
                                            <i class="fas fa-history"></i>
                                        </a>
                                        <form action="{{ route('sensors.destroy', $sensor->id) }}" method="POST" class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-icon delete" title="Supprimer">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="no-results">
                                    <div class="empty-state">
                                        <div class="empty-icon">
                                            <i class="fas fa-sensor"></i>
                                        </div>
                                        <h3>Aucun capteur trouvé</h3>
                                        <p>Aucun capteur ne correspond à vos critères de recherche ou aucun capteur n'a été ajouté au système.</p>
                                        <a href="{{ route('sensors.create') }}" class="btn primary-btn">
                                            <i class="fas fa-plus-circle"></i> Ajouter un capteur
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Vue en grille (masquée par défaut) -->
            <div class="sensors-grid-view" style="display: none;">
                @forelse($capteurs as $sensor)
                    <div class="sensor-card">
                        <div class="sensor-card-header">
                            <div class="sensor-type">
                                @if($sensor->type == 'temperature')
                                    <span class="sensor-icon temperature">
                                        <i class="fas fa-thermometer-half"></i>
                                    </span>
                                @elseif($sensor->type == 'pulse')
                                    <span class="sensor-icon pulse">
                                        <i class="fas fa-heartbeat"></i>
                                    </span>
                                @elseif($sensor->type == 'oxygen')
                                    <span class="sensor-icon oxygen">
                                        <i class="fas fa-lungs"></i>
                                    </span>
                                @elseif($sensor->type == 'blood_pressure')
                                    <span class="sensor-icon blood-pressure">
                                        <i class="fas fa-stethoscope"></i>
                                    </span>
                                @elseif($sensor->type == 'glucose')
                                    <span class="sensor-icon glucose">
                                        <i class="fas fa-tint"></i>
                                    </span>
                                @else
                                    <span class="sensor-icon">
                                        <i class="fas fa-microchip"></i>
                                    </span>
                                @endif
                                <span>{{ $sensor->sensor_id }}</span>
                            </div>
                            <div class="status">
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
                        <div class="sensor-card-body">
                            <h3>{{ $sensor->model }}</h3>
                            <p class="model">{{ ucfirst($sensor->type) }}</p>
                            
                            @if($sensor->patient)
                                <div class="patient-info">
                                    <div class="avatar">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($sensor->patient->name) }}&background=random" alt="{{ $sensor->patient->name }}">
                                    </div>
                                    <span>{{ $sensor->patient->name }}</span>
                                </div>
                            @else
                                <div class="no-patient">Non assigné</div>
                            @endif
                            
                            <div class="sensor-stats">
                                <div class="stat-item">
                                    <div class="stat-label">Batterie</div>
                                    <div class="battery-indicator">
                                        @php
                                            $batteryLevel = rand(10, 100); // Simulation - à remplacer par la vraie valeur
                                            $batteryClass = $batteryLevel > 70 ? 'high' : ($batteryLevel > 30 ? 'medium' : 'low');
                                        @endphp
                                        <div class="battery-bar {{ $batteryClass }}" style="width: {{ $batteryLevel }}%"></div>
                                        <span class="battery-text">{{ $batteryLevel }}%</span>
                                    </div>
                                </div>
                                
                                <div class="stat-item">
                                    <div class="stat-label">Dernière Lecture</div>
                                    @if($sensor->last_reading_at)
                                        <div class="last-reading">
                                            <div class="reading-value">{{ $sensor->last_reading }}</div>
                                            <div class="reading-time">{{ $sensor->last_reading_at->diffForHumans() }}</div>
                                        </div>
                                    @else
                                        <span class="no-data">Aucune donnée</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="sensor-card-footer">
                            <div class="actions">
                                <a href="{{ route('sensors.show', $sensor->id) }}" class="btn-icon" title="Voir les détails">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('sensors.edit', $sensor->id) }}" class="btn-icon" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('sensors.configure', $sensor->id) }}" class="btn-icon configure" title="Configurer">
                                    <i class="fas fa-cog"></i>
                                </a>
                                <a href="{{ route('sensors.history', $sensor->id) }}" class="btn-icon history" title="Historique">
                                    <i class="fas fa-history"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-sensor"></i>
                        </div>
                        <h3>Aucun capteur trouvé</h3>
                        <p>Aucun capteur ne correspond à vos critères de recherche ou aucun capteur n'a été ajouté au système.</p>
                        <a href="{{ route('sensors.create') }}" class="btn primary-btn">
                            <i class="fas fa-plus-circle"></i> Ajouter un capteur
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
        <div class="card-footer">
            <div class="pagination-container">
                {{ $capteurs->links('pagination.custun') }}
            </div>
        </div>
    </div>
</div>

<!-- Modal pour la suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer ce capteur? Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn outline-btn" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn danger-btn" id="confirmDelete">Supprimer</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('styles')
<style>
    /* Styles pour la page des capteurs */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 2rem;
    }
    
    .stat-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        padding: 1.5rem;
        display: flex;
        align-items: center;
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
    }
    
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
    }
    
    .stat-icon i {
        font-size: 24px;
        color: white;
    }
    
    .sensor-active {
        background: linear-gradient(135deg, #23b0ad, #1a8a8a);
    }
    
    .sensor-warning {
        background: linear-gradient(135deg, #f39c12, #e67e22);
    }
    
    .sensor-inactive {
        background: linear-gradient(135deg, #95a5a6, #7f8c8d);
    }
    
    .sensor-error {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
    }
    
    .stat-content h3 {
        font-size: 14px;
        color: #777;
        margin-bottom: 0.5rem;
    }
    
    .stat-value {
        font-size: 24px;
        font-weight: bold;
        color: #333;
        margin: 0;
    }
    
    /* Filtres et recherche */
    .filters-section {
        background: white;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        padding: 1rem;
        margin-bottom: 2rem;
    }
    
    .filter-form {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    @media (min-width: 768px) {
        .filter-form {
            grid-template-columns: 1fr 2fr;
        }
    }
    
    .search-bar {
        position: relative;
    }
    
    .search-bar i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #777;
    }
    
    .search-bar input {
        padding: 0.75rem 1rem 0.75rem 2.5rem;
        width: 100%;
        border: 1px solid #ddd;
        border-radius: 30px;
        font-size: 14px;
        transition: all 0.3s ease;
    }
    
    .search-bar input:focus {
        border-color: #23b0ad;
        box-shadow: 0 0 0 3px rgba(35, 176, 173, 0.2);
        outline: none;
    }
    
    .filters {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
    }
    
    .filter-select {
        padding: 0.75rem 1rem;
        border: 1px solid #ddd;
        border-radius: 30px;
        font-size: 14px;
        min-width: 150px;
        flex-grow: 1;
    }
    
    .filter-btn {
        padding: 0.75rem 1.5rem;
        background: #23b0ad;
        color: white;
        border: none;
        border-radius: 30px;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .filter-btn:hover {
        background: #1a8a8a;
    }
    
    .outline-btn {
        padding: 0.75rem 1.5rem;
        background: transparent;
        color: #777;
        border: 1px solid #ddd;
        border-radius: 30px;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .outline-btn:hover {
        background: #f5f5f5;
    }
    
    /* Table des capteurs */
    .sensors-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        margin-bottom: 2rem;
    }
    
    .card-header {
        padding: 1.5rem;
        border-bottom: 1px solid #eee;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .card-header h2 {
        font-size: 18px;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .card-tools {
        display: flex;
        gap: 0.5rem;
    }
    
    .btn-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        background: #f5f5f5;
        border: none;
    }
    
    .btn-icon:hover {
        background: #eee;
    }
    
    .btn-icon.active {
        background: #23b0ad;
        color: white;
    }
    
    .sensors-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .sensors-table th {
        padding: 1rem;
        font-weight: 600;
        text-align: left;
        border-bottom: 1px solid #eee;
        color: #555;
    }
    
    .sensors-table td {
        padding: 1rem;
        border-bottom: 1px solid #eee;
        vertical-align: middle;
    }
    
    .sensors-table tr:hover {
        background: #f9f9f9;
    }
    
    /* Styles pour le type de capteur */
    .sensor-type {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .sensor-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f5f5f5;
    }
    
    .sensor-icon.temperature {
        background: rgba(243, 156, 18, 0.2);
        color: #f39c12;
    }
    
    .sensor-icon.pulse {
        background: rgba(231, 76, 60, 0.2);
        color: #e74c3c;
    }
    
    .sensor-icon.oxygen {
        background: rgba(52, 152, 219, 0.2);
        color: #3498db;
    }
    
    .sensor-icon.blood-pressure {
        background: rgba(142, 68, 173, 0.2);
        color: #8e44ad;
    }
    
    .sensor-icon.glucose {
        background: rgba(46, 204, 113, 0.2);
        color: #2ecc71;
    }
    
    /* Patient info */
    .patient-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        overflow: hidden;
    }
    
    .avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    /* Badges de statut */
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
    
    /* Indicateur de batterie */
    .battery-indicator {
        width: 100%;
        max-width: 150px;
        height: 10px;
        background: #eee;
        border-radius: 5px;
        position: relative;
        margin-bottom: 5px;
    }
    
    .battery-bar {
        height: 100%;
        border-radius: 5px;
    }
    
    .battery-bar.high {
        background: #2ecc71;
    }
    
    .battery-bar.medium {
        background: #f39c12;
    }
    
    .battery-bar.low {
        background: #e74c3c;
    }
    
    .battery-text {
        font-size: 12px;
        color: #777;
    }
    
    /* Dernière lecture */
    .last-reading {
        display: flex;
        flex-direction: column;
    }
    
    .reading-value {
        font-weight: 600;
    }
    
    .reading-time {
        font-size: 12px;
        color: #777;
    }
    
    .no-data {
        font-size: 12px;
        color: #999;
        font-style: italic;
    }
    
    /* Actions */
    .actions {
        display: flex;
        gap: 0.5rem;
    }
    
    .actions .btn-icon {
        color: #555;
    }
    
    .actions .btn-icon:hover {
        color: #23b0ad;
        background: rgba(35, 176, 173, 0.1);
    }
    
    .actions .btn-icon.configure:hover {
        color: #3498db;
        background: rgba(52, 152, 219, 0.1);
    }
    
    .actions .btn-icon.history:hover {
        color: #9b59b6;
        background: rgba(155, 89, 182, 0.1);
    }
    
    .actions .btn-icon.delete:hover {
        color: #e74c3c;
        background: rgba(231, 76, 60, 0.1);
    }
    
    /* État vide */
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
    }
    
    .empty-icon {
        width: 80px;
        height: 80px;
        background: #f5f5f5;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
    }
    
    .empty-icon i {
        font-size: 32px;
        color: #aaa;
    }
    
    .empty-state h3 {
        font-size: 18px;
        margin-bottom: 0.75rem;
    }
    
    .empty-state p {
        color: #777;
        max-width: 400px;
        margin: 0 auto 1.5rem;
    }
    
    /* Vue en grille */
    .sensors-grid-view {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.5rem;
        padding: 1.5rem;
    }
    
    .sensor-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .sensor-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.12);
    }
    
    .sensor-card-header {
        padding: 1.25rem;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .sensor-card-body {
        padding: 1.25rem;
    }
    
    .sensor-card-body h3 {
        margin: 0 0 0.25rem;
        font-size: 18px;
    }
    
    .sensor-card-body .model {
        color: #777;
        margin-bottom: 1rem;
    }
    
    .no-patient {
        padding: 0.5rem 0;
        color: #999;
        font-style: italic;
    }
    
    .sensor-stats {
        margin-top: 1.5rem;
    }
    
    .stat-item {
        margin-bottom: 1rem;
    }
    
    .stat-label {
        font-size: 12px;
        color: #777;
        margin-bottom: 0.5rem;
    }
    
    .sensor-card-footer {
        border-top: 1px solid #eee;
        padding: 1rem;
        display: flex;
        justify-content: center;
    }
    
    /* Pagination */
    .card-footer {
        padding: 1.25rem;
        border-top: 1px solid #eee;
    }
    
    /* Modal */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }
    
    .modal.fade.show {
        display: flex;
    }
    
    .modal-dialog {
        width: 90%;
        max-width: 500px;
    }
    
    .modal-content {
        background: white;
        border-radius: 10px;
        overflow: hidden;
    }
    
    .modal-header {
        padding: 1.25rem;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .modal-title {
        font-size: 18px;
        margin: 0;
    }
    
    .close {
        background: transparent;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
    }
    
    .modal-body {
        padding: 1.5rem;
    }
    
    .modal-footer {
        padding: 1.25rem;
        border-top: 1px solid #eee;
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
    }
    
    .danger-btn {
        padding: 0.75rem 1.5rem;
        background: #e74c3c;
        color: white;
        border: none;
        border-radius: 30px;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .danger-btn:hover {
        background: #c0392b;
    }
    
    /* Assign form */
    .assign-form {
        display: inline-block;
    }
    
    .mini-btn {
        padding: 0.35rem 0.75rem;
        font-size: 12px;
        border-radius: 15px;
        background: rgba(52, 152, 219, 0.2);
        color: #3498db;
        border: none;
        display: flex;
        align-items: center;
        gap: 0.35rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .mini-btn:hover {
        background: rgba(52, 152, 219, 0.3);
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle entre vue liste et grille
        const toggleViewButtons = document.querySelectorAll('.toggle-view');
        const tableView = document.querySelector('.table-responsive');
        const gridView = document.querySelector('.sensors-grid-view');
        
        toggleViewButtons.forEach(button => {
            button.addEventListener('click', function() {
                const viewType = this.dataset.view;
                
                // Update active button
                toggleViewButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                
                // Show the selected view
                if (viewType === 'grid') {
                    tableView.style.display = 'none';
                    gridView.style.display = 'grid';
                } else {
                    tableView.style.display = 'block';
                    gridView.style.display = 'none';
                }
            });
        });
        
        // Confirmation de suppression
        const deleteModal = document.getElementById('deleteModal');
        const deleteButtons = document.querySelectorAll('.delete-form .delete');
        const confirmDeleteBtn = document.getElementById('confirmDelete');
        let formToSubmit;
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                formToSubmit = this.closest('form');
                
                // Show modal
                deleteModal.classList.add('show');
            });
        });
        
        // Close modal
        const closeButtons = document.querySelectorAll('[data-dismiss="modal"]');
        closeButtons.forEach(button => {
            button.addEventListener('click', function() {
                deleteModal.classList.remove('show');
            });
        });
        
        // Confirm delete
        confirmDeleteBtn.addEventListener('click', function() {
            if (formToSubmit) {
                formToSubmit.submit();
            }
        });
        
        // Close modal when clicking outside
        window.addEventListener('click', function(e) {
            if (e.target === deleteModal) {
                deleteModal.classList.remove('show');
            }
        });
    });
</script>
@endsection