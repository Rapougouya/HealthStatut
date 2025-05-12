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
    /* Animation pour les onglets */
    .tab-pane.fade:not(.show) {
        display: none;
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
        <!-- Navigation par onglets -->
        <ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="true">
                    <i class="ri-user-line"></i> Profil
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="appearance-tab" data-bs-toggle="tab" data-bs-target="#appearance" type="button" role="tab" aria-controls="appearance" aria-selected="false">
                    <i class="ri-palette-line"></i> Apparence
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="notifications-tab" data-bs-toggle="tab" data-bs-target="#notifications" type="button" role="tab" aria-controls="notifications" aria-selected="false">
                    <i class="ri-notification-line"></i> Notifications
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab" aria-controls="security" aria-selected="false">
                    <i class="ri-shield-check-line"></i> Sécurité
                </button>
            </li>
        </ul>

        <!-- Contenu des onglets -->
        <div class="tab-content" id="settingsTabContent">
            <!-- Onglet Profil -->
            <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
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
                                    <h4 class="mb-1">{{ auth()->user()->nom_complet }}</h4>
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
            
            <!-- Onglet Apparence -->
            <div class="tab-pane fade" id="appearance" role="tabpanel" aria-labelledby="appearance-tab">
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
                            
                            <div class="mb-4">
                                <label class="form-label">Disposition</label>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-4">
                                        <div class="layout-option active p-3 border rounded">
                                            <div class="layout-preview mb-2" style="height: 100px; background-color: #f1f5f9; position: relative;">
                                                <div style="position: absolute; left: 0; top: 0; bottom: 0; width: 30%; background-color: #e2e8f0;"></div>
                                                <div style="position: absolute; right: 0; top: 0; left: 30%; height: 20%; background-color: #cbd5e1;"></div>
                                            </div>
                                            <div class="text-center">Standard</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="layout-option p-3 border rounded">
                                            <div class="layout-preview mb-2" style="height: 100px; background-color: #f1f5f9; position: relative;">
                                                <div style="position: absolute; left: 0; top: 0; width: 100%; height: 20%; background-color: #cbd5e1;"></div>
                                                <div style="position: absolute; left: 0; top: 20%; bottom: 0; width: 30%; background-color: #e2e8f0;"></div>
                                            </div>
                                            <div class="text-center">Horizontal</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="layout-option p-3 border rounded">
                                            <div class="layout-preview mb-2" style="height: 100px; background-color: #f1f5f9; position: relative;">
                                                <div style="position: absolute; left: 0; top: 0; bottom: 0; width: 20%; background-color: #e2e8f0;"></div>
                                                <div style="position: absolute; right: 0; top: 0; left: 20%; height: 20%; background-color: #cbd5e1;"></div>
                                            </div>
                                            <div class="text-center">Compact</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label class="form-label">Taille du texte</label>
                                <input type="range" class="form-range" min="80" max="120" step="5" value="100" id="fontSizeRange">
                                <div class="d-flex justify-content-between">
                                    <span>Petit</span>
                                    <span>Normal</span>
                                    <span>Grand</span>
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
            
            <!-- Onglet Notifications -->
            <div class="tab-pane fade" id="notifications" role="tabpanel" aria-labelledby="notifications-tab">
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
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="appSystem" checked>
                                    <label class="form-check-label" for="appSystem">
                                        Annonces système
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <h5 class="mb-3">Fréquence des notifications</h5>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="notifFrequency" id="freqRealtime" value="realtime" checked>
                                    <label class="form-check-label" for="freqRealtime">
                                        Temps réel
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="notifFrequency" id="freqHourly" value="hourly">
                                    <label class="form-check-label" for="freqHourly">
                                        Résumé horaire
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="notifFrequency" id="freqDaily" value="daily">
                                    <label class="form-check-label" for="freqDaily">
                                        Résumé quotidien
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
            
            <!-- Onglet Sécurité -->
            <div class="tab-pane fade" id="security" role="tabpanel" aria-labelledby="security-tab">
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
                        
                        <hr class="my-4">
                        
                        <h5 class="mb-4">Authentification à deux facteurs</h5>
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span>Statut 2FA:</span>
                                <span class="badge bg-danger">Désactivé</span>
                            </div>
                            <p class="text-muted">L'authentification à deux facteurs ajoute une couche de sécurité supplémentaire à votre compte en demandant un code temporaire en plus de votre mot de passe.</p>
                            <button type="button" class="btn btn-outline-primary">Activer l'authentification à deux facteurs</button>
                        </div>
                        
                        <hr class="my-4">
                        
                        <h5 class="mb-4">Sessions actives</h5>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Appareil</th>
                                        <th>Localisation</th>
                                        <th>Dernière activité</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ri-computer-line me-2 text-primary"></i>
                                                Windows 10 - Chrome
                                            </div>
                                        </td>
                                        <td>Paris, France</td>
                                        <td>Actuellement</td>
                                        <td><span class="badge bg-success">Session courante</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ri-smartphone-line me-2 text-secondary"></i>
                                                iPhone - Safari
                                            </div>
                                        </td>
                                        <td>Paris, France</td>
                                        <td>Il y a 2 jours</td>
                                        <td><button class="btn btn-sm btn-outline-danger">Déconnecter</button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- jQuery first, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    // Initialisation des onglets Bootstrap
    var tabEls = document.querySelectorAll('button[data-bs-toggle="tab"]');
    tabEls.forEach(function(tabEl) {
        tabEl.addEventListener('click', function (event) {
            event.preventDefault();
            var tab = new bootstrap.Tab(this);
            tab.show();
        });
    });

    // Gestion de la force du mot de passe
    const passwordInput = document.getElementById('newPassword');
    if(passwordInput) {
        passwordInput.addEventListener('input', function() {
            const strength = calculatePasswordStrength(this.value);
            const progressBar = document.querySelector('.password-strength .progress-bar');
            const strengthText = document.getElementById('passwordStrength');
            
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
});
</script>
@endsection