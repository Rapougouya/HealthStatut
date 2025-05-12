<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\SensorController;
use App\Http\Controllers\Api\VitalSignsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Routes d'API protégées par authentification
Route::middleware('auth:sanctum')->group(function () {
    // Routes pour les données de signes vitaux des patients
    Route::get('/patients/{patient}/vital-signs', [VitalSignsController::class, 'getPatientData']);
});

// Routes d'API pour les alertes (non-protégées)
Route::post('/alerts', [AlertController::class, 'apiStore']);

// Routes d'API pour les capteurs (non-protégées)
Route::post('/sensor-data', [SensorController::class, 'apiReceiveData'])->name('api.sensor-data');
Route::get('/sensors/configuration', [SensorController::class, 'apiGetConfiguration'])->name('api.sensors.configuration');
Route::post('/sensors/{sensor}/configure', [SensorController::class, 'apiSaveConfiguration'])->name('api.sensors.configure');
Route::get('/sensors', [SensorController::class, 'apiGetSensors'])->name('api.sensors.list');

// Route pour la simulation de capteurs (uniquement en environnement de développement)
if (app()->environment('local', 'development')) {
    Route::get('/sensors/simulate', function () {
        return view('sensors.simulator');
    });
}