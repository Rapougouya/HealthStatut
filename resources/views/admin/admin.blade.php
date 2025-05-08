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
        <button class="tab-btn" data-tab="departments">
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
                <div class="table-container">
                    <table class="admin-table users-table">
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
                            @foreach($users ?? [] as $user)
                            <tr>
                                <td>
                                    <div class="user-info-cell">
                                        <div class="user-avatar">
                                            <img src="{{ $user->avatar ?? asset('lovable-uploads/3bec0184-d9fb-47a0-b398-880496412872.png') }}" alt="User">
                                        </div>
                                        <div class="user-details">
                                            <div class="user-name">{{ $user->name ?? 'Dr. Ahmed Benali' }}</div>
                                            <div class="user-id">ID: {{ $user->id_number ?? 'MED-001' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->email ?? 'ahmed.benali@example.com' }}</td>
                                <td><span class="badge role-badge {{ $user->role_class ?? 'admin' }}">{{ $user->role_name ?? 'Administrateur' }}</span></td>
                                <td>{{ $user->department ?? 'Cardiologie' }}</td>
                                <td><span class="badge status-badge {{ $user->status ?? 'active' }}">{{ $user->status_label ?? 'Actif' }}</span></td>
                                <td>{{ $user->last_login ?? 'Aujourd\'hui à 09:45' }}</td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.users.edit', $user->id ?? 1) }}" class="icon-btn" title="Éditer"><i class="fas fa-edit"></i></a>
                                        <button class="icon-btn" data-id="{{ $user->id ?? 1 }}" data-action="permissions" title="Permissions"><i class="fas fa-key"></i></button>
                                        <button class="icon-btn" data-id="{{ $user->id ?? 1 }}" data-action="reset-password" title="Réinitialiser mot de passe"><i class="fas fa-lock"></i></button>
                                        @if($user->status ?? 'active' == 'active')
                                        <button class="icon-btn delete" data-id="{{ $user->id ?? 1 }}" data-action="deactivate" title="Désactiver"><i class="fas fa-user-slash"></i></button>
                                        @else
                                        <button class="icon-btn activate" data-id="{{ $user->id ?? 1 }}" data-action="activate" title="Activer"><i class="fas fa-user-check"></i></button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            @if(empty($users))
                                <tr>
                                    <td>
                                        <div class="user-info-cell">
                                            <div class="user-avatar">
                                                <img src="{{ asset('lovable-uploads/3bec0184-d9fb-47a0-b398-880496412872.png') }}" alt="User">
                                            </div>
                                            <div class="user-details">
                                                <div class="user-name">Dr. Ahmed Benali</div>
                                                <div class="user-id">ID: MED-001</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>ahmed.benali@example.com</td>
                                    <td><span class="badge role-badge admin">Administrateur</span></td>
                                    <td>Cardiologie</td>
                                    <td><span class="badge status-badge active">Actif</span></td>
                                    <td>Aujourd'hui à 09:45</td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="icon-btn" title="Éditer"><i class="fas fa-edit"></i></button>
                                            <button class="icon-btn" title="Permissions"><i class="fas fa-key"></i></button>
                                            <button class="icon-btn" title="Réinitialiser mot de passe"><i class="fas fa-lock"></i></button>
                                            <button class="icon-btn delete" title="Désactiver"><i class="fas fa-user-slash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Exemple d'utilisateurs supplémentaires -->
                                <tr>
                                    <td>
                                        <div class="user-info-cell">
                                            <div class="user-avatar">
                                                <img src="{{ asset('lovable-uploads/3bec0184-d9fb-47a0-b398-880496412872.png') }}" alt="User">
                                            </div>
                                            <div class="user-details">
                                                <div class="user-name">Dr. Sophie Martin</div>
                                                <div class="user-id">ID: MED-002</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>sophie.martin@example.com</td>
                                    <td><span class="badge role-badge doctor">Médecin</span></td>
                                    <td>Pédiatrie</td>
                                    <td><span class="badge status-badge active">Actif</span></td>
                                    <td>Hier à 16:23</td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="icon-btn" title="Éditer"><i class="fas fa-edit"></i></button>
                                            <button class="icon-btn" title="Permissions"><i class="fas fa-key"></i></button>
                                            <button class="icon-btn" title="Réinitialiser mot de passe"><i class="fas fa-lock"></i></button>
                                            <button class="icon-btn delete" title="Désactiver"><i class="fas fa-user-slash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Plus d'exemples d'utilisateurs -->
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="pagination">
                    <button class="pagination-btn" data-action="prev"><i class="fas fa-chevron-left"></i></button>
                    <span class="page-info">Page {{ $users_page ?? 1 }} sur {{ $users_total_pages ?? 3 }}</span>
                    <button class="pagination-btn" data-action="next"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Roles Tab Content -->
    <div class="tab-content" id="roles-tab">
        <div class="admin-card">
            <div class="card-header">
                <h2><i class="fas fa-user-tag"></i> Rôles et permissions</h2>
                <button class="action-btn primary" id="add-role-btn">
                    <i class="fas fa-plus"></i> Nouveau rôle
                </button>
            </div>
            <div class="card-content">
                <div class="roles-container">
                    <div class="roles-list">
                        @foreach($roles ?? [] as $index => $role)
                        <div class="role-item {{ $index == 0 ? 'active' : '' }}" data-role-id="{{ $role->id ?? $index+1 }}">
                            <div class="role-name">
                                <i class="{{ $role->icon ?? 'fas fa-user-shield' }}"></i>
                                <span>{{ $role->name ?? 'Rôle par défaut' }}</span>
                            </div>
                            <div class="role-user-count">
                                <span>{{ $role->users_count ?? '0' }} utilisateurs</span>
                            </div>
                        </div>
                        @endforeach
                        @if(empty($roles))
                            <div class="role-item active" data-role-id="1">
                                <div class="role-name">
                                    <i class="fas fa-user-shield"></i>
                                    <span>Administrateur</span>
                                </div>
                                <div class="role-user-count">
                                    <span>3 utilisateurs</span>
                                </div>
                            </div>
                            <div class="role-item" data-role-id="2">
                                <div class="role-name">
                                    <i class="fas fa-user-md"></i>
                                    <span>Médecin</span>
                                </div>
                                <div class="role-user-count">
                                    <span>12 utilisateurs</span>
                                </div>
                            </div>
                            <!-- Exemple de rôles supplémentaires -->
                            <div class="role-item" data-role-id="3">
                                <div class="role-name">
                                    <i class="fas fa-user-nurse"></i>
                                    <span>Infirmier/Infirmière</span>
                                </div>
                                <div class="role-user-count">
                                    <span>24 utilisateurs</span>
                                </div>
                            </div>
                            <!-- Plus d'exemples de rôles -->
                        @endif
                    </div>
                    <div class="permissions-section">
                        <div class="permissions-header">
                            <h3>Permissions pour: <span id="selected-role-name">{{ $roles[0]->name ?? 'Administrateur' }}</span></h3>
                            <button class="action-btn" id="edit-permissions-btn">
                                <i class="fas fa-edit"></i> Modifier les permissions
                            </button>
                        </div>
                        <div class="permissions-grid">
                            @foreach($permission_categories ?? [] as $category)
                            <div class="permission-category">
                                <h4>{{ $category->name ?? 'Catégorie' }}</h4>
                                <div class="permission-items">
                                    @foreach($category->permissions ?? [] as $permission)
                                    <div class="permission-item">
                                        <span class="permission-label">{{ $permission->name ?? 'Permission' }}</span>
                                        <span class="permission-access {{ $permission->level ?? 'full' }}">{{ $permission->level_label ?? 'Total' }}</span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                            @if(empty($permission_categories))
                                <div class="permission-category">
                                    <h4>Administration</h4>
                                    <div class="permission-items">
                                        <div class="permission-item">
                                            <span class="permission-label">Gestion des utilisateurs</span>
                                            <span class="permission-access full">Total</span>
                                        </div>
                                        <div class="permission-item">
                                            <span class="permission-label">Gestion des rôles</span>
                                            <span class="permission-access full">Total</span>
                                        </div>
                                        <div class="permission-item">
                                            <span class="permission-label">Configuration système</span>
                                            <span class="permission-access full">Total</span>
                                        </div>
                                    </div>
                                </div>
                                <!-- Catégories de permissions supplémentaires -->
                                <div class="permission-category">
                                    <h4>Patients</h4>
                                    <div class="permission-items">
                                        <div class="permission-item">
                                            <span class="permission-label">Ajouter/Modifier patients</span>
                                            <span class="permission-access full">Total</span>
                                        </div>
                                        <div class="permission-item">
                                            <span class="permission-label">Voir dossiers patients</span>
                                            <span class="permission-access full">Total</span>
                                        </div>
                                        <div class="permission-item">
                                            <span class="permission-label">Supprimer patients</span>
                                            <span class="permission-access full">Total</span>
                                        </div>
                                    </div>
                                </div>
                                <!-- Plus de catégories de permissions -->
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Departments Tab Content -->
    <div class="tab-content" id="departments-tab">
        <div class="admin-card">
            <div class="card-header">
                <h2><i class="fas fa-hospital"></i> Gestion des services</h2>
                <button class="action-btn primary" id="add-department-btn">
                    <i class="fas fa-plus"></i> Ajouter un service
                </button>
            </div>
            <div class="card-content">
                <div class="departments-grid">
                    @foreach($departments ?? [] as $department)
                    <div class="department-card" data-id="{{ $department->id ?? 1 }}">
                        <div class="department-card-header">
                            <h3>{{ $department->name ?? 'Service' }}</h3>
                            <div class="department-actions">
                                <button class="icon-btn" data-id="{{ $department->id ?? 1 }}" data-action="edit-department" title="Éditer"><i class="fas fa-edit"></i></button>
                                <button class="icon-btn delete" data-id="{{ $department->id ?? 1 }}" data-action="delete-department" title="Supprimer"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                        <div class="department-info">
                            <div class="info-item">
                                <i class="fas fa-users"></i>
                                <span>{{ $department->users_count ?? '0' }} utilisateurs</span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-user-injured"></i>
                                <span>{{ $department->patients_count ?? '0' }} patients</span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-heartbeat"></i>
                                <span>{{ $department->sensors_count ?? '0' }} capteurs</span>
                            </div>
                        </div>
                        <div class="department-footer">
                            <a href="{{ route('admin.departments.manage', $department->id ?? 1) }}" class="action-btn full-width">
                                <i class="fas fa-cog"></i> Gérer
                            </a>
                        </div>
                    </div>
                    @endforeach
                    @if(empty($departments))
                        <div class="department-card" data-id="1">
                            <div class="department-card-header">
                                <h3>Cardiologie</h3>
                                <div class="department-actions">
                                    <button class="icon-btn" title="Éditer"><i class="fas fa-edit"></i></button>
                                    <button class="icon-btn delete" title="Supprimer"><i class="fas fa-trash"></i></button>
                                </div>
                            </div>
                            <div class="department-info">
                                <div class="info-item">
                                    <i class="fas fa-users"></i>
                                    <span>15 utilisateurs</span>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-user-injured"></i>
                                    <span>42 patients</span>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-heartbeat"></i>
                                    <span>28 capteurs</span>
                                </div>
                            </div>
                            <div class="department-footer">
                                <button class="action-btn full-width">
                                    <i class="fas fa-cog"></i> Gérer
                                </button>
                            </div>
                        </div>
                        <!-- Exemple de services supplémentaires -->
                        <div class="department-card" data-id="2">
                            <div class="department-card-header">
                                <h3>Pédiatrie</h3>
                                <div class="department-actions">
                                    <button class="icon-btn" title="Éditer"><i class="fas fa-edit"></i></button>
                                    <button class="icon-btn delete" title="Supprimer"><i class="fas fa-trash"></i></button>
                                </div>
                            </div>
                            <div class="department-info">
                                <div class="info-item">
                                    <i class="fas fa-users"></i>
                                    <span>12 utilisateurs</span>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-user-injured"></i>
                                    <span>36 patients</span>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-heartbeat"></i>
                                    <span>20 capteurs</span>
                                </div>
                            </div>
                            <div class="department-footer">
                                <button class="action-btn full-width">
                                    <i class="fas fa-cog"></i> Gérer
                                </button>
                            </div>
                        </div>
                        <!-- Plus d'exemples de services -->
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Logs Tab Content -->
    <div class="tab-content" id="logs-tab">
        <div class="admin-card">
            <div class="card-header">
                <h2><i class="fas fa-clipboard-list"></i> Journaux d'activité</h2>
                <div class="header-actions">
                    <div class="logs-filter">
                        <select id="log-type-filter">
                            <option value="all">Tous les types</option>
                            <option value="login">Connexions</option>
                            <option value="patient">Patients</option>
                            <option value="alert">Alertes</option>
                            <option value="config">Configuration</option>
                        </select>
                        <select id="log-user-filter">
                            <option value="all">Tous les utilisateurs</option>
                            @foreach($users ?? [] as $user)
                            <option value="{{ $user->id ?? 1 }}">{{ $user->name ?? 'Utilisateur' }}</option>
                            @endforeach
                            @if(empty($users))
                            <option value="1">Dr. Ahmed Benali</option>
                            <option value="2">Dr. Sophie Martin</option>
                            <option value="3">Isabelle Dubois</option>
                            @endif
                        </select>
                        <button class="action-btn" id="filter-logs-btn">
                            <i class="fas fa-filter"></i> Filtrer
                        </button>
                    </div>
                    <button class="action-btn" id="export-logs-btn">
                        <i class="fas fa-download"></i> Exporter
                    </button>
                </div>
            </div>
            <div class="card-content">
                <div class="table-container">
                    <table class="admin-table logs-table">
                        <thead>
                            <tr>
                                <th>Horodatage</th>
                                <th>Utilisateur</th>
                                <th>Type</th>
                                <th>Action</th>
                                <th>Détails</th>
                                <th>Adresse IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs ?? [] as $log)
                            <tr>
                                <td>{{ $log->timestamp ?? now()->format('d/m/Y H:i:s') }}</td>
                                <td>{{ $log->user_name ?? 'Utilisateur' }}</td>
                                <td><span class="badge log-badge {{ $log->type ?? 'login' }}">{{ $log->type_label ?? 'Action' }}</span></td>
                                <td>{{ $log->action ?? 'Action effectuée' }}</td>
                                <td>{{ $log->details ?? 'Détails de l\'action' }}</td>
                                <td>{{ $log->ip_address ?? '127.0.0.1' }}</td>
                            </tr>
                            @endforeach
                            @if(empty($logs))
                                <tr>
                                    <td>24/04/2023 09:45:12</td>
                                    <td>Dr. Ahmed Benali</td>
                                    <td><span class="badge log-badge login">Connexion</span></td>
                                    <td>Connexion réussie</td>
                                    <td>Navigateur: Chrome, OS: Windows</td>
                                    <td>192.168.1.105</td>
                                </tr>
                                <!-- Exemples de logs supplémentaires -->
                                <tr>
                                    <td>24/04/2023 09:48:32</td>
                                    <td>Dr. Ahmed Benali</td>
                                    <td><span class="badge log-badge patient">Patient</span></td>
                                    <td>Dossier consulté</td>
                                    <td>ID Patient: P-1234, Jean Dupont</td>
                                    <td>192.168.1.105</td>
                                </tr>
                                <!-- Plus d'exemples de logs -->
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="pagination">
                    <button class="pagination-btn" data-action="prev"><i class="fas fa-chevron-left"></i></button>
                    <span class="page-info">Page {{ $logs_page ?? 1 }} sur {{ $logs_total_pages ?? 15 }}</span>
                    <button class="pagination-btn" data-action="next"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals for Add/Edit User, Role, Department, etc. -->
