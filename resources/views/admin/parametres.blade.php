@extends('layouts.app')

@section('title', 'Paramètres administratifs')

@section('styles')
<style>
    .admin-panel {
        background-color: #f8fafc;
        border-radius: 0.5rem;
    }
    
    .tab-content {
        display: none;
    }
    
    .tab-content.active {
        display: block;
    }
    
    .nav-tabs .nav-link {
        color: #64748b;
        border-bottom: 2px solid transparent;
        padding: 1rem 1.5rem;
        font-weight: 500;
        transition: all 0.2s ease-in-out;
    }
    
    .nav-tabs .nav-link:hover {
        color: #334155;
        border-color: #94a3b8;
    }
    
    .nav-tabs .nav-link.active {
        color: #1e40af;
        border-color: #1e40af;
    }
    
    .card {
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    
    .card-header {
        border-bottom: 1px solid #e2e8f0;
        padding: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #f8fafc;
        border-top-left-radius: 0.5rem;
        border-top-right-radius: 0.5rem;
    }
    
    .card-header h3 {
        font-weight: 600;
        color: #1e293b;
        margin: 0;
    }
    
    .card-body {
        padding: 1.5rem;
    }
    
    .btn-admin {
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-weight: 500;
        transition: all 0.2s;
    }
    
    .btn-primary {
        background-color: #1e40af;
        color: white;
    }
    
    .btn-primary:hover {
        background-color: #1e3a8a;
    }
    
    .btn-secondary {
        background-color: #64748b;
        color: white;
    }
    
    .btn-secondary:hover {
        background-color: #475569;
    }
    
    .btn-danger {
        background-color: #dc2626;
        color: white;
    }
    
    .btn-danger:hover {
        background-color: #b91c1c;
    }
    
    .btn-success {
        background-color: #16a34a;
        color: white;
    }
    
    .btn-success:hover {
        background-color: #15803d;
    }
    
    .btn-warning {
        background-color: #eab308;
        color: white;
    }
    
    .btn-warning:hover {
        background-color: #ca8a04;
    }
    
    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .status-active {
        background-color: #dcfce7;
        color: #166534;
    }
    
    .status-inactive {
        background-color: #fee2e2;
        color: #991b1b;
    }
    
    .form-control {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        font-size: 0.875rem;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25);
    }
    
    .form-label {
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.5rem;
        display: block;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    table {
        width: 100%;
        border-collapse: collapse;
    }
    
    th {
        text-align: left;
        padding: 0.75rem 1.5rem;
        color: #6b7280;
        border-bottom: 1px solid #e5e7eb;
        font-weight: 500;
    }
    
    td {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        vertical-align: middle;
    }
    
    tr:hover {
        background-color: #f9fafb;
    }
    
    .actions {
        display: flex;
        gap: 0.5rem;
    }
    
    .btn-icon {
        padding: 0.25rem;
        border-radius: 0.25rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2rem;
        height: 2rem;
        transition: all 0.2s;
    }
    
    .search-box {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
    }
    
    .search-box .form-control {
        max-width: 300px;
    }
    
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        padding: 1rem;
        overflow-y: auto;
        align-items: center;
        justify-content: center;
    }
    
    .modal.show {
        display: flex;
    }
    
    .modal-content {
        background-color: white;
        border-radius: 0.5rem;
        width: 100%;
        max-width: 600px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    .modal-header {
        padding: 1rem;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .modal-header h4 {
        margin: 0;
        font-weight: 600;
        color: #1e293b;
    }
    
    .modal-body {
        padding: 1.5rem;
    }
    
    .modal-footer {
        padding: 1rem;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: flex-end;
        gap: 0.5rem;
    }
    
    .close-modal {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #64748b;
    }
    
    .alert {
        padding: 1rem;
        margin-bottom: 1rem;
        border-radius: 0.375rem;
    }
    
    .alert-success {
        background-color: #dcfce7;
        color: #166534;
    }
    
    .alert-danger {
        background-color: #fee2e2;
        color: #991b1b;
    }
    
    .alert-warning {
        background-color: #fef3c7;
        color: #854d0e;
    }
</style>
@endsection

@section('content')
<div class="container mx-auto py-8 px-4">
    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif
    
    @if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif
    
    <div class="admin-panel">
        <div class="mb-8">
            <h1 class="text-3xl font-bold mb-2">Paramètres administratifs</h1>
            <p class="text-gray-600">Gérez les utilisateurs, les services et les paramètres de l'application</p>
        </div>
        
        <ul class="nav nav-tabs mb-6" id="adminTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="users-tab" data-toggle="tab" href="#users" role="tab">
                    <i class="ri-user-line mr-2"></i> Utilisateurs
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="services-tab" data-toggle="tab" href="#services" role="tab">
                    <i class="ri-building-line mr-2"></i> Services
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="system-tab" data-toggle="tab" href="#system" role="tab">
                    <i class="ri-settings-line mr-2"></i> Système
                </a>
            </li>
        </ul>
        
        <div class="tab-content" id="adminTabContent">
            <div class="tab-content active" id="users">
                <div class="card">
                    <div class="card-header">
                        <h3>Gestion des utilisateurs</h3>
                        <button id="addUserBtn" class="btn-admin btn-primary">
                            <i class="ri-user-add-line mr-1"></i> Nouvel utilisateur
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="search-box">
                            <input type="text" id="searchUser" class="form-control" placeholder="Rechercher un utilisateur...">
                            <select class="form-control" id="filterRole">
                                <option value="">Tous les rôles</option>
                                @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->nom }}</option>
                                @endforeach
                            </select>
                            <select class="form-control" id="filterService">
                                <option value="">Tous les services</option>
                                @foreach($services as $service)
                                <option value="{{ $service->id }}">{{ $service->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="table-responsive">
                            <table id="usersTable">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Email</th>
                                        <th>Rôle</th>
                                        <th>Service</th>
                                        <th>Statut</th>
                                        <th>Dernière connexion</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->nom_complet }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->role->nom ?? 'N/A' }}</td>
                                        <td>{{ $user->service->name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="status-badge {{ $user->statut === 'actif' ? 'status-active' : 'status-inactive' }}">
                                                {{ ucfirst($user->statut) }}
                                            </span>
                                        </td>
                                        <td>{{ $user->derniere_connexion ? $user->derniere_connexion->format('d/m/Y H:i') : 'Jamais' }}</td>
                                        <td>
                                            <div class="actions">
                                                <button class="btn-icon btn-secondary edit-user" data-id="{{ $user->id }}" data-name="{{ $user->nom }}" data-firstname="{{ $user->prenom }}" data-email="{{ $user->email }}" data-role="{{ $user->role_id }}" data-service="{{ $user->service_id }}">
                                                    <i class="ri-edit-line"></i>
                                                </button>
                                                
                                                <form action="{{ route('utilisateurs.toggle-status', $user) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="btn-icon {{ $user->statut === 'actif' ? 'btn-warning' : 'btn-success' }}" title="{{ $user->statut === 'actif' ? 'Désactiver' : 'Activer' }} l'utilisateur">
                                                        <i class="ri-{{ $user->statut === 'actif' ? 'user-unfollow-line' : 'user-follow-line' }}"></i>
                                                    </button>
                                                </form>
                                                
                                                <form action="{{ route('utilisateurs.reset-password', $user) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="btn-icon btn-warning" title="Réinitialiser le mot de passe">
                                                        <i class="ri-key-line"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="tab-content" id="services">
                <div class="card">
                    <div class="card-header">
                        <h3>Gestion des services</h3>
                        <button id="addServiceBtn" class="btn-admin btn-primary">
                            <i class="ri-add-line mr-1"></i> Nouveau service
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="search-box">
                            <input type="text" id="searchService" class="form-control" placeholder="Rechercher un service...">
                        </div>
                        
                        <div class="table-responsive">
                            <table id="servicesTable">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Description</th>
                                        <th>Nombre d'utilisateurs</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($services as $service)
                                    <tr>
                                        <td>{{ $service->name }}</td>
                                        <td>{{ $service->description }}</td>
                                        <td>{{ $service->utilisateurs->count() }}</td>
                                        <td>
                                            <div class="actions">
                                                <button class="btn-icon btn-secondary edit-service" data-id="{{ $service->id }}" data-name="{{ $service->name }}" data-description="{{ $service->description }}">
                                                    <i class="ri-edit-line"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="tab-content" id="system">
                <div class="card">
                    <div class="card-header">
                        <h3>Paramètres système</h3>
                    </div>
                    <div class="card-body">
                        <form id="systemSettingsForm">
                            <div class="form-group">
                                <label class="form-label">Titre de l'application</label>
                                <input type="text" class="form-control" value="HealthStatut" disabled>
                                <small class="text-muted">Contactez l'administrateur système pour modifier ce paramètre</small>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Maintenance planifiée</label>
                                <div class="flex items-center">
                                    <input type="checkbox" id="maintenanceMode"> 
                                    <label for="maintenanceMode" class="ml-2">Activer le mode maintenance</label>
                                </div>
                            </div>
                            
                            <div id="maintenanceOptions" style="display: none;">
                                <div class="form-group">
                                    <label class="form-label">Date de début</label>
                                    <input type="datetime-local" class="form-control">
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Durée estimée (heures)</label>
                                    <input type="number" class="form-control" min="1" value="1">
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Message de maintenance</label>
                                    <textarea class="form-control" rows="3">Le système est actuellement en maintenance. Veuillez réessayer ultérieurement.</textarea>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn-admin btn-primary">Enregistrer les modifications</button>
                            </div>
                        </form>
                        
                        <hr class="my-6">
                        
                        <div class="mb-6">
                            <h4 class="text-xl font-semibold mb-4">Actions système</h4>
                            
                            <div class="space-y-4">
                                <div class="card p-4 bg-gray-50">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <h5 class="font-semibold">Vider le cache</h5>
                                            <p class="text-gray-600">Effacer le cache de l'application pour appliquer les dernières modifications</p>
                                        </div>
                                        <button class="btn-admin btn-secondary">Exécuter</button>
                                    </div>
                                </div>
                                
                                <div class="card p-4 bg-gray-50">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <h5 class="font-semibold">Sauvegarde de la base de données</h5>
                                            <p class="text-gray-600">Créer une sauvegarde complète de la base de données</p>
                                        </div>
                                        <button class="btn-admin btn-secondary">Exécuter</button>
                                    </div>
                                </div>
                                
                                <div class="card p-4 bg-yellow-50">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <h5 class="font-semibold text-yellow-800">Réinitialiser les préférences</h5>
                                            <p class="text-yellow-800">Réinitialiser toutes les préférences utilisateurs aux valeurs par défaut</p>
                                        </div>
                                        <button class="btn-admin btn-warning">Exécuter</button>
                                    </div>
                                </div>
                                
                                <div class="card p-4 bg-red-50">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <h5 class="font-semibold text-red-800">Purger les données temporaires</h5>
                                            <p class="text-red-800">Supprimer toutes les données temporaires et les fichiers de journalisation</p>
                                        </div>
                                        <button class="btn-admin btn-danger">Exécuter</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-6">
                        
                        <div>
                            <h4 class="text-xl font-semibold mb-4">Informations système</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="flex justify-between">
                                        <span class="font-medium">Version de l'application:</span>
                                        <span>1.5.2</span>
                                    </p>
                                    <p class="flex justify-between">
                                        <span class="font-medium">Dernière mise à jour:</span>
                                        <span>{{ now()->format('d/m/Y') }}</span>
                                    </p>
                                    <p class="flex justify-between">
                                        <span class="font-medium">Environnement:</span>
                                        <span>{{ ucfirst(app()->environment()) }}</span>
                                    </p>
                                </div>
                                <div>
                                    <p class="flex justify-between">
                                        <span class="font-medium">Version PHP:</span>
                                        <span>{{ phpversion() }}</span>
                                    </p>
                                    <p class="flex justify-between">
                                        <span class="font-medium">Version Laravel:</span>
                                        <span>{{ app()->version() }}</span>
                                    </p>
                                    <p class="flex justify-between">
                                        <span class="font-medium">Serveur:</span>
                                        <span>{{ $_SERVER['SERVER_SOFTWARE'] ?? 'N/A' }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajouter/Modifier Utilisateur -->
<div id="userModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h4 id="userModalTitle">Ajouter un utilisateur</h4>
            <button type="button" class="close-modal">&times;</button>
        </div>
        <div class="modal-body">
            <form id="userForm" action="{{ route('utilisateurs.store') }}" method="POST">
                @csrf
                <input type="hidden" id="userId" name="user_id">
                <div class="form-group">
                    <label for="nom" class="form-label">Nom</label>
                    <input type="text" id="nom" name="nom" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="prenom" class="form-label">Prénom</label>
                    <input type="text" id="prenom" name="prenom" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="role_id" class="form-label">Rôle</label>
                    <select id="role_id" name="role_id" class="form-control" required>
                        <option value="">Sélectionner un rôle</option>
                        @foreach($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="service_id" class="form-label">Service</label>
                    <select id="service_id" name="service_id" class="form-control" required>
                        <option value="">Sélectionner un service</option>
                        @foreach($services as $service)
                        <option value="{{ $service->id }}">{{ $service->name }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-admin btn-secondary close-modal-btn">Annuler</button>
            <button type="button" id="saveUserBtn" class="btn-admin btn-primary">Enregistrer</button>
        </div>
    </div>
</div>

<!-- Modal Ajouter/Modifier Service -->
<div id="serviceModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h4 id="serviceModalTitle">Ajouter un service</h4>
            <button type="button" class="close-modal">&times;</button>
        </div>
        <div class="modal-body">
            <form id="serviceForm" action="{{ route('services.store') }}" method="POST">
                @csrf
                <input type="hidden" id="serviceId" name="service_id">
                <div class="form-group">
                    <label for="name" class="form-label">Nom du service</label>
                    <input type="text" id="serviceName" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="serviceDescription" name="description" class="form-control" rows="3"></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-admin btn-secondary close-modal-btn">Annuler</button>
            <button type="button" id="saveServiceBtn" class="btn-admin btn-primary">Enregistrer</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestion des onglets
        const tabs = document.querySelectorAll('.nav-link');
        tabs.forEach(function(tab) {
            tab.addEventListener('click', function(event) {
                event.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                
                // Désactiver tous les onglets et contenu
                tabs.forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
                
                // Activer l'onglet cliqué et son contenu
                this.classList.add('active');
                target.classList.add('active');
            });
        });
        
        // Gestion des modals
        const modals = document.querySelectorAll('.modal');
        const closeButtons = document.querySelectorAll('.close-modal, .close-modal-btn');
        
        // Fonction pour ouvrir une modal
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
        
        // Fonction pour fermer une modal
        function closeModal() {
            modals.forEach(modal => {
                modal.classList.remove('show');
            });
            document.body.style.overflow = '';
        }
        
        // Fermer les modals
        closeButtons.forEach(button => {
            button.addEventListener('click', closeModal);
        });
        
        // Fermer la modal si on clique en dehors
        modals.forEach(modal => {
            modal.addEventListener('click', function(event) {
                if (event.target === this) {
                    closeModal();
                }
            });
        });
        
        // Bouton "Nouvel utilisateur"
        document.getElementById('addUserBtn').addEventListener('click', function() {
            document.getElementById('userModalTitle').textContent = 'Ajouter un utilisateur';
            document.getElementById('userForm').reset();
            document.getElementById('userForm').setAttribute('action', '{{ route("utilisateurs.store") }}');
            document.getElementById('userForm').setAttribute('method', 'POST');
            openModal('userModal');
        });
        
        // Bouton "Enregistrer" utilisateur
        document.getElementById('saveUserBtn').addEventListener('click', function() {
            document.getElementById('userForm').submit();
        });
        
        // Boutons "Modifier" utilisateur
        document.querySelectorAll('.edit-user').forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-id');
                const userNom = this.getAttribute('data-name');
                const userPrenom = this.getAttribute('data-firstname');
                const userEmail = this.getAttribute('data-email');
                const userRole = this.getAttribute('data-role');
                const userService = this.getAttribute('data-service');
                
                document.getElementById('userModalTitle').textContent = 'Modifier un utilisateur';
                document.getElementById('userId').value = userId;
                document.getElementById('nom').value = userNom;
                document.getElementById('prenom').value = userPrenom;
                document.getElementById('email').value = userEmail;
                document.getElementById('role_id').value = userRole;
                document.getElementById('service_id').value = userService;
                
                document.getElementById('userForm').setAttribute('action', `{{ url('utilisateurs') }}/${userId}`);
                document.getElementById('userForm').setAttribute('method', 'POST');
                
                // Ajouter method PUT
                let methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'PUT';
                document.getElementById('userForm').appendChild(methodInput);
                
                openModal('userModal');
            });
        });
        
        // Bouton "Nouveau service"
        document.getElementById('addServiceBtn').addEventListener('click', function() {
            document.getElementById('serviceModalTitle').textContent = 'Ajouter un service';
            document.getElementById('serviceForm').reset();
            document.getElementById('serviceForm').setAttribute('action', '{{ route("services.store") }}');
            document.getElementById('serviceForm').setAttribute('method', 'POST');
            openModal('serviceModal');
        });
        
        // Bouton "Enregistrer" service
        document.getElementById('saveServiceBtn').addEventListener('click', function() {
            document.getElementById('serviceForm').submit();
        });
        
        // Boutons "Modifier" service
        document.querySelectorAll('.edit-service').forEach(button => {
            button.addEventListener('click', function() {
                const serviceId = this.getAttribute('data-id');
                const serviceName = this.getAttribute('data-name');
                const serviceDescription = this.getAttribute('data-description');
                
                document.getElementById('serviceModalTitle').textContent = 'Modifier un service';
                document.getElementById('serviceId').value = serviceId;
                document.getElementById('serviceName').value = serviceName;
                document.getElementById('serviceDescription').value = serviceDescription;
                
                document.getElementById('serviceForm').setAttribute('action', `{{ url('services') }}/${serviceId}`);
                document.getElementById('serviceForm').setAttribute('method', 'POST');
                
                // Ajouter method PUT
                let methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'PUT';
                document.getElementById('serviceForm').appendChild(methodInput);
                
                openModal('serviceModal');
            });
        });
        
        // Recherche d'utilisateurs
        document.getElementById('searchUser').addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            filterUsers(searchValue, null, null);
        });
        
        // Filtre par rôle
        document.getElementById('filterRole').addEventListener('change', function() {
            const roleValue = this.value;
            const searchValue = document.getElementById('searchUser').value.toLowerCase();
            const serviceValue = document.getElementById('filterService').value;
            filterUsers(searchValue, roleValue, serviceValue);
        });
        
        // Filtre par service
        document.getElementById('filterService').addEventListener('change', function() {
            const serviceValue = this.value;
            const searchValue = document.getElementById('searchUser').value.toLowerCase();
            const roleValue = document.getElementById('filterRole').value;
            filterUsers(searchValue, roleValue, serviceValue);
        });
        
        // Fonction de filtrage des utilisateurs
        function filterUsers(searchValue, roleValue, serviceValue) {
            const rows = document.querySelectorAll('#usersTable tbody tr');
            
            rows.forEach(row => {
                const name = row.cells[0].textContent.toLowerCase();
                const email = row.cells[1].textContent.toLowerCase();
                const role = row.cells[2].textContent;
                const service = row.cells[3].textContent;
                const roleId = row.querySelector('.edit-user').getAttribute('data-role');
                const serviceId = row.querySelector('.edit-user').getAttribute('data-service');
                
                const matchSearch = !searchValue || name.includes(searchValue) || email.includes(searchValue);
                const matchRole = !roleValue || roleId === roleValue;
                const matchService = !serviceValue || serviceId === serviceValue;
                
                if (matchSearch && matchRole && matchService) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
        
        // Recherche de services
        document.getElementById('searchService').addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const rows = document.querySelectorAll('#servicesTable tbody tr');
            
            rows.forEach(row => {
                const name = row.cells[0].textContent.toLowerCase();
                const description = row.cells[1].textContent.toLowerCase();
                
                if (name.includes(searchValue) || description.includes(searchValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
        
        // Gestion des options de maintenance
        document.getElementById('maintenanceMode').addEventListener('change', function() {
            const maintenanceOptions = document.getElementById('maintenanceOptions');
            if (this.checked) {
                maintenanceOptions.style.display = 'block';
            } else {
                maintenanceOptions.style.display = 'none';
            }
        });
        
        // Formulaire paramètres système
        document.getElementById('systemSettingsForm').addEventListener('submit', function(event) {
            event.preventDefault();
            // Simulation d'enregistrement
            alert('Paramètres système enregistrés avec succès.');
        });
    });
</script>
@endsection