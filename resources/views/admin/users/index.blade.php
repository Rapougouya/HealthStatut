@extends('layouts.app')

@section('title', 'Administration des Utilisateurs')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-modern.css') }}">
    <style>
        .user-header {
            background: linear-gradient(135deg, var(--primary-light), var(--primary));
            color: white;
            padding: 2rem;
            border-radius: var(--radius);
            margin-bottom: 2rem;
        }
        
        .action-btn {
            transition: all 0.2s;
        }
        
        .action-btn:hover {
            transform: scale(1.05);
        }
        
        .status-active {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--success);
            border-left: 3px solid var(--success);
        }
        
        .status-inactive {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--danger);
            border-left: 3px solid var(--danger);
        }
    </style>
@endsection

@section('content')
<div class="admin-container">
    <div class="user-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title"><i class="ri-user-settings-line"></i> Administration des Utilisateurs</h1>
                <p class="text-white-50">Gérez les comptes utilisateurs, les rôles et les permissions</p>
            </div>
            <div>
                <button id="addUserBtn" class="btn btn-light">
                    <i class="ri-user-add-line"></i> Nouvel utilisateur
                </button>
            </div>
        </div>
    </div>
    
    <div class="admin-panel">
        <ul class="nav nav-tabs" id="adminTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="users-tab" data-toggle="tab" href="#users" role="tab">
                    <i class="ri-user-line"></i> Utilisateurs
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="roles-tab" data-toggle="tab" href="#roles" role="tab">
                    <i class="ri-shield-user-line"></i> Rôles
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="permissions-tab" data-toggle="tab" href="#permissions" role="tab">
                    <i class="ri-lock-line"></i> Permissions
                </a>
            </li>
        </ul>
        
        <div class="tab-content fade-transition" id="adminTabContent">
            <div class="tab-content" id="users">
                <div class="card admin-card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="ri-user-search-line"></i> Liste des utilisateurs</h3>
                        <div class="header-actions">
                            <div class="search-box">
                                <i class="ri-search-line search-icon"></i>
                                <input type="text" id="searchUser" class="form-control search-input" placeholder="Rechercher un utilisateur...">
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-container">
                            <table class="admin-table" id="usersTable">
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
                                        <td>
                                            <div class="user-info">
                                                <div class="avatar">
                                                    @if($user->avatar)
                                                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->nom }}">
                                                    @else
                                                        {{ strtoupper(substr($user->prenom, 0, 1) . substr($user->nom, 0, 1)) }}
                                                    @endif
                                                </div>
                                                <div class="user-details">
                                                    <div class="user-name">{{ $user->nom_complet }}</div>
                                                    <div class="user-meta">ID: {{ $user->id }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td><span class="badge badge-primary">{{ $user->role->nom ?? 'N/A' }}</span></td>
                                        <td>{{ $user->service->name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge {{ $user->statut === 'actif' ? 'badge-success' : 'badge-danger' }}">
                                                {{ ucfirst($user->statut) }}
                                            </span>
                                        </td>
                                        <td>{{ $user->derniere_connexion ? $user->derniere_connexion->format('d/m/Y H:i') : 'Jamais' }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <button class="btn btn-icon btn-sm btn-outline-primary action-btn edit-user" 
                                                        data-id="{{ $user->id }}" 
                                                        data-nom="{{ $user->nom }}" 
                                                        data-prenom="{{ $user->prenom }}" 
                                                        data-email="{{ $user->email }}" 
                                                        data-role="{{ $user->role_id }}" 
                                                        data-service="{{ $user->service_id }}"
                                                        data-bs-toggle="tooltip"
                                                        title="Modifier">
                                                    <i class="ri-edit-line"></i>
                                                </button>
                                                
                                                <form action="{{ route('utilisateurs.toggle-status', $user) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-icon btn-sm {{ $user->statut === 'actif' ? 'btn-outline-warning' : 'btn-outline-success' }} action-btn" 
                                                            data-bs-toggle="tooltip" 
                                                            title="{{ $user->statut === 'actif' ? 'Désactiver' : 'Activer' }}">
                                                        <i class="ri-{{ $user->statut === 'actif' ? 'user-unfollow-line' : 'user-follow-line' }}"></i>
                                                    </button>
                                                </form>
                                                
                                                <form action="{{ route('utilisateurs.reset-password', $user) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-icon btn-sm btn-outline-info action-btn"
                                                            data-bs-toggle="tooltip"
                                                            title="Réinitialiser le mot de passe">
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
            
            <div class="tab-content" id="roles">
                <div class="card admin-card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="ri-shield-user-line"></i> Rôles système</h3>
                        <div class="header-actions">
                            <button id="addRoleBtn" class="btn btn-primary">
                                <i class="ri-add-line"></i> Nouveau rôle
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-container">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Description</th>
                                        <th>Utilisateurs</th>
                                        <th>Permissions</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($roles as $role)
                                    <tr>
                                        <td>{{ ucfirst($role->nom) }}</td>
                                        <td>{{ $role->description ?? 'Aucune description' }}</td>
                                        <td>{{ $role->users_count ?? 0 }}</td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                <!-- Simulation des permissions -->
                                                @if($role->nom === 'admin')
                                                <span class="badge badge-secondary">Tout</span>
                                                @elseif($role->nom === 'manager')
                                                <span class="badge badge-secondary">Lecture</span>
                                                <span class="badge badge-secondary">Écriture</span>
                                                @else
                                                <span class="badge badge-secondary">Lecture</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <button class="btn btn-icon btn-sm btn-outline-primary action-btn"
                                                        data-bs-toggle="tooltip"
                                                        title="Modifier">
                                                    <i class="ri-edit-line"></i>
                                                </button>
                                                <button class="btn btn-icon btn-sm btn-outline-secondary action-btn"
                                                        data-bs-toggle="tooltip"
                                                        title="Gérer les permissions">
                                                    <i class="ri-lock-line"></i>
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
            
            <div class="tab-content" id="permissions">
                <div class="card admin-card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="ri-lock-line"></i> Permissions système</h3>
                    </div>
                    <div class="card-body">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Module Utilisateurs -->
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">Module Utilisateurs</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" value="" id="user_view" checked disabled>
                                            <label class="form-check-label" for="user_view">
                                                Voir les utilisateurs
                                            </label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" value="" id="user_create" checked>
                                            <label class="form-check-label" for="user_create">
                                                Créer des utilisateurs
                                            </label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" value="" id="user_edit" checked>
                                            <label class="form-check-label" for="user_edit">
                                                Modifier des utilisateurs
                                            </label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" value="" id="user_delete">
                                            <label class="form-check-label" for="user_delete">
                                                Supprimer des utilisateurs
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Module Services -->
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">Module Services</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" value="" id="service_view" checked disabled>
                                            <label class="form-check-label" for="service_view">
                                                Voir les services
                                            </label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" value="" id="service_create" checked>
                                            <label class="form-check-label" for="service_create">
                                                Créer des services
                                            </label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" value="" id="service_edit" checked>
                                            <label class="form-check-label" for="service_edit">
                                                Modifier des services
                                            </label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" value="" id="service_delete">
                                            <label class="form-check-label" for="service_delete">
                                                Supprimer des services
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4 text-center">
                            <button class="btn btn-primary">Enregistrer les modifications</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajouter/Modifier Utilisateur -->
<div id="userModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 id="userModalTitle">Ajouter un utilisateur</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="userForm" action="{{ route('utilisateurs.store') }}" method="POST">
                    @csrf
                    <input type="hidden" id="userId" name="user_id">
                    <div class="form-group mb-3">
                        <label for="nom" class="form-label">Nom</label>
                        <input type="text" id="nom" name="nom" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="prenom" class="form-label">Prénom</label>
                        <input type="text" id="prenom" name="prenom" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="role_id" class="form-label">Rôle</label>
                        <select id="role_id" name="role_id" class="form-select" required>
                            <option value="">Sélectionner un rôle</option>
                            @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ ucfirst($role->nom) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="service_id" class="form-label">Service</label>
                        <select id="service_id" name="service_id" class="form-select" required>
                            <option value="">Sélectionner un service</option>
                            @foreach($services as $service)
                            <option value="{{ $service->id }}">{{ $service->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" id="saveUserBtn" class="btn btn-primary">Enregistrer</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="{{ asset('js/admin-modern.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Configuration du modal utilisateur
            const userModal = document.getElementById('userModal');
            const userForm = document.getElementById('userForm');
            
            // Bouton d'ajout d'utilisateur
            document.getElementById('addUserBtn').addEventListener('click', function() {
                document.getElementById('userModalTitle').textContent = 'Ajouter un utilisateur';
                userForm.reset();
                userForm.setAttribute('action', '{{ route("utilisateurs.store") }}');
                userForm.setAttribute('method', 'POST');
                
                // Ouvrir le modal (avec Bootstrap 5)
                const modal = new bootstrap.Modal(userModal);
                modal.show();
            });
            
            // Bouton de sauvegarde d'utilisateur
            document.getElementById('saveUserBtn').addEventListener('click', function() {
                userForm.submit();
            });
            
            // Boutons de modification d'utilisateur
            document.querySelectorAll('.edit-user').forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.getAttribute('data-id');
                    const nom = this.getAttribute('data-nom');
                    const prenom = this.getAttribute('data-prenom');
                    const email = this.getAttribute('data-email');
                    const roleId = this.getAttribute('data-role');
                    const serviceId = this.getAttribute('data-service');
                    
                    document.getElementById('userModalTitle').textContent = 'Modifier un utilisateur';
                    document.getElementById('userId').value = userId;
                    document.getElementById('nom').value = nom;
                    document.getElementById('prenom').value = prenom;
                    document.getElementById('email').value = email;
                    document.getElementById('role_id').value = roleId;
                    document.getElementById('service_id').value = serviceId;
                    
                    userForm.setAttribute('action', `{{ url('utilisateurs') }}/${userId}`);
                    userForm.setAttribute('method', 'POST');
                    
                    // Ajouter method PUT
                    let methodInput = document.querySelector('input[name="_method"]');
                    if (!methodInput) {
                        methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        userForm.appendChild(methodInput);
                    }
                    methodInput.value = 'PUT';
                    
                    // Ouvrir le modal (avec Bootstrap 5)
                    const modal = new bootstrap.Modal(userModal);
                    modal.show();
                });
            });
            
            // Recherche utilisateur
            document.getElementById('searchUser').addEventListener('input', function() {
                const searchText = this.value.toLowerCase();
                const rows = document.querySelectorAll('#usersTable tbody tr');
                
                rows.forEach(row => {
                    const name = row.querySelector('.user-name').textContent.toLowerCase();
                    const email = row.cells[1].textContent.toLowerCase();
                    const role = row.cells[2].textContent.toLowerCase();
                    
                    if (name.includes(searchText) || email.includes(searchText) || role.includes(searchText)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
            
            // Animation des cartes
            const cards = document.querySelectorAll('.admin-card');
            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
@endsection