<div class="modal" id="userModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Ajouter un utilisateur</h3>
            <button class="close-modal">&times;</button>
        </div>
        <div class="modal-body">
            <form id="userForm" method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <div class="form-grid">
                    <div class="form-group">
                        <label for="user-first-name">Prénom</label>
                        <input type="text" id="user-first-name" name="first_name" placeholder="Prénom" required>
                    </div>
                    <div class="form-group">
                        <label for="user-last-name">Nom</label>
                        <input type="text" id="user-last-name" name="last_name" placeholder="Nom" required>
                    </div>
                    <div class="form-group">
                        <label for="user-email">Email</label>
                        <input type="email" id="user-email" name="email" placeholder="exemple@email.com" required>
                    </div>
                    <div class="form-group">
                        <label for="user-role">Rôle</label>
                        <select id="user-role" name="role_id" required>
                            <option value="">Sélectionner un rôle</option>
                            @foreach($roles ?? [] as $role)
                            <option value="{{ $role->id ?? '' }}">{{ $role->name ?? '' }}</option>
                            @endforeach
                            @if(empty($roles))
                            <option value="admin">Administrateur</option>
                            <option value="doctor">Médecin</option>
                            <option value="nurse">Infirmier/Infirmière</option>
                            <option value="technician">Technicien</option>
                            <option value="secretary">Secrétaire médical</option>
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="user-department">Service</label>
                        <select id="user-department" name="department_id" required>
                            <option value="">Sélectionner un service</option>
                            @foreach($departments ?? [] as $department)
                            <option value="{{ $department->id ?? '' }}">{{ $department->name ?? '' }}</option>
                            @endforeach
                            @if(empty($departments))
                            <option value="cardio">Cardiologie</option>
                            <option value="pediatrics">Pédiatrie</option>
                            <option value="neurology">Neurologie</option>
                            <option value="emergency">Urgences</option>
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="user-status">Statut</label>
                        <select id="user-status" name="status">
                            <option value="active">Actif</option>
                            <option value="inactive">Inactif</option>
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

