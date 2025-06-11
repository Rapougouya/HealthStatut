@extends('layouts.app')

@section('title', 'Administration - Système de Monitoring Médical')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endsection

@section('content')
<div class="admin-container">
    <!-- Tabs Navigation -->
    <div class="admin-tabs">
        <button class="tab-btn active" data-tab="users">
            <i class="fas fa-users"></i> Utilisateurs
        </button>
        <button class="tab-btn" data-tab="roles">
            <i class="fas fa-user-tag"></i> Rôles et Permissions
        </button>
        <button class="tab-btn" data-tab="services">
            <i class="fas fa-hospital"></i> Services
        </button>
        <button class="tab-btn" data-tab="logs">
            <i class="fas fa-clipboard-list"></i> Journaux d'activité
        </button>
    </div>

    <!-- Users Tab Content -->
    <div class="tab-content active" id="users-tab">
        <div class="admin-card">
            <div class="card-header">
                <h2><i class="fas fa-users"></i> Gestion des utilisateurs</h2>
                <div class="header-actions">
                    <div class="search-box">
                        <input type="text" placeholder="Rechercher un utilisateur..." id="user-search">
                        <i class="fas fa-search"></i>
                    </div>
                    <button class="action-btn primary" id="add-user-btn">
                        <i class="fas fa-user-plus"></i> Ajouter un utilisateur
                    </button>
                </div>
            </div>
            <div class="card-content">
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ session('error') }}
                    </div>
                @endif

                <div class="table-container">
                    <table class="admin-table users-table">
                        <thead>
                            <tr>
                                <th>Utilisateur</th>
                                <th>Email</th>
                                <th>Rôle</th>
                                <th>Service</th>
                                <th>Statut</th>
                                <th>Dernière connexion</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr>
                                <td>
                                    <div class="user-info-cell">
                                        <div class="user-avatar">
                                            @if($user->avatar)
                                                <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->nom_complet }}">
                                            @else
                                                <div class="avatar-placeholder">
                                                    {{ strtoupper(substr($user->prenom, 0, 1) . substr($user->nom, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="user-details">
                                            <div class="user-name">{{ $user->nom_complet }}</div>
                                            <div class="user-id">ID: {{ $user->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="badge role-badge role-{{ $user->role->nom ?? 'unknown' }}">
                                        {{ ucfirst($user->role->nom ?? 'Non défini') }}
                                    </span>
                                </td>
                                <td>{{ $user->service->name ?? 'Non assigné' }}</td>
                                <td>
                                    <span class="badge status-badge status-{{ $user->statut }}">
                                        {{ ucfirst($user->statut) }}
                                    </span>
                                </td>
                                <td>
                                    @if($user->derniere_connexion)
                                        {{ $user->derniere_connexion->format('d/m/Y H:i') }}
                                    @else
                                        <span class="text-muted">Jamais connecté</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="icon-btn edit-user" 
                                                data-id="{{ $user->id }}" 
                                                data-nom="{{ $user->nom }}"
                                                data-prenom="{{ $user->prenom }}"
                                                data-email="{{ $user->email }}"
                                                data-role="{{ $user->role_id }}"
                                                data-service="{{ $user->service_id }}"
                                                title="Éditer">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        
                                        <form method="POST" action="{{ route('utilisateurs.reset-password', $user) }}" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="icon-btn reset-password" 
                                                    title="Réinitialiser mot de passe"
                                                    onclick="return confirm('Réinitialiser le mot de passe de {{ $user->nom_complet }} ?')">
                                                <i class="fas fa-lock"></i>
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('utilisateurs.toggle-status', $user) }}" style="display: inline;">
                                            @csrf
                                            @if($user->statut === 'actif')
                                                <button type="submit" class="icon-btn deactivate" 
                                                        title="Désactiver"
                                                        onclick="return confirm('Désactiver {{ $user->nom_complet }} ?')">
                                                    <i class="fas fa-user-slash"></i>
                                                </button>
                                            @else
                                                <button type="submit" class="icon-btn activate" 
                                                        title="Activer"
                                                        onclick="return confirm('Activer {{ $user->nom_complet }} ?')">
                                                    <i class="fas fa-user-check"></i>
                                                </button>
                                            @endif
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">
                                    <div class="empty-state">
                                        <i class="fas fa-users"></i>
                                        <h3>Aucun utilisateur trouvé</h3>
                                        <p>Commencez par ajouter votre premier utilisateur</p>
                                        <button class="action-btn primary" onclick="document.getElementById('add-user-btn').click()">
                                            <i class="fas fa-user-plus"></i> Ajouter un utilisateur
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Roles Tab Content -->
    <div class="tab-content" id="roles-tab">
        <div class="admin-card">
            <div class="card-header">
                <h2><i class="fas fa-user-tag"></i> Rôles et permissions</h2>
            </div>
            <div class="card-content">
                <div class="roles-grid">
                    @forelse($roles as $role)
                    <div class="role-card">
                        <div class="role-header">
                            <h3>{{ ucfirst($role->nom) }}</h3>
                            <span class="role-count">{{ $role->users->count() }} utilisateurs</span>
                        </div>
                        <div class="role-description">
                            {{ $role->description }}
                        </div>
                        <div class="role-users">
                            @foreach($role->users->take(3) as $user)
                                <span class="user-pill">{{ $user->nom_complet }}</span>
                            @endforeach
                            @if($role->users->count() > 3)
                                <span class="user-pill more">+{{ $role->users->count() - 3 }} autres</span>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="empty-state">
                        <i class="fas fa-user-tag"></i>
                        <h3>Aucun rôle configuré</h3>
                        <p>Les rôles définissent les permissions des utilisateurs</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Services Tab Content -->
    <div class="tab-content" id="services-tab">
        <div class="admin-card">
            <div class="card-header">
                <h2><i class="fas fa-hospital"></i> Gestion des services</h2>
                <button class="action-btn primary" id="add-service-btn">
                    <i class="fas fa-plus"></i> Ajouter un service
                </button>
            </div>
            <div class="card-content">
                <div class="services-grid">
                    @forelse($services as $service)
                    <div class="service-card">
                        <div class="service-header">
                            <h3>{{ $service->name }}</h3>
                            <div class="service-actions">
                                <button class="icon-btn edit-service" 
                                        data-id="{{ $service->id }}"
                                        data-name="{{ $service->name }}"
                                        data-description="{{ $service->description }}"
                                        title="Éditer">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        </div>
                        <div class="service-description">
                            {{ $service->description ?? 'Aucune description' }}
                        </div>
                        <div class="service-stats">
                            <div class="stat-item">
                                <i class="fas fa-users"></i>
                                <span>{{ $service->users->count() }} utilisateurs</span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="empty-state">
                        <i class="fas fa-hospital"></i>
                        <h3>Aucun service configuré</h3>
                        <p>Les services organisent les utilisateurs par département</p>
                        <button class="action-btn primary" onclick="document.getElementById('add-service-btn').click()">
                            <i class="fas fa-plus"></i> Ajouter un service
                        </button>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Logs Tab Content -->
    <div class="tab-content" id="logs-tab">
        <div class="admin-card">
            <div class="card-header">
                <h2><i class="fas fa-clipboard-list"></i> Journaux d'activité</h2>
            </div>
            <div class="card-content">
                <div class="logs-placeholder">
                    <i class="fas fa-clipboard-list"></i>
                    <h3>Journaux d'activité</h3>
                    <p>Cette fonctionnalité sera disponible prochainement</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour ajouter/modifier un utilisateur -->
<div class="modal" id="userModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modal-title">Ajouter un utilisateur</h3>
            <button class="close-modal">&times;</button>
        </div>
        <div class="modal-body">
            <form id="userForm" method="POST">
                @csrf
                <div id="method-field"></div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="user-nom">Nom</label>
                        <input type="text" id="user-nom" name="nom" placeholder="Nom" required>
                    </div>
                    <div class="form-group">
                        <label for="user-prenom">Prénom</label>
                        <input type="text" id="user-prenom" name="prenom" placeholder="Prénom" required>
                    </div>
                    <div class="form-group">
                        <label for="user-email">Email</label>
                        <input type="email" id="user-email" name="email" placeholder="exemple@email.com" required>
                    </div>
                    <div class="form-group">
                        <label for="user-role">Rôle</label>
                        <select id="user-role" name="role_id" required>
                            <option value="">Sélectionner un rôle</option>
                            @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ ucfirst($role->nom) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="user-service">Service</label>
                        <select id="user-service" name="service_id" required>
                            <option value="">Sélectionner un service</option>
                            @foreach($services as $service)
                            <option value="{{ $service->id }}">{{ $service->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <input type="hidden" name="user_id" id="user-id" value="">
            </form>
        </div>
        <div class="modal-footer">
            <button class="cancel-btn" data-dismiss="modal">Annuler</button>
            <button class="action-btn primary" id="save-user-btn">Ajouter</button>
        </div>
    </div>
</div>

<!-- Modal pour ajouter/modifier un service -->
<div class="modal" id="serviceModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="service-modal-title">Ajouter un service</h3>
            <button class="close-modal">&times;</button>
        </div>
        <div class="modal-body">
            <form id="serviceForm" method="POST">
                @csrf
                <div id="service-method-field"></div>
                
                <div class="form-group">
                    <label for="service-name">Nom du service</label>
                    <input type="text" id="service-name" name="name" placeholder="Ex: Cardiologie" required>
                </div>
                <div class="form-group">
                    <label for="service-description">Description</label>
                    <textarea id="service-description" name="description" placeholder="Description du service" rows="3"></textarea>
                </div>
                
                <input type="hidden" name="service_id" id="service-id" value="">
            </form>
        </div>
        <div class="modal-footer">
            <button class="cancel-btn" data-dismiss="modal">Annuler</button>
            <button class="action-btn primary" id="save-service-btn">Ajouter</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion des onglets
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            this.classList.add('active');
            document.getElementById(`${tabId}-tab`).classList.add('active');
        });
    });
    
    // Gestion du modal utilisateur
    const userModal = document.getElementById('userModal');
    const addUserBtn = document.getElementById('add-user-btn');
    const saveUserBtn = document.getElementById('save-user-btn');
    
    // Ouvrir modal pour ajouter un utilisateur
    addUserBtn?.addEventListener('click', function() {
        document.getElementById('modal-title').textContent = 'Ajouter un utilisateur';
        document.getElementById('userForm').action = "{{ route('utilisateurs.store') }}";
        document.getElementById('method-field').innerHTML = '';
        document.getElementById('userForm').reset();
        document.getElementById('user-id').value = '';
        saveUserBtn.textContent = 'Ajouter';
        userModal.style.display = 'block';
    });
    
    // Ouvrir modal pour modifier un utilisateur
    document.querySelectorAll('.edit-user').forEach(btn => {
        btn.addEventListener('click', function() {
            const userId = this.dataset.id;
            document.getElementById('modal-title').textContent = 'Modifier l\'utilisateur';
            document.getElementById('userForm').action = `/admin/utilisateurs/${userId}`;
            document.getElementById('method-field').innerHTML = '@method("PUT")';
            
            // Pré-remplir le formulaire
            document.getElementById('user-nom').value = this.dataset.nom;
            document.getElementById('user-prenom').value = this.dataset.prenom;
            document.getElementById('user-email').value = this.dataset.email;
            document.getElementById('user-role').value = this.dataset.role;
            document.getElementById('user-service').value = this.dataset.service;
            document.getElementById('user-id').value = userId;
            
            saveUserBtn.textContent = 'Modifier';
            userModal.style.display = 'block';
        });
    });
    
    // Gestion du modal service
    const serviceModal = document.getElementById('serviceModal');
    const addServiceBtn = document.getElementById('add-service-btn');
    const saveServiceBtn = document.getElementById('save-service-btn');
    
    // Ouvrir modal pour ajouter un service
    addServiceBtn?.addEventListener('click', function() {
        document.getElementById('service-modal-title').textContent = 'Ajouter un service';
        document.getElementById('serviceForm').action = "{{ route('services.store') }}";
        document.getElementById('service-method-field').innerHTML = '';
        document.getElementById('serviceForm').reset();
        document.getElementById('service-id').value = '';
        saveServiceBtn.textContent = 'Ajouter';
        serviceModal.style.display = 'block';
    });
    
    // Ouvrir modal pour modifier un service
    document.querySelectorAll('.edit-service').forEach(btn => {
        btn.addEventListener('click', function() {
            const serviceId = this.dataset.id;
            document.getElementById('service-modal-title').textContent = 'Modifier le service';
            document.getElementById('serviceForm').action = `/admin/services/${serviceId}`;
            document.getElementById('service-method-field').innerHTML = '@method("PUT")';
            
            document.getElementById('service-name').value = this.dataset.name;
            document.getElementById('service-description').value = this.dataset.description;
            document.getElementById('service-id').value = serviceId;
            
            saveServiceBtn.textContent = 'Modifier';
            serviceModal.style.display = 'block';
        });
    });
    
    // Fermer les modals
    document.querySelectorAll('.close-modal, .cancel-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            userModal.style.display = 'none';
            serviceModal.style.display = 'none';
        });
    });
    
    // Soumettre les formulaires
    saveUserBtn?.addEventListener('click', function() {
        document.getElementById('userForm').submit();
    });
    
    saveServiceBtn?.addEventListener('click', function() {
        document.getElementById('serviceForm').submit();
    });
    
    // Recherche d'utilisateurs
    const userSearch = document.getElementById('user-search');
    userSearch?.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const userRows = document.querySelectorAll('.users-table tbody tr');
        
        userRows.forEach(row => {
            const userName = row.querySelector('.user-name')?.textContent.toLowerCase() || '';
            const userEmail = row.cells[1]?.textContent.toLowerCase() || '';
            
            if (userName.includes(searchTerm) || userEmail.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
    
    // Auto-hide alerts
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        });
    }, 5000);
});
</script>
@endsection