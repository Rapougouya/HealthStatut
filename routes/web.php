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


Route::get('/', function () {
    return redirect()->route('login');
});

// Routes d'authentification
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Route pour afficher le formulaire d'inscription
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');

Route::get('/alertes/settings', [AlertController::class, 'settings'])->name('alertes.settings');
// Route pour traiter la soumission du formulaire d'inscription
Route::post('/register', [AuthController::class, 'register']);

// Routes pour la réinitialisation du mot de passe
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])
    ->name('password.request'); // Affiche le formulaire de demande de réinitialisation
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->name('password.email'); // Envoie le lien de réinitialisation par e-mail

Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])
    ->name('password.reset'); // Affiche le formulaire de réinitialisation
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
    ->name('password.update'); // Traite la réinitialisation du mot de passe


// Groupe de routes protégées par authentification
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 📌 Accès à tous les utilisateurs connectés
    Route::resource('patients', PatientController::class);
    Route::get('patients/{patient}/notes', [PatientController::class, 'notes'])->name('patients.notes');
    
    // Route pour les paramètres déplacée ici pour la protéger
   
    // Routes pour les paramètres personnels (accessible à tous les utilisateurs authentifiés)
Route::middleware(['auth'])->group(function () {
    Route::get('/parametres', [ParametreController::class, 'index'])->name('parametres.index');
    // Ajout de la route manquante pour la mise à jour du profil
    Route::put('/profile/update', [ParametreController::class, 'updateProfile'])->name('profile.update');

});
    // 🔒 Routes réservées à l’ADMIN
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Route d'administration principale
    Route::get('/admin/parametres', [ParametreController::class, 'adminIndex'])->name('admin.parametres');
    
    // Gestion des utilisateurs
    Route::get('/admin/utilisateurs', [ParametreController::class, 'adminUsers'])->name('utilisateurs.index');
    Route::post('/utilisateurs', [ParametreController::class, 'storeUser'])->name('utilisateurs.store');
    Route::put('/utilisateurs/{user}', [ParametreController::class, 'updateUser'])->name('utilisateurs.update');
    Route::post('/utilisateurs/{user}/toggle-status', [ParametreController::class, 'toggleUserStatus'])->name('utilisateurs.toggle-status');
    Route::post('/utilisateurs/{user}/reset-password', [ParametreController::class, 'resetUserPassword'])->name('utilisateurs.reset-password');

    // Gestion des services
    Route::post('/services', [ParametreController::class, 'storeService'])->name('services.store');
    Route::put('/services/{service}', [ParametreController::class, 'updateService'])->name('services.update');
    
    // Les ressources qui ne sont plus nécessaires car remplacées par les routes ci-dessus
    // Route::resource('utilisateurs', UserController::class)->except(['show']);
    // Route::resource('services', ServiceController::class)->except(['show']);
});

    // 🔒 Routes accessibles à ADMIN et MÉDECIN
    Route::middleware(['role:admin,medecin'])->group(function () {
        // Alertes
        Route::resource('alertes', AlertController::class)->except(['create', 'store', 'edit', 'update', 'destroy']);
        Route::post('alertes/confirm', [AlertController::class, 'confirm'])->name('alertes.confirm');
    
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
    
        // Correction : Utiliser /alertes/settings au lieu de /alerts/settings
       
        Route::post('/alertes/settings/save', [AlertController::class, 'saveSettings'])->name('alertes.settings.save');
        Route::post('/alertes/settings/reset', [AlertController::class, 'resetSettings'])->name('alertes.settings.reset');
        Route::post('/alertes/{alert}/contact-patient', [AlertController::class, 'contactPatient'])->name('alertes.contact-patient');
    });

    // 🔒 Routes accessibles à ADMIN et TECHNICIEN
    Route::middleware(['role:admin,technicien'])->group(function () {
        // Capteurs
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
    });

    // 🔒 Routes accessibles à ADMIN et MÉDECIN
    Route::middleware(['role:admin,medecin'])->group(function () {
        Route::get('rapports/{rapport}/download', [RapportController::class, 'download'])->name('rapports.download');
        Route::get('rapports/generate', [RapportController::class, 'generate'])->name('reports.generate');      
        Route::resource('rapports', RapportController::class);

        Route::resource('prescriptions', PrescriptionController::class);
        
        Route::get('patient/liste', [PatientController::class, 'liste'])->name('patients.liste');
        Route::get('/patients/search', [PatientController::class, 'search'])->name('patients.search');
    });


    Route::middleware(['auth', 'role:patient'])->group(function () {
        Route::get('/mes-prescriptions', [PrescriptionController::class, 'mesPrescriptions'])->name('prescriptions.mes');
    });
    
    // ⚙️ Paramètres pour tous
    Route::get('/parametres', [ParametreController::class, 'index'])->name('parametres.index');
    Route::put('/parametres/update-profile', [ParametreController::class, 'updateProfile'])->name('utilisateurs.update-profile');
    Route::put('/parametres/update-notifications', [ParametreController::class, 'updateNotifications'])->name('utilisateurs.update-notifications');
    Route::put('/parametres/update-appearance', [ParametreController::class, 'updateAppearance'])->name('utilisateurs.update-appearance');
    Route::put('/parametres/update-thresholds', [ParametreController::class, 'updateThresholds'])->name('parametres.update-thresholds');
    Route::post('/parametres/reset-thresholds', [ParametreController::class, 'resetThresholds'])->name('parametres.reset-thresholds');
    Route::post('/parametres/system-maintenance', [ParametreController::class, 'systemMaintenance'])->name('parametres.system-maintenance');

    // 📜 Historique
    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
    Route::get('/history/export', [HistoryController::class, 'export'])->name('history.export');
    Route::get('/history/details/{patient?}/{date?}', [HistoryController::class, 'details'])->name('history.details');

});
