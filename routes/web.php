<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\CapteurController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\ParametreController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\ServiceManagementController;
use App\Http\Controllers\SystemAdminController;
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
    // Dashboard redirection personnalisé
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ADMIN uniquement
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        Route::get('/parametres', [ParametreController::class, 'adminIndex'])->name('admin.parametres');

        // Utilisateurs
        Route::get('/utilisateurs', [UserManagementController::class, 'index'])->name('utilisateurs.index');
        Route::post('/utilisateurs', [UserManagementController::class, 'store'])->name('utilisateurs.store');
        Route::put('/utilisateurs/{user}', [UserManagementController::class, 'update'])->name('utilisateurs.update');
        Route::post('/utilisateurs/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('utilisateurs.toggle-status');
        Route::post('/utilisateurs/{user}/reset-password', [UserManagementController::class, 'resetPassword'])->name('utilisateurs.reset-password');

        // Services
        Route::post('/services', [ServiceManagementController::class, 'store'])->name('services.store');
        Route::put('/services/{service}', [ServiceManagementController::class, 'update'])->name('services.update');
        
         // Routes pour les pages système
         Route::prefix('system')->name('admin.system.')->group(function () {
            // Configuration serveur
            Route::get('/server-config', [SystemAdminController::class, 'serverConfig'])->name('server-config');
            Route::post('/server-config', [SystemAdminController::class, 'saveServerConfig'])->name('save-server-config');
            
            // Logs système
            Route::get('/logs', [SystemAdminController::class, 'systemLogs'])->name('logs');
                        
            // Maintenance système
            Route::get('/maintenance', [SystemAdminController::class, 'systemMaintenance'])->name('maintenance');
            Route::post('/maintenance', [SystemAdminController::class, 'performMaintenance'])->name('perform-maintenance');
            
            // Export des données
            Route::get('/export-data', [SystemAdminController::class, 'exportData'])->name('export-data');
            Route::post('/export-data', [SystemAdminController::class, 'performExport'])->name('perform-export');
        });
        
        // Maintenance système (route legacy)
        Route::post('/parametres/system-maintenance', [ParametreController::class, 'systemMaintenance'])->name('parametres.system-maintenance');
    });
   

    // PATIENT uniquement
    Route::middleware(['role:patient'])->group(function () {
        Route::get('/mes-prescriptions', [PrescriptionController::class, 'mesPrescriptions'])->name('prescriptions.mes');
        Route::get('/mon-suivi', [PatientController::class, 'monSuivi'])->name('patient.suivi');
        Route::get('/mes-donnees', [PatientController::class, 'mesDonnees'])->name('patient.donnees');
        Route::put('/parametres/update-thresholds', [ParametreController::class, 'updateThresholds'])->name('parametres.update-thresholds');
        Route::post('/parametres/reset-thresholds', [ParametreController::class, 'resetThresholds'])->name('parametres.reset-thresholds');
        Route::get('/mes-parametres', [ParametreController::class, 'patientSettings'])->name('patient.parametres');
        Route::put('/mes-parametres/update', [ParametreController::class, 'updatePatientProfile'])->name('patient.profile.update');
    });

    // MEDECIN uniquement
    Route::middleware(['role:medecin'])->group(function () {
        Route::get('/mes-patients', [PatientController::class, 'mesPatients'])->name('medecin.patients');
        Route::get('/consultations', [PatientController::class, 'consultations'])->name('medecin.consultations');
    });

    // TECHNICIEN uniquement
    Route::middleware(['role:technicien'])->group(function () {
        Route::get('/maintenance', [CapteurController::class, 'maintenance'])->name('technicien.maintenance');
        Route::get('/diagnostics', [CapteurController::class, 'diagnostics'])->name('technicien.diagnostics');
    });

    // ADMIN + MEDECIN
    Route::middleware(['role:admin,medecin'])->group(function () {
        Route::resource('patients', PatientController::class);
        Route::get('patients/{patient}/notes', [PatientController::class, 'notes'])->name('patients.notes');
        Route::get('patient/liste', [PatientController::class, 'liste'])->name('patients.liste');
        Route::get('/patients/search', [PatientController::class, 'search'])->name('patients.search');

        // Gestion des rapports - Routes principales
        Route::get('/rapports', [RapportController::class, 'index'])->name('rapports.index');
        Route::get('/reports/generate', [RapportController::class, 'generate'])->name('reports.generate');
        Route::post('/reports', [RapportController::class, 'store'])->name('reports.store');
        Route::get('/reports/{rapport}', [RapportController::class, 'show'])->name('reports.show');
        Route::get('/reports/{rapport}/download', [RapportController::class, 'download'])->name('reports.download');
                
        // Routes spécifiques par type de rapport
        Route::get('/rapports/medical', [RapportController::class, 'medical'])->name('reports.medical');
        Route::get('/rapports/vitals', [RapportController::class, 'vitals'])->name('reports.vitals');
        Route::get('/rapports/laboratory', [RapportController::class, 'laboratory'])->name('reports.laboratory');
        Route::get('/rapports/prescriptions', [RapportController::class, 'prescriptions'])->name('reports.prescriptions');
                
        // Routes anciennes pour compatibilité
        Route::resource('rapports', RapportController::class);
        Route::get('rapports/{rapport}/download', [RapportController::class, 'download'])->name('rapports.download');
                
        Route::resource('prescriptions', PrescriptionController::class);

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

        Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
        Route::get('/history/export', [HistoryController::class, 'export'])->name('history.export');
        Route::get('/history/details/{patient?}/{date?}', [HistoryController::class, 'details'])->name('history.details');
    });

    // ADMIN + TECHNICIEN
    Route::middleware(['role:admin,technicien'])->group(function () {
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

        // API
        Route::post('/api/sensors/{sensor}/configure', [CapteurController::class, 'apiSaveConfiguration'])->name('api.sensors.configure');
        Route::get('/api/sensors', [CapteurController::class, 'apiGetSensors'])->name('api.sensors.list');
    });

    // ADMIN + MEDECIN + TECHNICIEN (hors patient)
    Route::middleware(['role:admin,medecin,technicien'])->group(function () {
        Route::get('/parametres', [ParametreController::class, 'index'])->name('parametres.index');
        Route::put('/profile/update', [ParametreController::class, 'updateProfile'])->name('profile.update');
        Route::put('/parametres/update-notifications', [UserManagementController::class, 'updateNotifications'])->name('utilisateurs.update-notifications');
        Route::put('/parametres/update-appearance', [UserManagementController::class, 'updateAppearance'])->name('utilisateurs.update-appearance');
    });
});