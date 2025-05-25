<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\CapteurController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\ParametreController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Api\VitalSignsController;

/*
|--------------------------------------------------------------------------
| Routes Publiques (accessibles sans authentification)
|--------------------------------------------------------------------------
*/

// Redirection de la racine vers la page de connexion
Route::get('/', function () {
    return redirect()->route('login');
});

// Routes d'authentification
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Routes d'inscription
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Routes pour la réinitialisation du mot de passe
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

/*
|--------------------------------------------------------------------------
| Routes Protégées (accessibles uniquement après authentification)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    // Dashboard - Accessible à tous les utilisateurs authentifiés
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    /*
    |--------------------------------------------------------------------------
    | Routes pour les Administrateurs
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        // Gestion des paramètres administratifs
        Route::get('/parametres', [ParametreController::class, 'adminIndex'])->name('admin.parametres');
        
        // Gestion des utilisateurs
        Route::get('/utilisateurs', [ParametreController::class, 'adminUsers'])->name('utilisateurs.index');
        Route::post('/utilisateurs', [ParametreController::class, 'storeUser'])->name('utilisateurs.store');
        Route::put('/utilisateurs/{user}', [ParametreController::class, 'updateUser'])->name('utilisateurs.update');
        Route::post('/utilisateurs/{user}/toggle-status', [ParametreController::class, 'toggleUserStatus'])->name('utilisateurs.toggle-status');
        Route::post('/utilisateurs/{user}/reset-password', [ParametreController::class, 'resetUserPassword'])->name('utilisateurs.reset-password');

        // Gestion des services
        Route::post('/services', [ParametreController::class, 'storeService'])->name('services.store');
        Route::put('/services/{service}', [ParametreController::class, 'updateService'])->name('services.update');
        
        // Maintenance système
        Route::post('/parametres/system-maintenance', [ParametreController::class, 'systemMaintenance'])->name('parametres.system-maintenance');
    });
    
    /*
    |--------------------------------------------------------------------------
    | Routes pour les Patients
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:patient'])->group(function () {
        // Accès aux prescriptions personnelles
        Route::get('/mes-prescriptions', [PrescriptionController::class, 'mesPrescriptions'])->name('prescriptions.mes');
        
        // Configuration des seuils d'alerte personnels
        Route::put('/parametres/update-thresholds', [ParametreController::class, 'updateThresholds'])->name('parametres.update-thresholds');
        Route::post('/parametres/reset-thresholds', [ParametreController::class, 'resetThresholds'])->name('parametres.reset-thresholds');
    });
    
    /*
    |--------------------------------------------------------------------------
    | Routes pour les Médecins et Administrateurs
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin,medecin'])->group(function () {
        // Gestion des patients
        Route::resource('patients', PatientController::class);
        Route::get('patients/{patient}/notes', [PatientController::class, 'notes'])->name('patients.notes');
        Route::get('patient/liste', [PatientController::class, 'liste'])->name('patients.liste');
        Route::get('/patients/search', [PatientController::class, 'search'])->name('patients.search');
        
        // Gestion des alertes
        Route::get('/alertes', [AlertController::class, 'index'])->name('alertes.index');
        Route::get('/alertes/create', [AlertController::class, 'create'])->name('alertes.create');
        Route::post('/alertes', [AlertController::class, 'store'])->name('alertes.store');
        Route::get('/alertes/{alert}', [AlertController::class, 'show'])->name('alertes.show');
        Route::get('/alertes/{alert}/edit', [AlertController::class, 'edit'])->name('alertes.edit');
        Route::put('/alertes/{alert}', [AlertController::class, 'update'])->name('alertes.update');
        Route::delete('/alertes/{alert}', [AlertController::class, 'destroy'])->name('alertes.destroy');
        Route::get('/alertes/history', [AlertController::class, 'history'])->name('alertes.history');
        Route::post('/alertes/{alert}/resolve', [AlertController::class, 'resolve'])->name('alertes.resolve');
        Route::post('/alertes/resolve-all', [AlertController::class, 'resolveAll'])->name('alertes.resolve-all');
        Route::get('/alertes/settings', [AlertController::class, 'settings'])->name('alertes.settings');
        Route::post('/alertes/settings/save', [AlertController::class, 'saveSettings'])->name('alertes.settings.save');
        Route::post('/alertes/settings/reset', [AlertController::class, 'resetSettings'])->name('alertes.settings.reset');
        Route::post('/alertes/{alert}/contact-patient', [AlertController::class, 'contactPatient'])->name('alertes.contact-patient');
        Route::post('alertes/confirm', [AlertController::class, 'confirm'])->name('alertes.confirm');
        
        // Gestion des rapports
        Route::resource('rapports', RapportController::class);
        Route::get('rapports/{rapport}/download', [RapportController::class, 'download'])->name('rapports.download');
        Route::get('rapports/generate', [RapportController::class, 'generate'])->name('reports.generate');
        
        // Gestion des prescriptions
        Route::resource('prescriptions', PrescriptionController::class);
        
        // Accès à l'historique
        Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
        Route::get('/history/export', [HistoryController::class, 'export'])->name('history.export');
        Route::get('/history/details/{patient?}/{date?}', [HistoryController::class, 'details'])->name('history.details');
    });
    
    /*
    |--------------------------------------------------------------------------
    | Routes pour les Techniciens et Administrateurs
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin,technicien'])->group(function () {
        // Gestion des capteurs
        Route::resource('capteurs', CapteurController::class);
        Route::get('/sensors', [CapteurController::class, 'index'])->name('sensors.index');
        Route::get('/sensors/create', [CapteurController::class, 'create'])->name('sensors.create');
        Route::post('/sensors', [CapteurController::class, 'store'])->name('sensors.store');
        Route::get('/sensors/{sensor}', [CapteurController::class, 'show'])->name('sensors.show');
        Route::get('/sensors/{sensor}/edit', [CapteurController::class, 'edit'])->name('sensors.edit');
        Route::put('/sensors/{sensor}', [CapteurController::class, 'update'])->name('sensors.update');
        Route::delete('/sensors/{sensor}', [CapteurController::class, 'destroy'])->name('sensors.destroy');
        Route::get('/sensors/{sensor}/configure', [CapteurController::class, 'configure'])->name('sensors.configure');
        Route::post('/sensors/{sensor}/configure', [CapteurController::class, 'saveConfiguration'])->name('sensors.save-configuration');
        Route::get('/sensors/{sensor}/history', [CapteurController::class, 'history'])->name('sensors.history');
        Route::post('/sensors/{sensor}/reset', [CapteurController::class, 'reset'])->name('sensors.reset');
        Route::post('/sensors/{sensor}/unassign', [CapteurController::class, 'unassign'])->name('sensors.unassign');
        Route::post('/sensors/{sensor}/assign', [CapteurController::class, 'assign'])->name('sensors.assign');
        Route::get('/sensors/wifi/config', [CapteurController::class, 'wifiConfig'])->name('sensors.wifi-config');
        
        // API pour les capteurs
        Route::post('/api/sensors/{sensor}/configure', [CapteurController::class, 'apiSaveConfiguration'])->name('api.sensors.configure');
        Route::get('/api/sensors', [CapteurController::class, 'apiGetSensors'])->name('api.sensors.list');
    });
    
    /*
    |--------------------------------------------------------------------------
    | Routes pour tous les utilisateurs authentifiés (sauf patients)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth', 'role:admin,medecin,technicien'])->group(function () {
        // Paramètres personnels - Accessible à tous sauf patients
        Route::get('/parametres', [ParametreController::class, 'index'])->name('parametres.index');
        Route::put('/profile/update', [ParametreController::class, 'updateProfile'])->name('profile.update');
        Route::put('/parametres/update-notifications', [ParametreController::class, 'updateNotifications'])->name('utilisateurs.update-notifications');
        Route::put('/parametres/update-appearance', [ParametreController::class, 'updateAppearance'])->name('utilisateurs.update-appearance');
    });
});

// Dans routes/web.php

// Routes pour les Administrateurs (accessibles uniquement par les admin)
Route::middleware(['role:admin'])->prefix('admin')->group(function () {
    // Affichage de la page des paramètres admin
    //Route::get('/parametres', [ParametreController::class, 'adminIndex'])->name('admin.parametres');
    
    // Page de gestion des utilisateurs
    Route::get('/utilisateurs', [ParametreController::class, 'adminUsers'])->name('utilisateurs.index');
    
    // Création d'un nouvel utilisateur
    Route::post('/utilisateurs', [ParametreController::class, 'storeUser'])->name('utilisateurs.store');
    
    // Mise à jour d'un utilisateur existant
    Route::put('/utilisateurs/{user}', [ParametreController::class, 'updateUser'])->name('utilisateurs.update');
    
    // Activation/désactivation d'un utilisateur
    Route::post('/utilisateurs/{user}/toggle-status', [ParametreController::class, 'toggleUserStatus'])->name('utilisateurs.toggle-status');
    
    // Réinitialisation du mot de passe d'un utilisateur
    Route::post('/utilisateurs/{user}/reset-password', [ParametreController::class, 'resetUserPassword'])->name('utilisateurs.reset-password');

    // Gestion des services
    Route::post('/services', [ParametreController::class, 'storeService'])->name('services.store');
    Route::put('/services/{service}', [ParametreController::class, 'updateService'])->name('services.update');
});