<!-- Autres modals (Rôle, Service, etc.) selon les besoins -->
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
                
                // Désactiver tous les onglets
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));
                
                // Activer l'onglet sélectionné
                this.classList.add('active');
                document.getElementById(`${tabId}-tab`).classList.add('active');
            });
        });
        
        // Gestion du modal utilisateur
        const addUserBtn = document.getElementById('add-user-btn');
        const userModal = document.getElementById('userModal');
        const closeModal = userModal.querySelector('.close-modal');
        const cancelBtn = userModal.querySelector('.cancel-btn');
        const saveUserBtn = document.getElementById('save-user-btn');
        
        function openUserModal() {
            userModal.style.display = 'block';
            document.getElementById('userForm').reset();
            document.getElementById('user-id').value = '';
            userModal.querySelector('.modal-header h3').textContent = 'Ajouter un utilisateur';
            saveUserBtn.textContent = 'Ajouter';
        }
        
        function closeUserModal() {
            userModal.style.display = 'none';
        }
        
        if (addUserBtn) {
            addUserBtn.addEventListener('click', openUserModal);
        }
        
        if (closeModal) {
            closeModal.addEventListener('click', closeUserModal);
        }
        
        if (cancelBtn) {
            cancelBtn.addEventListener('click', closeUserModal);
        }
        
        if (saveUserBtn) {
            saveUserBtn.addEventListener('click', function() {
                document.getElementById('userForm').submit();
            });
        }
        
        // Édition d'utilisateur
        const editUserBtns = document.querySelectorAll('.action-buttons .icon-btn[title="Éditer"]');
        editUserBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const userId = this.closest('tr').querySelector('.user-info-cell .user-id').textContent.replace('ID: ', '');
                const userName = this.closest('tr').querySelector('.user-info-cell .user-name').textContent;
                
                // Dans une vraie application, vous feriez un appel AJAX pour obtenir les données de l'utilisateur
                // Pour cet exemple, nous allons juste ouvrir le modal avec quelques données préremplies
                userModal.querySelector('.modal-header h3').textContent = `Modifier l'utilisateur: ${userName}`;
                saveUserBtn.textContent = 'Enregistrer';
                document.getElementById('user-id').value = userId;
                
                // Afficher le modal
                userModal.style.display = 'block';
            });
        });
        
        // Gestion des rôles
        const roleItems = document.querySelectorAll('.role-item');
        roleItems.forEach(item => {
            item.addEventListener('click', function() {
                roleItems.forEach(r => r.classList.remove('active'));
                this.classList.add('active');
                
                const roleName = this.querySelector('.role-name span').textContent;
                document.getElementById('selected-role-name').textContent = roleName;
                
                // Dans une vraie application, vous feriez un appel AJAX pour charger les permissions du rôle
            });
        });
        
        // Prévenir les actions de suppression sans confirmation
        const deleteButtons = document.querySelectorAll('.icon-btn.delete');
        deleteButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                if (!confirm('Êtes-vous sûr de vouloir effectuer cette action ?')) {
                    e.preventDefault();
                }
            });
        });
    });
</script>
@endsection
