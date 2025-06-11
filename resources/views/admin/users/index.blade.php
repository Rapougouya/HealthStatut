@extends('layouts.app')

@section('title', 'Liste des Utilisateurs')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-modern.css') }}">
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

        .users-container {
            background: var(--primary-gradient);
            min-height: 100vh;
            padding: 20px;
        }

        .users-header {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid var(--border-color);
        }

        .header-title {
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

        .header-title h1 {
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
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--card-bg);
            backdrop-filter: blur(15px);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            border-left: 4px solid;
            text-align: center;
        }

        .stat-card:hover {
            transform: translateY(-3px);
        }

        .stat-card.total { border-left-color: var(--info-color); }
        .stat-card.active { border-left-color: var(--success-color); }
        .stat-card.inactive { border-left-color: var(--danger-color); }
        .stat-card.admins { border-left-color: var(--warning-color); }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 10px 0 5px;
        }

        .stat-label {
            color: var(--text-secondary);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Formulaire d'ajout utilisateur amélioré */
        .add-user-form {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid var(--border-color);
            display: none;
            animation: slideDown 0.3s ease-out;
        }

        .add-user-form.show {
            display: block;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid rgba(102, 126, 234, 0.1);
        }

        .form-title {
            display: flex;
            align-items: center;
            gap: 15px;
            color: var(--text-primary);
            font-size: 1.5rem;
            font-weight: 600;
        }

        .form-title i {
            color: #667eea;
            font-size: 1.3rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .form-label i {
            color: #667eea;
            font-size: 0.9rem;
        }

        .form-input, .form-select {
            padding: 12px 15px;
            border: 2px solid rgba(102, 126, 234, 0.1);
            border-radius: 10px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
        }

        .form-input:focus, .form-select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            background: white;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            padding-top: 20px;
            border-top: 1px solid rgba(102, 126, 234, 0.1);
        }

        .users-content {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid var(--border-color);
        }

        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .search-filters {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }

        .search-box {
            position: relative;
            display: flex;
            align-items: center;
        }

        .search-input {
            padding: 12px 15px 12px 45px;
            border: 2px solid rgba(102, 126, 234, 0.1);
            border-radius: 10px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
            width: 300px;
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
            color: var(--text-secondary);
            font-size: 1rem;
        }

        .filter-select {
            padding: 12px 15px;
            border: 2px solid rgba(102, 126, 234, 0.1);
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
            cursor: pointer;
        }

        /* Actions en lot */
        .bulk-actions {
            display: none;
            background: rgba(102, 126, 234, 0.1);
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            align-items: center;
            gap: 15px;
        }

        .bulk-actions.show {
            display: flex;
        }

        .bulk-info {
            color: var(--text-primary);
            font-weight: 600;
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

        .users-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .users-table th {
            background: rgba(102, 126, 234, 0.1);
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: var(--text-primary);
            border-bottom: 2px solid rgba(102, 126, 234, 0.2);
        }

        .users-table td {
            padding: 15px;
            border-bottom: 1px solid rgba(102, 126, 234, 0.1);
            vertical-align: middle;
        }

        .users-table tr:hover {
            background: rgba(102, 126, 234, 0.05);
        }

        /* Checkbox personnalisé */
        .custom-checkbox {
            position: relative;
            display: inline-block;
            width: 20px;
            height: 20px;
        }

        .custom-checkbox input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .checkbox-checkmark {
            position: absolute;
            top: 0;
            left: 0;
            height: 20px;
            width: 20px;
            background-color: #fff;
            border: 2px solid rgba(102, 126, 234, 0.3);
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .custom-checkbox:hover input ~ .checkbox-checkmark {
            border-color: #667eea;
        }

        .custom-checkbox input:checked ~ .checkbox-checkmark {
            background-color: #667eea;
            border-color: #667eea;
        }

        .checkbox-checkmark:after {
            content: "";
            position: absolute;
            display: none;
        }

        .custom-checkbox input:checked ~ .checkbox-checkmark:after {
            display: block;
        }

        .custom-checkbox .checkbox-checkmark:after {
            left: 6px;
            top: 2px;
            width: 6px;
            height: 10px;
            border: solid white;
            border-width: 0 3px 3px 0;
            transform: rotate(45deg);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.2rem;
        }

        .user-avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        .user-details {
            flex: 1;
        }

        .user-name {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 5px;
        }

        .user-email {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }

        .badge-danger {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
        }

        .badge-primary {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
        }

        .badge-warning {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning-color);
        }

        .actions-group {
            display: flex;
            gap: 8px;
            align-items: center;
            justify-content: center;
        }

        .btn-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            transition: all 0.2s ease;
            cursor: pointer;
            border: none;
            position: relative;
        }

        .btn-icon:hover {
            transform: scale(1.1);
        }

        /* Tooltips personnalisés */
        .tooltip {
            position: relative;
            display: inline-block;
        }

        .tooltip .tooltiptext {
            visibility: hidden;
            width: 120px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 5px 8px;
            font-size: 12px;
            position: absolute;
            z-index: 1000;
            bottom: 125%;
            left: 50%;
            margin-left: -60px;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .tooltip .tooltiptext::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: #333 transparent transparent transparent;
        }

        .tooltip:hover .tooltiptext {
            visibility: visible;
            opacity: 1;
        }

        /* Amélioration de la section des actions */
        .actions-group {
            display: flex;
            gap: 8px;
            align-items: center;
            justify-content: center;
        }

        .action-label {
            display: none;
            font-size: 0.75rem;
            color: var(--text-secondary);
            margin-top: 2px;
        }

        @media (min-width: 1200px) {
            .btn-icon {
                width: auto;
                height: auto;
                padding: 8px 12px;
                border-radius: 6px;
                flex-direction: column;
                gap: 4px;
            }
            
            .action-label {
                display: block;
            }
        }

        @media (max-width: 768px) {
            .users-container { padding: 15px; }
            .users-header { padding: 20px; }
            .header-title h1 { font-size: 2rem; }
            .search-input { width: 100%; }
            .content-header { flex-direction: column; align-items: stretch; }
            .search-filters { flex-direction: column; }
            .users-table { font-size: 0.8rem; }
            .users-table th, .users-table td { padding: 10px; }
            .user-info { flex-direction: column; text-align: center; }
            .actions-group { justify-content: center; }
        }

        /* Modal styles pour les détails utilisateur */
        .modal {
            display: none;
            position: fixed;
            z-index: 10000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
            animation: fadeIn 0.3s ease-out;
        }

        .modal-content {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            margin: 2% auto;
            border-radius: 15px;
            width: 90%;
            max-width: 800px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: slideUp 0.3s ease-out;
        }

        .user-details-modal {
            width: 90%;
            max-width: 700px;
        }

        .modal-header-enhanced {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 25px 30px;
            border-radius: 15px 15px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title-enhanced {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .close {
            cursor: pointer;
            font-size: 1.8rem;
            font-weight: bold;
            transition: all 0.2s ease;
        }

        .close:hover {
            transform: scale(1.1);
            opacity: 0.8;
        }

        .details-content {
            padding: 30px;
        }

        .detail-section {
            background: rgba(102, 126, 234, 0.05);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }

        .detail-section:last-child {
            margin-bottom: 0;
        }

        .detail-section-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            padding-bottom: 10px;
            border-bottom: 2px solid rgba(102, 126, 234, 0.1);
        }

        .detail-section-title i {
            color: #667eea;
            font-size: 1.1rem;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(102, 126, 234, 0.1);
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: var(--text-primary);
            font-size: 0.95rem;
        }

        .detail-value {
            color: var(--text-secondary);
            font-size: 0.95rem;
        }

        .detail-value .badge {
            font-size: 0.8rem;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Toast notifications */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 10px;
            color: white;
            z-index: 10001;
            font-weight: 500;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            animation: slideInRight 0.3s ease-out;
        }

        .toast-success {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .toast-error {
            background: linear-gradient(135deg, #ef4444, #dc2626);
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100%);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @media (max-width: 768px) {
            .modal-content {
                width: 95%;
                margin: 5% auto;
            }
            
            .details-content {
                padding: 20px;
            }
            
            .detail-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
            
            .detail-label {
                font-weight: 700;
            }
        }
    </style>
@endsection

@section('content')
<div class="users-container">
    <div class="users-header">
        <div class="header-title">
            <div class="title-icon">
                <i class="fas fa-users"></i>
            </div>
            <h1>Gestion des Utilisateurs</h1>
        </div>
        
        <div class="stats-overview">
            <div class="stat-card total">
                <div class="stat-number">{{ $totalUsers ?? $users->count() }}</div>
                <div class="stat-label">Total Utilisateurs</div>
            </div>
            <div class="stat-card active">
                <div class="stat-number">{{ $activeUsers ?? $users->where('statut', 'actif')->count() }}</div>
                <div class="stat-label">Actifs</div>
            </div>
            <div class="stat-card inactive">
                <div class="stat-number">{{ $inactiveUsers ?? $users->where('statut', 'inactif')->count() }}</div>
                <div class="stat-label">Inactifs</div>
            </div>
            <div class="stat-card admins">
                <div class="stat-number">{{ $adminUsers ?? $users->filter(function($user) { return $user->role && $user->role->nom === 'admin'; })->count() }}</div>
                <div class="stat-label">Administrateurs</div>
            </div>
        </div>
    </div>

    <div class="add-user-form" id="addUserForm">
        <div class="form-header">
            <div class="form-title">
                <i class="fas fa-user-plus"></i>
                <span id="formTitle">Ajouter un Utilisateur</span>
            </div>
            <button type="button" class="btn btn-secondary" onclick="toggleAddUserForm()">
                <i class="fas fa-times"></i>
                Annuler
            </button>
        </div>
        
        <form id="userForm" action="{{ route('utilisateurs.store') }}" method="POST">
            @csrf
            <input type="hidden" id="userId" name="user_id">
            <input type="hidden" id="methodField" name="_method" value="POST">
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="nom" class="form-label">
                        <i class="fas fa-user"></i>
                        Nom *
                    </label>
                    <input type="text" id="nom" name="nom" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label for="prenom" class="form-label">
                        <i class="fas fa-user"></i>
                        Prénom *
                    </label>
                    <input type="text" id="prenom" name="prenom" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope"></i>
                        Email *
                    </label>
                    <input type="email" id="email" name="email" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label for="role_id" class="form-label">
                        <i class="fas fa-shield-alt"></i>
                        Rôle *
                    </label>
                    <select id="role_id" name="role_id" class="form-select" required>
                        <option value="">Sélectionner un rôle</option>
                        @foreach($roles as $role)
                        <option value="{{ $role->id }}">{{ ucfirst($role->nom) }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="service_id" class="form-label">
                        <i class="fas fa-building"></i>
                        Service *
                    </label>
                    <select id="service_id" name="service_id" class="form-select" required>
                        <option value="">Sélectionner un service</option>
                        @foreach($services as $service)
                        <option value="{{ $service->id }}">{{ $service->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="toggleAddUserForm()">
                    <i class="fas fa-times"></i>
                    Annuler
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Enregistrer l'utilisateur
                </button>
            </div>
        </form>
    </div>

    <div class="users-content">
        <div class="content-header">
            <div class="search-filters">
                <div class="search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="searchUsers" class="search-input" placeholder="Rechercher un utilisateur...">
                </div>
                
                <select id="roleFilter" class="filter-select">
                    <option value="">Tous les rôles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}">{{ ucfirst($role->nom) }}</option>
                    @endforeach
                </select>
                
                <select id="statusFilter" class="filter-select">
                    <option value="">Tous les statuts</option>
                    <option value="actif">Actifs</option>
                    <option value="inactif">Inactifs</option>
                </select>
                
                <select id="serviceFilter" class="filter-select">
                    <option value="">Tous les services</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}">{{ $service->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <button id="addUserBtn" class="btn btn-primary" onclick="toggleAddUserForm()">
                <i class="fas fa-user-plus"></i>
                Nouvel Utilisateur
            </button>
        </div>

        <div class="bulk-actions" id="bulkActions">
            <div class="bulk-info">
                <span id="selectedCount">0</span> utilisateur(s) sélectionné(s)
            </div>
            <button class="btn btn-success" onclick="bulkAction('activate')">
                <i class="fas fa-user-check"></i>
                Activer
            </button>
            <button class="btn btn-warning" onclick="bulkAction('deactivate')">
                <i class="fas fa-user-slash"></i>
                Désactiver
            </button>
            <button class="btn btn-danger" onclick="bulkAction('delete')">
                <i class="fas fa-trash"></i>
                Supprimer
            </button>
        </div>

        <div class="table-responsive">
            <table class="users-table" id="usersTable">
                <thead>
                    <tr>
                        <th style="width: 50px;">
                            <label class="custom-checkbox">
                                <input type="checkbox" id="selectAll">
                                <span class="checkbox-checkmark"></span>
                            </label>
                        </th>
                        <th><i class="fas fa-user"></i> Utilisateur</th>
                        <th><i class="fas fa-shield-alt"></i> Rôle</th>
                        <th><i class="fas fa-building"></i> Service</th>
                        <th><i class="fas fa-toggle-on"></i> Statut</th>
                        <th><i class="fas fa-clock"></i> Dernière Connexion</th>
                        <th style="width: 200px;"><i class="fas fa-cogs"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr data-user-id="{{ $user->id }}" data-role="{{ $user->role_id }}" data-service="{{ $user->service_id }}" data-status="{{ $user->statut }}">
                        <td>
                            <label class="custom-checkbox">
                                <input type="checkbox" class="user-select" value="{{ $user->id }}">
                                <span class="checkbox-checkmark"></span>
                            </label>
                        </td>
                        <td>
                            <div class="user-info">
                                <div class="user-avatar">
                                    @if($user->avatar)
                                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->nom }}">
                                    @else
                                        {{ strtoupper(substr($user->prenom, 0, 1) . substr($user->nom, 0, 1)) }}
                                    @endif
                                </div>
                                <div class="user-details">
                                    <div class="user-name">{{ $user->prenom }} {{ $user->nom }}</div>
                                    <div class="user-email">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge {{ $user->role && $user->role->nom === 'admin' ? 'badge-danger' : ($user->role && $user->role->nom === 'manager' ? 'badge-warning' : 'badge-primary') }}">
                                <i class="fas fa-{{ $user->role && $user->role->nom === 'admin' ? 'crown' : ($user->role && $user->role->nom === 'manager' ? 'user-tie' : 'user') }}"></i>
                                {{ $user->role->nom ?? 'N/A' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-primary">
                                <i class="fas fa-building"></i>
                                {{ $user->service->name ?? 'N/A' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $user->statut === 'actif' ? 'badge-success' : 'badge-danger' }}">
                                <i class="fas fa-{{ $user->statut === 'actif' ? 'check-circle' : 'times-circle' }}"></i>
                                {{ ucfirst($user->statut) }}
                            </span>
                        </td>
                        <td>
                            @if($user->derniere_connexion)
                                <span title="{{ $user->derniere_connexion->format('d/m/Y H:i:s') }}">
                                    <i class="fas fa-calendar"></i>
                                    {{ $user->derniere_connexion->diffForHumans() }}
                                </span>
                            @else
                                <span class="text-muted">
                                    <i class="fas fa-minus"></i>
                                    Jamais connecté
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="actions-group">
                                <!-- Voir détails -->
                                <div class="tooltip">
                                    <button class="btn-icon btn-outline-primary view-details"
                                            data-id="{{ $user->id }}"
                                            data-nom="{{ $user->nom }}"
                                            data-prenom="{{ $user->prenom }}"
                                            data-email="{{ $user->email }}"
                                            data-role="{{ $user->role->nom ?? 'N/A' }}"
                                            data-service="{{ $user->service->name ?? 'N/A' }}"
                                            data-statut="{{ $user->statut }}"
                                            data-connexion="{{ $user->derniere_connexion ? $user->derniere_connexion->format('d/m/Y H:i:s') : 'Jamais connecté' }}"
                                            data-created="{{ $user->created_at ? $user->created_at->format('d/m/Y H:i:s') : 'N/A' }}"
                                            data-phone="{{ $user->telephone ?? 'Non renseigné' }}">
                                        <i class="fas fa-eye"></i>
                                        <span class="action-label">Détails</span>
                                    </button>
                                    <span class="tooltiptext">Voir les détails</span>
                                </div>
                                
                                <!-- Modifier -->
                                <div class="tooltip">
                                    <button class="btn-icon btn-outline-primary edit-user" 
                                            data-id="{{ $user->id }}" 
                                            data-nom="{{ $user->nom }}" 
                                            data-prenom="{{ $user->prenom }}" 
                                            data-email="{{ $user->email }}" 
                                            data-role="{{ $user->role_id }}" 
                                            data-service="{{ $user->service_id }}">
                                        <i class="fas fa-edit"></i>
                                        <span class="action-label">Modifier</span>
                                    </button>
                                    <span class="tooltiptext">Modifier l'utilisateur</span>
                                </div>
                                
                                <!-- Activer/Désactiver -->
                                <div class="tooltip">
                                    <form action="{{ route('utilisateurs.toggle-status', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn-icon {{ $user->statut === 'actif' ? 'btn-outline-warning' : 'btn-outline-success' }}">
                                            <i class="fas fa-{{ $user->statut === 'actif' ? 'user-slash' : 'user-check' }}"></i>
                                            <span class="action-label">{{ $user->statut === 'actif' ? 'Désactiver' : 'Activer' }}</span>
                                        </button>
                                    </form>
                                    <span class="tooltiptext">{{ $user->statut === 'actif' ? 'Désactiver' : 'Activer' }} l'utilisateur</span>
                                </div>
                                
                                <!-- Réinitialiser mot de passe -->
                                <div class="tooltip">
                                    <form action="{{ route('utilisateurs.reset-password', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn-icon btn-outline-info"
                                                onclick="return confirm('Êtes-vous sûr de vouloir réinitialiser le mot de passe de cet utilisateur ?')">
                                            <i class="fas fa-key"></i>
                                            <span class="action-label">Reset MDP</span>
                                        </button>
                                    </form>
                                    <span class="tooltiptext">Réinitialiser le mot de passe</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">
                            <div style="padding: 40px;">
                                <i class="fas fa-users" style="font-size: 3rem; color: #ccc; margin-bottom: 15px;"></i>
                                <p style="color: #999; font-size: 1.1rem;">Aucun utilisateur trouvé</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($users, 'links'))
        <div class="pagination-wrapper">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Détails Utilisateur -->
<div id="userDetailsModal" class="modal">
    <div class="modal-content user-details-modal">
        <div class="modal-header-enhanced">
            <h2 class="modal-title-enhanced">
                <i class="fas fa-user-circle"></i>
                Détails de l'Utilisateur
            </h2>
            <span class="close" onclick="closeModal('userDetailsModal')">&times;</span>
        </div>
        
        <div class="details-content" id="userDetailsContent">
            <!-- Contenu dynamique -->
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const userDetailsModal = document.getElementById('userDetailsModal');
    const addUserForm = document.getElementById('addUserForm');
    const userForm = document.getElementById('userForm');
    
    let selectedUsers = [];
    
    // Boutons de modification d'utilisateur
    document.querySelectorAll('.edit-user').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-id');
            const nom = this.getAttribute('data-nom');
            const prenom = this.getAttribute('data-prenom');
            const email = this.getAttribute('data-email');
            const roleId = this.getAttribute('data-role');
            const serviceId = this.getAttribute('data-service');
            
            document.getElementById('formTitle').textContent = 'Modifier l\'Utilisateur';
            document.getElementById('userId').value = userId;
            document.getElementById('nom').value = nom;
            document.getElementById('prenom').value = prenom;
            document.getElementById('email').value = email;
            document.getElementById('role_id').value = roleId;
            document.getElementById('service_id').value = serviceId;
            
            userForm.setAttribute('action', `{{ url('utilisateurs') }}/${userId}`);
            document.getElementById('methodField').value = 'PUT';
            
            addUserForm.classList.add('show');
        });
    });
    
    // Boutons de détails utilisateur
    document.querySelectorAll('.view-details').forEach(button => {
        button.addEventListener('click', function() {
            const userData = {
                nom: this.getAttribute('data-nom'),
                prenom: this.getAttribute('data-prenom'),
                email: this.getAttribute('data-email'),
                role: this.getAttribute('data-role'),
                service: this.getAttribute('data-service'),
                statut: this.getAttribute('data-statut'),
                connexion: this.getAttribute('data-connexion'),
                created: this.getAttribute('data-created'),
                phone: this.getAttribute('data-phone')
            };
            
            const initials = userData.prenom.charAt(0) + userData.nom.charAt(0);
            
            document.getElementById('userDetailsContent').innerHTML = `
                <div style="text-align: center; margin-bottom: 30px;">
                    <div style="width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #667eea, #764ba2); display: inline-flex; align-items: center; justify-content: center; color: white; font-size: 1.8rem; font-weight: 600; margin-bottom: 15px;">
                        ${initials.toUpperCase()}
                    </div>
                    <h3 style="color: var(--text-primary); margin: 0; font-size: 1.4rem;">${userData.prenom} ${userData.nom}</h3>
                    <p style="color: var(--text-secondary); margin: 5px 0 0; font-size: 1rem;">${userData.email}</p>
                </div>
                
                <div class="detail-section">
                    <div class="detail-section-title">
                        <i class="fas fa-user"></i> Informations personnelles
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Nom complet :</span>
                        <span class="detail-value">${userData.prenom} ${userData.nom}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Email :</span>
                        <span class="detail-value">${userData.email}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Téléphone :</span>
                        <span class="detail-value">${userData.phone}</span>
                    </div>
                </div>
                
                <div class="detail-section">
                    <div class="detail-section-title">
                        <i class="fas fa-briefcase"></i> Informations professionnelles
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Rôle :</span>
                        <span class="detail-value">
                            <span class="badge badge-primary">
                                <i class="fas fa-user-tag"></i> ${userData.role}
                            </span>
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Service :</span>
                        <span class="detail-value">
                            <span class="badge badge-primary">
                                <i class="fas fa-building"></i> ${userData.service}
                            </span>
                        </span>
                    </div>
                </div>
                
                <div class="detail-section">
                    <div class="detail-section-title">
                        <i class="fas fa-info-circle"></i> Statut et activité
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Statut :</span>
                        <span class="detail-value">
                            <span class="badge ${userData.statut === 'actif' ? 'badge-success' : 'badge-danger'}">
                                <i class="fas fa-${userData.statut === 'actif' ? 'check-circle' : 'times-circle'}"></i>
                                ${userData.statut === 'actif' ? 'Actif' : 'Inactif'}
                            </span>
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Dernière connexion :</span>
                        <span class="detail-value">
                            <i class="fas fa-clock"></i> ${userData.connexion}
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Date de création :</span>
                        <span class="detail-value">
                            <i class="fas fa-calendar-plus"></i> ${userData.created}
                        </span>
                    </div>
                </div>
            `;
            
            userDetailsModal.style.display = 'block';
        });
    });
    
    // Boutons de suppression
    document.querySelectorAll('.delete-user').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-id');
            if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ url('utilisateurs') }}/${userId}`;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                
                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            }
        });
    });
    
    // Gestion des checkboxes
    const selectAllCheckbox = document.getElementById('selectAll');
    const userCheckboxes = document.querySelectorAll('.user-select');
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selectedCount');
    
    selectAllCheckbox.addEventListener('change', function() {
        userCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelectedUsers();
    });
    
    userCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedUsers);
    });
    
    function updateSelectedUsers() {
        selectedUsers = Array.from(userCheckboxes)
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.value);
        
        selectedCount.textContent = selectedUsers.length;
        
        if (selectedUsers.length > 0) {
            bulkActions.classList.add('show');
        } else {
            bulkActions.classList.remove('show');
        }
        
        selectAllCheckbox.checked = selectedUsers.length === userCheckboxes.length;
        selectAllCheckbox.indeterminate = selectedUsers.length > 0 && selectedUsers.length < userCheckboxes.length;
    }
    
    // Filtres et recherche
    const searchInput = document.getElementById('searchUsers');
    const roleFilter = document.getElementById('roleFilter');
    const statusFilter = document.getElementById('statusFilter');
    const serviceFilter = document.getElementById('serviceFilter');
    
    function filterUsers() {
        const searchText = searchInput.value.toLowerCase();
        const selectedRole = roleFilter.value;
        const selectedStatus = statusFilter.value;
        const selectedService = serviceFilter.value;
        
        const rows = document.querySelectorAll('#usersTable tbody tr[data-user-id]');
        
        rows.forEach(row => {
            const userInfo = row.querySelector('.user-info');
            const userName = userInfo ? userInfo.textContent.toLowerCase() : '';
            const userRole = row.getAttribute('data-role');
            const userStatus = row.getAttribute('data-status');
            const userService = row.getAttribute('data-service');
            
            const matchesSearch = userName.includes(searchText);
            const matchesRole = !selectedRole || userRole === selectedRole;
            const matchesStatus = !selectedStatus || userStatus === selectedStatus;
            const matchesService = !selectedService || userService === selectedService;
            
            if (matchesSearch && matchesRole && matchesStatus && matchesService) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
        
        updateSelectedUsers();
    }
    
    searchInput.addEventListener('input', filterUsers);
    roleFilter.addEventListener('change', filterUsers);
    statusFilter.addEventListener('change', filterUsers);
    serviceFilter.addEventListener('change', filterUsers);
    
    // Gestion des modales
    window.onclick = function(event) {
        if (event.target === userDetailsModal) {
            userDetailsModal.style.display = 'none';
        }
    };
    
    // Animation d'entrée
    const cards = document.querySelectorAll('.stat-card, .users-content');
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

function toggleAddUserForm() {
    const form = document.getElementById('addUserForm');
    const userForm = document.getElementById('userForm');
    
    if (form.classList.contains('show')) {
        form.classList.remove('show');
        resetUserForm();
    } else {
        document.getElementById('formTitle').textContent = 'Ajouter un Utilisateur';
        userForm.setAttribute('action', '{{ route("utilisateurs.store") }}');
        document.getElementById('methodField').value = 'POST';
        document.getElementById('userId').value = '';
        form.classList.add('show');
    }
}

function resetUserForm() {
    document.getElementById('userForm').reset();
    document.getElementById('userId').value = '';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function bulkAction(action) {
    const selectedUsers = Array.from(document.querySelectorAll('.user-select:checked')).map(cb => cb.value);
    
    if (selectedUsers.length === 0) {
        showToast('Aucun utilisateur sélectionné', 'error');
        return;
    }
    
    let message = '';
    switch(action) {
        case 'activate':
            message = `Activer ${selectedUsers.length} utilisateur(s) ?`;
            break;
        case 'deactivate':
            message = `Désactiver ${selectedUsers.length} utilisateur(s) ?`;
            break;
        case 'delete':
            message = `Supprimer définitivement ${selectedUsers.length} utilisateur(s) ?`;
            break;
    }
    
    if (confirm(message)) {
        showToast(`Action "${action}" exécutée sur ${selectedUsers.length} utilisateur(s)`, 'success');
        
        document.querySelectorAll('.user-select').forEach(cb => cb.checked = false);
        document.getElementById('selectAll').checked = false;
        document.getElementById('bulkActions').classList.remove('show');
    }
}

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 8px;
        color: white;
        z-index: 10000;
        animation: slideIn 0.3s ease-out;
        ${type === 'success' ? 'background: #10b981;' : 'background: #ef4444;'}
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 4000);
}

@if(session('success'))
    showToast('{{ session('success') }}', 'success');
@endif

@if(session('error'))
    showToast('{{ session('error') }}', 'error');
@endif
</script>
@endsection
