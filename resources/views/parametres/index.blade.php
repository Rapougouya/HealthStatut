@extends('layouts.app')

@section('title', 'Paramètres')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-modern.css') }}">
    <style>
        .param-header {
            background: linear-gradient(135deg, var(--secondary), var(--info));
            color: white;
            padding: 2rem;
            border-radius: var(--radius);
            margin-bottom: 2rem;
        }
        
        .param-card {
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .param-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
        }
        
        .param-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: var(--primary);
        }
    </style>
@endsection

@section('content')
<div class="admin-container">
    <div class="param-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title"><i class="ri-settings-4-line"></i> Paramètres</h1>
                <p class="text-white-50">Configurez vos préférences et personnalisez l'application</p>
            </div>
        </div>
    </div>
    
    <div class="admin-panel">
        <ul class="nav nav-tabs" id="paramTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="profile-tab" data-toggle="tab" href="#profile" role="tab">
                    <i class="ri-user-line"></i> Profil
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="security-tab" data-toggle="tab" href="#security" role="tab">
                    <i class="ri-shield-check-line"></i> Sécurité
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="notif-tab" data-toggle="tab" href="#notifications" role="tab">
                    <i class="ri-notification-line"></i> Notifications
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="appearance-tab" data-toggle="tab" href="#appearance" role="tab">
                    <i class="ri-palette-line"></i> Apparence
                </a>
            </li>
        </ul>
        
        <div class="tab-content fade-transition" id="paramTabContent">
            <div class="tab-content active" id="profile">
                <div class="card admin-card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="ri-user-settings-line"></i> Informations personnelles</h3>
                    </div>
                    <div class="card-body">
                        <form id="profileForm" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="d-flex mb-4 align-items-center">
                                <div class="me-4">
                                    <div class="avatar" style="width: 100px; height: 100px; font-size: 2rem;">
                                        @if(auth()->user()->avatar)
                                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="{{ auth()->user()->nom }}">
                                        @else
                                            {{ strtoupper(substr(auth()->user()->prenom, 0, 1) . substr(auth()->user()->nom, 0, 1)) }}
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <h4 class="mb-1">{{ auth()->user()->prenom }} {{ auth()->user()->nom }}</h4>
                                    <p class="text-muted mb-2">{{ auth()->user()->role->nom }} - {{ auth()->user()->service->name }}</p>
                                    <div class="d-flex gap-2">
                                        <label class="btn btn-sm btn-outline-primary">
                                            <i class="ri-upload-2-line"></i> Changer la photo
                                            <input type="file" name="avatar" style="display: none">
                                        </label>
                                        <button type="button" class="btn btn-sm btn-outline-danger">
                                            <i class="ri-delete-bin-line"></i> Supprimer
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nom" class="form-label">Nom</label>
                                        <input type="text" id="nom" name="nom" class="form-control" value="{{ auth()->user()->nom }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="prenom" class="form-label">Prénom</label>
                                        <input type="text" id="prenom" name="prenom" class="form-control" value="{{ auth()->user()->prenom }}">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" id="email" name="email" class="form-control" value="{{ auth()->user()->email }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="telephone" class="form-label">Téléphone</label>
                                        <input type="tel" id="telephone" name="telephone" class="form-control" value="{{ auth()->user()->telephone ?? '' }}">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group mb-4">
                                <label for="bio" class="form-label">Bio</label>
                                <textarea id="bio" name="bio" class="form-control" rows="3">{{ auth()->user()->bio ?? '' }}</textarea>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="tab-content" id="security">
                <div class="card admin-card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="ri-shield-check-line"></i> Sécurité du compte</h3>
                    </div>
                    <div class="card-body">
                        <h5 class="mb-4">Changer le mot de passe</h5>
                        <form id="passwordForm">
                            <div class="mb-3">
                                <label for="currentPassword" class="form-label">Mot de passe actuel</label>
                                <input type="password" class="form-control" id="currentPassword" required>
                            </div>
                            <div class="mb-3">
                                <label for="newPassword" class="form-label">Nouveau mot de passe</label>
                                <input type="password" class="form-control" id="newPassword" required>
                                <div class="password-strength mt-2">
                                    <div class="progress" style="height: 5px;">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: 0%"></div>
                                    </div>
                                    <small class="text-muted">Force: <span id="passwordStrength">Faible</span></small>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="confirmPassword" class="form-label">Confirmer le nouveau mot de passe</label>
                                <input type="password" class="form-control" id="confirmPassword" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Mettre à jour le mot de passe</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="tab-content" id="notifications">
                <div class="card admin-card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="ri-notification-line"></i> Préférences de notification</h3>
                    </div>
                    <div class="card-body">
                        <form id="notificationsForm">
                            <div class="mb-4">
                                <h5 class="mb-3">Notifications par email</h5>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="emailAlerts" checked>
                                    <label class="form-check-label" for="emailAlerts">
                                        Alertes patients
                                    </label>
                                </div>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="emailAppointments" checked>
                                    <label class="form-check-label" for="emailAppointments">
                                        Rappels de rendez-vous
                                    </label>
                                </div>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="emailSystem">
                                    <label class="form-check-label" for="emailSystem">
                                        Annonces système
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <h5 class="mb-3">Notifications dans l'application</h5>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="appAlerts" checked>
                                    <label class="form-check-label" for="appAlerts">
                                        Alertes patients
                                    </label>
                                </div>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="appAppointments" checked>
                                    <label class="form-check-label" for="appAppointments">
                                        Rappels de rendez-vous
                                    </label>
                                </div>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="appMessages" checked>
                                    <label class="form-check-label" for="appMessages">
                                        Nouveaux messages
                                    </label>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Enregistrer les préférences</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="tab-content" id="appearance">
                <div class="card admin-card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="ri-palette-line"></i> Personnalisation de l'interface</h3>
                    </div>
                    <div class="card-body">
                        <form id="appearanceForm">
                            <div class="mb-4">
                                <label class="form-label">Thème</label>
                                <div class="d-flex gap-4 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="theme" id="themeLight" value="light" checked>
                                        <label class="form-check-label" for="themeLight">
                                            Clair
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="theme" id="themeDark" value="dark">
                                        <label class="form-check-label" for="themeDark">
                                            Sombre
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="theme" id="themeSystem" value="system">
                                        <label class="form-check-label" for="themeSystem">
                                            Système
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Couleur principale</label>
                                <div class="d-flex gap-3 mb-3">
                                    <div class="color-option active" style="background-color: #6366f1;"></div>
                                    <div class="color-option" style="background-color: #3b82f6;"></div>
                                    <div class="color-option" style="background-color: #10b981;"></div>
                                    <div class="color-option" style="background-color: #f59e0b;"></div>
                                    <div class="color-option" style="background-color: #ef4444;"></div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Appliquer</button>
                                <button type="reset" class="btn btn-secondary">Réinitialiser</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="{{ asset('js/admin-modern.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gestion des onglets
            const tabLinks = document.querySelectorAll('.nav-link');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Désactiver tous les onglets
                    tabLinks.forEach(item => item.classList.remove('active'));
                    tabContents.forEach(item => {
                        item.classList.remove('active');
                        item.classList.remove('show');
                    });
                    
                    // Activer l'onglet cliqué
                    this.classList.add('active');
                    const tabId = this.getAttribute('href').substring(1);
                    const activeTab = document.getElementById(tabId);
                    activeTab.classList.add('active');
                    
                    // Animation
                    setTimeout(() => {
                        activeTab.classList.add('show');
                    }, 50);
                });
            });
            
            // Gestion des options de couleur
            const colorOptions = document.querySelectorAll('.color-option');
            colorOptions.forEach(option => {
                option.addEventListener('click', function() {
                    colorOptions.forEach(o => o.classList.remove('active'));
                    this.classList.add('active');
                    document.documentElement.style.setProperty('--primary', this.style.backgroundColor);
                });
            });
            
            // Gestion de la force du mot de passe
            const passwordInput = document.getElementById('newPassword');
            if (passwordInput) {
                const progressBar = document.querySelector('.password-strength .progress-bar');
                const strengthText = document.getElementById('passwordStrength');
                
                passwordInput.addEventListener('input', function() {
                    const strength = calculatePasswordStrength(this.value);
                    
                    if (strength < 25) {
                        progressBar.className = 'progress-bar bg-danger';
                        progressBar.style.width = '25%';
                        strengthText.textContent = 'Très faible';
                    } else if (strength < 50) {
                        progressBar.className = 'progress-bar bg-warning';
                        progressBar.style.width = '50%';
                        strengthText.textContent = 'Faible';
                    } else if (strength < 75) {
                        progressBar.className = 'progress-bar bg-info';
                        progressBar.style.width = '75%';
                        strengthText.textContent = 'Moyen';
                    } else {
                        progressBar.className = 'progress-bar bg-success';
                        progressBar.style.width = '100%';
                        strengthText.textContent = 'Fort';
                    }
                });
            }
            
            function calculatePasswordStrength(password) {
                let strength = 0;
                
                // Longueur
                if (password.length >= 8) strength += 25;
                
                // Majuscule
                if (/[A-Z]/.test(password)) strength += 25;
                
                // Chiffre
                if (/[0-9]/.test(password)) strength += 25;
                
                // Caractère spécial
                if (/[^A-Za-z0-9]/.test(password)) strength += 25;
                
                return strength;
            }
            
            // Animation d'entrée
            setTimeout(() => {
                const activeContent = document.querySelector('.tab-content.active');
                if (activeContent) {
                    activeContent.classList.add('show');
                }
            }, 100);
        });
    </script>
@endsection