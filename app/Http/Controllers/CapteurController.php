<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Capteur;
use App\Models\User;
use App\Models\SensorReading;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class CapteurController extends Controller
{
    /**
     * Affiche la liste des capteurs.
     */
    public function index(Request $request)
    {
        $query = Capteur::with('patient');
        
        // Filtrage par type de capteur
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }
        
        // Filtrage par statut
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Recherche par ID ou modèle
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('capteur_id', 'like', "%$searchTerm%")
                  ->orWhere('model', 'like', "%$searchTerm%")
                  ->orWhereHas('patient', function($sq) use ($searchTerm) {
                      $sq->where('name', 'like', "%$searchTerm%");
                  });
            });
        }
        
        $capteurs = $query->latest()->paginate(10);
        
        // Calcul du nombre de capteurs avec batterie faible (simulation)
        $lowBatteryCount = rand(0, 5);  // À remplacer par une vraie logique
        
        return view('capteurs.index', compact('capteurs', 'lowBatteryCount'));
    }

    /**
     * Affiche le formulaire de création d'un capteur.
     */
    public function create()
    {
        $patients = User::whereHas('role', function ($query) {
            $query->where('nom', 'patient');
        })->get();
        return view('capteurs.create', compact('patients'));
    }

    /**
     * Enregistre un nouveau capteur.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'capteur_id' => 'required|string|unique:capteurs,capteur_id',
            'type' => 'required|string',
            'model' => 'required|string',
            'patient_id' => 'nullable|exists:users,id',
            'location' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Capteur::create($request->all());
        
        return redirect()->route('sensors.index')->with('success', 'Capteur ajouté avec succès.');
    }

    /**
     * Affiche les détails d'un capteur spécifique.
     */
    public function show(string $id)
    {
        $sensor = Capteur::with(['patient', 'readings' => function($query) {
            $query->latest()->take(100);
        }])->findOrFail($id);
        
        return view('capteurs.show', compact('sensor'));
    }

    /**
     * Affiche le formulaire d'édition d'un capteur.
     */
    public function edit(string $id)
    {
        $sensor = Capteur::findOrFail($id);
        $patients = User::whereHas('role', function ($query) {
            $query->where('nom', 'patient');
        })->get();
        
        return view('capteurs.edit', compact('sensor', 'patients'));
    }

    /**
     * Met à jour un capteur dans la base de données.
     */
    public function update(Request $request, string $id)
    {
        $sensor = Capteur::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'capteur_id' => 'required|string|unique:sensors,capteur_id,' . $sensor->id,
            'type' => 'required|string',
            'model' => 'required|string',
            'patient_id' => 'nullable|exists:users,id',
            'location' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $sensor->update($request->all());
        
        return redirect()->route('sensors.index')->with('success', 'Capteur mis à jour avec succès.');
    }

    /**
     * Supprime un capteur.
     */
    public function destroy(string $id)
    {
        $sensor = Capteur::findOrFail($id);
        $sensor->delete();
        
        return redirect()->route('sensors.index')->with('success', 'Capteur supprimé avec succès.');
    }

    /**
     * Configure les paramètres d'un capteur.
     */
    public function configure(string $id)
    {
        $sensor = Capteur::findOrFail($id);
        return view('capteurs.configure', compact('sensor'));
    }

    /**
     * Enregistre les configurations d'un capteur.
     */
    public function saveConfiguration(Request $request, string $id)
    {
        $sensor = Capteur::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'threshold_low' => 'nullable|numeric',
            'threshold_high' => 'nullable|numeric',
            'alert_level' => 'nullable|string|in:low,medium,high,critical',
            'interval' => 'nullable|integer|min:1',
            'active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $sensor->update([
            'threshold_low' => $request->threshold_low,
            'threshold_high' => $request->threshold_high,
            'alert_level' => $request->alert_level,
            'interval' => $request->interval,
            'active' => $request->has('active'),
        ]);
        
        return redirect()->route('sensors.show', $sensor->id)->with('success', 'Configuration du capteur mise à jour.');
    }

    /**
     * Affiche l'historique des lectures d'un capteur.
     */
    public function history(string $id)
    {
        $sensor = Capteur::findOrFail($id);
        $readings = $sensor->readings()->latest()->paginate(50);
        
        return view('capteurs.history', compact('sensor', 'readings'));
    }

    /**
     * Réinitialise un capteur.
     */
    public function reset(string $id)
    {
        $sensor = Capteur::findOrFail($id);
        $sensor->update([
            'last_reading' => null,
            'status' => 'ready',
        ]);
        
        return redirect()->route('sensors.show', $sensor->id)->with('success', 'Capteur réinitialisé avec succès.');
    }

    /**
     * Dissocie un capteur d'un patient.
     */
    public function unassign(string $id)
    {
        $sensor = Capteur::findOrFail($id);
        $sensor->update([
            'patient_id' => null,
        ]);
        
        return redirect()->route('sensors.show', $sensor->id)->with('success', 'Patient dissocié du capteur.');
    }
    
    /**
     * Assigner un capteur à un patient.
     */
    public function assign(Request $request, string $id)
    {
        // Récupérer le capteur par son ID
        $sensor = Capteur::findOrFail($id);

        if ($request->isMethod('get')) {
            $patients = User::whereHas('role', function ($query) {
                $query->where('nom', 'patient');
            })->get();
        
            return view('capteurs.assign', compact('sensor', 'patients'));
        }

        $validator = Validator::make($request->all(), [
        'patient_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $sensor->update([
            'patient_id' => $request->patient_id,
        ]);

        return redirect()->route('sensors.show', $sensor->id)
        ->with('success', 'Capteur assigné au patient avec succès.');
    }

    /**
     * Endpoint API pour recevoir les données du capteur en temps réel via WiFi.
     */
    public function apiReceiveData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'capteur_id' => 'required|string|exists:capteurs,capteur_id',
            'value' => 'required',
            'timestamp' => 'nullable|date',
            'raw_data' => 'nullable|array',
            'signal_strength' => 'nullable|integer',
            'battery_level' => 'nullable|integer|min:0|max:100',
            'status_code' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            Log::warning('Données de capteur invalides reçues:', [
                'errors' => $validator->errors()->toArray(),
                'data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $sensor = Capteur::where('capteur_id', $request->capteur_id)->first();
            
            if (!$sensor) {
                Log::warning('Capteur non trouvé:', ['capteur_id' => $request->sensor_id]);
                return response()->json(['success' => false, 'message' => 'Capteur non trouvé'], 404);
            }
            
            // Enregistrer la lecture
            $reading = $sensor->readings()->create([
                'value' => $request->value,
                'timestamp' => $request->timestamp ?? now(),
                'raw_data' => $request->raw_data,
                'signal_strength' => $request->signal_strength,
                'battery_level' => $request->battery_level,
                'status_code' => $request->status_code,
            ]);

            // Mettre à jour le capteur
            $sensorStatus = 'active';
            
            // Déterminer le statut du capteur en fonction de la batterie
            if ($request->has('battery_level')) {
                if ($request->battery_level <= 10) {
                    $sensorStatus = 'warning'; // Batterie faible
                }
            }
            
            // Déterminer le statut en fonction de la force du signal
            if ($request->has('signal_strength') && $request->signal_strength < -80) {
                $sensorStatus = 'warning'; // Signal faible
            }
            
            $sensor->update([
                'last_reading' => $request->value,
                'last_reading_at' => $request->timestamp ?? now(),
                'status' => $sensorStatus,
            ]);

            // Vérifier les seuils et générer des alertes si nécessaire
            if (!$reading->isWithinThresholds()) {
                if ($sensor->patient_id) {
                    $severity = $sensor->alert_level ?? 'medium';
                    
                    \App\Models\Alerte::create([
                        'title' => 'Valeur anormale détectée',
                        'message' => "Le capteur {$sensor->capteur_id} a détecté une valeur anormale: {$request->value}",
                        'severity' => $severity,
                        'type' => 'capteur',
                        'patient_id' => $sensor->patient_id,
                        'data' => json_encode([
                            'capteur_id' => $sensor->id,
                            'value' => $request->value,
                            'threshold_low' => $sensor->threshold_low,
                            'threshold_high' => $sensor->threshold_high,
                            'signal_strength' => $request->signal_strength,
                            'battery_level' => $request->battery_level,
                        ]),
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Données reçues avec succès',
                'reading_id' => $reading->id
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors du traitement des données du capteur:', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur interne du serveur',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Endpoint API pour obtenir la configuration d'un capteur.
     * Utilisé par les appareils Wi-Fi qui se connectent pour la première fois.
     */
    public function apiGetConfiguration(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'capteur_id' => 'required|string|exists:capteurs,capteur_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $sensor = Capteur::where('capteur_id', $request->capteur_id)->first();
        
        if (!$sensor) {
            return response()->json(['success' => false, 'message' => 'Capteur non trouvé'], 404);
        }
        
        // Retourner la configuration
        return response()->json([
            'success' => true,
            'config' => [
                'interval' => $sensor->interval, // intervalle d'envoi en secondes
                'threshold_low' => $sensor->threshold_low,
                'threshold_high' => $sensor->threshold_high,
                'active' => $sensor->active,
                'type' => $sensor->type,
                'patient_id' => $sensor->patient_id,
            ]
        ]);
    }

     /**
     * Affiche la page de configuration WiFi des capteurs.
     */
    public function wifiConfig()
    {
        return view('capteurs.wifi-config');
    }
    
    /**
     * Endpoint API pour enregistrer la configuration d'un capteur.
     */
    public function apiSaveConfiguration(Request $request, $id)
    {
        $sensor = Capteur::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'interval' => 'required|integer|min:1',
            'threshold_low' => 'nullable|numeric',
            'threshold_high' => 'nullable|numeric',
            'active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $sensor->update([
                'interval' => $request->interval,
                'threshold_low' => $request->threshold_low,
                'threshold_high' => $request->threshold_high,
                'active' => $request->active,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Configuration mise à jour avec succès',
                'sensor' => $sensor
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour de la configuration du capteur:', [
                'error' => $e->getMessage(),
                'sensor_id' => $id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de la configuration',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Endpoint API pour obtenir la liste des capteurs actifs.
     */
    public function apiGetSensors(Request $request)
    {
        try {
            $query = Capteur::with('patient');
            
            // Filtrer par état actif si demandé
            if ($request->has('active') && $request->active !== null) {
                $query->where('active', (bool)$request->active);
            }
            
            // Filtrer par type si spécifié
            if ($request->has('type') && $request->type) {
                $query->where('type', $request->type);
            }
            
            $sensors = $query->get();
            
            return response()->json([
                'success' => true,
                'sensors' => $sensors
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des capteurs:', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des capteurs',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}