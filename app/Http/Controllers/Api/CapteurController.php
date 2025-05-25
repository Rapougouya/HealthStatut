<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sensor;
use App\Models\SensorReading;
use App\Http\Requests\Api\StoreSensorRequest;
use App\Http\Requests\Api\UpdateSensorRequest;
use App\Http\Resources\SensorResource;
use App\Http\Resources\SensorReadingResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SensorController extends Controller
{
    /**
     * Lister tous les capteurs
     */
    public function index(Request $request): JsonResponse
    {
        $query = Sensor::with('patient');
        
        // Filtrage par statut
        if ($request->has('status') && $request->get('status') !== 'all') {
            $query->where('status', $request->get('status'));
        }
        
        // Filtrage par type
        if ($request->has('type') && $request->get('type') !== 'all') {
            $query->where('type', $request->get('type'));
        }
        
        // Filtrage par patient
        if ($request->has('patient_id') && $request->get('patient_id') !== 'all') {
            $query->where('patient_id', $request->get('patient_id'));
        }
        
        $sensors = $query->paginate($request->get('per_page', 15));
        
        return response()->json([
            'success' => true,
            'data' => SensorResource::collection($sensors),
            'meta' => [
                'current_page' => $sensors->currentPage(),
                'last_page' => $sensors->lastPage(),
                'per_page' => $sensors->perPage(),
                'total' => $sensors->total(),
            ]
        ]);
    }
    
    /**
     * Créer un nouveau capteur
     */
    public function store(StoreSensorRequest $request): JsonResponse
    {
        $sensor = Sensor::create($request->validated());
        
        return response()->json([
            'success' => true,
            'message' => 'Capteur créé avec succès',
            'data' => new SensorResource($sensor->load('patient'))
        ], 201);
    }
    
    /**
     * Afficher un capteur spécifique
     */
    public function show(Sensor $sensor): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new SensorResource($sensor->load('patient'))
        ]);
    }
    
    /**
     * Mettre à jour un capteur
     */
    public function update(UpdateSensorRequest $request, Sensor $sensor): JsonResponse
    {
        $sensor->update($request->validated());
        
        return response()->json([
            'success' => true,
            'message' => 'Capteur mis à jour avec succès',
            'data' => new SensorResource($sensor->load('patient'))
        ]);
    }
    
    /**
     * Supprimer un capteur
     */
    public function destroy(Sensor $sensor): JsonResponse
    {
        $sensor->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Capteur supprimé avec succès'
        ]);
    }
    
    /**
     * Recevoir des données d'un capteur
     */
    public function receiveData(Request $request, Sensor $sensor): JsonResponse
    {
        $validated = $request->validate([
            'value' => 'required|numeric',
            'timestamp' => 'required|date',
            'raw_data' => 'nullable|array',
            'signal_strength' => 'nullable|integer',
            'battery_level' => 'nullable|integer|min:0|max:100',
            'status_code' => 'nullable|integer',
            'connection_type' => 'nullable|string',
            'latency' => 'nullable|integer|min:0',
        ]);
        
        // Créer la lecture
        $reading = $sensor->readings()->create($validated);
        
        // Mettre à jour le capteur
        $sensor->update([
            'last_reading' => $validated['value'],
            'last_reading_at' => $validated['timestamp'],
            'status' => 'active'
        ]);
        
        // Vérifier les seuils et générer des alertes si nécessaire
        $this->checkThresholds($sensor, $reading);
        
        return response()->json([
            'success' => true,
            'message' => 'Données reçues avec succès',
            'data' => new SensorReadingResource($reading)
        ], 201);
    }
    
    /**
     * Obtenir les données d'un capteur
     */
    public function getData(Request $request, Sensor $sensor): JsonResponse
    {
        $query = $sensor->readings();
        
        // Filtrage par période
        if ($request->has('from')) {
            $query->where('timestamp', '>=', $request->get('from'));
        }
        
        if ($request->has('to')) {
            $query->where('timestamp', '<=', $request->get('to'));
        }
        
        // Limitation du nombre de résultats
        $limit = $request->get('limit', 100);
        $readings = $query->orderBy('timestamp', 'desc')->limit($limit)->get();
        
        return response()->json([
            'success' => true,
            'data' => SensorReadingResource::collection($readings)
        ]);
    }
    
    /**
     * Vérifier les seuils et générer des alertes
     */
    private function checkThresholds(Sensor $sensor, SensorReading $reading): void
    {
        if (!$reading->isWithinThresholds()) {
            // Créer une alerte
            \App\Models\Alert::create([
                'title' => "Seuil dépassé - {$sensor->type}",
                'message' => "La valeur {$reading->value} dépasse les seuils autorisés pour le capteur {$sensor->sensor_id}",
                'severity' => $sensor->alert_level,
                'type' => 'sensor_threshold',
                'patient_id' => $sensor->patient_id,
                'data' => [
                    'capteur_id' => $sensor->id,
                    'reading_id' => $reading->id,
                    'value' => $reading->value,
                    'threshold_low' => $sensor->threshold_low,
                    'threshold_high' => $sensor->threshold_high,
                ]
            ]);
        }
    }
    
    /**
     * Obtenir les statistiques des capteurs
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total' => Sensor::count(),
            'actifs' => Sensor::where('active', true)->count(),
            'inactifs' => Sensor::where('active', false)->count(),
            'en_erreur' => Sensor::where('status', 'error')->count(),
            'par_type' => Sensor::selectRaw('type, COUNT(*) as count')
                               ->groupBy('type')
                               ->pluck('count', 'type'),
        ];
        
        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}