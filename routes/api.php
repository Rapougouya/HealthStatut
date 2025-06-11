<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\CapteurController;
use App\Http\Controllers\Api\VitalSignsController;
use App\Http\Controllers\Api\PatientController as ApiPatientController;
use App\Http\Controllers\Api\CapteurController as ApiCapteurController;
use App\Http\Controllers\Api\AlertController as ApiAlertController;
use App\Http\Controllers\Api\AlertSettingsController;
use App\Http\Controllers\AlertManagementController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
*/

// Routes d'API protégées par authentification
Route::middleware('auth:sanctum')->group(function () {
    // Routes pour les patients
    // Route::apiResource('patients', ApiPatientController::class);
    // Route::get('/patients-stats', [ApiPatientController::class, 'stats']);
    
    // Routes pour les capteurs
    Route::apiResource('sensors', ApiCapteurController::class);
    Route::post('/sensors/{sensor}/data', [ApiCapteurController::class, 'receiveData']);
    Route::get('/sensors/{sensor}/data', [ApiCapteurController::class, 'getData']);
    Route::get('/sensors-stats', [ApiCapteurController::class, 'stats']);
    
    // Routes pour les alertes
    Route::apiResource('alerts', ApiAlertController::class);
    Route::post('/alerts/{alert}/resolve', [ApiAlertController::class, 'resolve']);
    Route::post('/alerts/{alert}/reopen', [ApiAlertController::class, 'reopen']);
    Route::post('/alerts/mark-as-read', [ApiAlertController::class, 'markAsRead']);
    Route::get('/alerts-stats', [ApiAlertController::class, 'stats']);
    
    // Routes pour les paramètres d'alerte
    Route::get('/alert-settings', [AlertSettingsController::class, 'show']);
    Route::put('/alert-settings', [AlertSettingsController::class, 'update']);
    Route::post('/alert-settings/reset', [AlertSettingsController::class, 'reset']);
    
    // Routes pour la gestion des alertes
    Route::get('/alert-management/stats', [AlertManagementController::class, 'getStats']);
    Route::get('/alert-management/real-time', [AlertManagementController::class, 'getRealTimeAlerts']);
    Route::post('/alert-management/trigger', [AlertManagementController::class, 'triggerManualAlert']);
    Route::post('/alert-management/test', [AlertManagementController::class, 'testAlert']);
    
    // Routes pour les données de signes vitaux des patients
    Route::get('/patients/{patient}/vital-signs', [VitalSignsController::class, 'getPatientData']);
    
    // Route pour obtenir les informations de l'utilisateur connecté
    Route::get('/user', function (Request $request) {
        return response()->json([
            'success' => true,
            'data' => new \App\Http\Resources\UserResource($request->user()->load(['role', 'service']))
        ]);
    });
});

// Routes d'API publiques (pour les capteurs externes)
Route::post('/alerts', [AlertController::class, 'apiStore']);
Route::post('/sensor-data', [CapteurController::class, 'apiReceiveData'])->name('api.sensor-data');
Route::get('/sensors/configuration', [CapteurController::class, 'apiGetConfiguration'])->name('api.sensors.configuration');
Route::post('/sensors/{sensor}/configure', [CapteurController::class, 'apiSaveConfiguration'])->name('api.sensors.configure');
Route::get('/sensors', [CapteurController::class, 'apiGetSensors'])->name('api.sensors.list');

// Route pour la simulation de capteurs (uniquement en environnement de développement)
if (app()->environment('local', 'development')) {
    Route::get('/sensors/simulate', function () {
        return view('sensors.simulator');
    });
}

// Route de test pour vérifier la santé de l'API
Route::get('/health', function() {
    return response()->json([
        'success' => true,
        'message' => 'API opérationnelle',
        'timestamp' => now()->toDateTimeString(),
        'version' => '1.0.0',
        'environment' => app()->environment(),
    ]);
});