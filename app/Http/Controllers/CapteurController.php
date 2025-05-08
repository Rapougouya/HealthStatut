<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Capteur;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class CapteurController extends Controller
{
    /**
     * Affiche la liste des capteurs.
     */
    public function index(Request $request)
{
    $query = Capteur::with('patients');
    
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
            $q->where('sensor_id', 'like', "%$searchTerm%")
              ->orWhere('model', 'like', "%$searchTerm%")
              ->orWhereHas('patients', function($sq) use ($searchTerm) {
                  $sq->where('name', 'like', "%$searchTerm%");
              });
        }); // <<== correction ici
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
        $patients = User::where('role', 'patient')->get();
        return view('capteurs.create', compact('patients'));
    }

    /**
     * Enregistre un nouveau capteur.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sensor_id' => 'required|string|unique:capteurs,sensor_id',
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
        $sensor = Capteur::with(['patients', 'readings' => function($query) {
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
        $patients = User::where('role', 'patients')->get();
        
        return view('capteurs.edit', compact('sensor', 'patients'));
    }

    /**
     * Met à jour un capteur dans la base de données.
     */
    public function update(Request $request, string $id)
    {
        $sensor = Capteur::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'sensor_id' => 'required|string|unique:capteurs,sensor_id,' . $sensor->id,
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
        return view('sensors.configure', compact('sensor'));
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
        $sensor = Capteur::findOrFail($id);
        
        // Si c'est une requête GET, afficher le formulaire d'assignation
        if ($request->isMethod('get')) {
            $patients = User::where('role', 'patients')->get();
            return view('capteurs.assign', compact('sensor', 'patients'));
        }
        
        // Sinon, traiter l'assignation (POST)
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
     * Endpoint API pour recevoir les données du capteur.
     */
    public function apiReceiveData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sensor_id' => 'required|string|exists:capteurs,sensor_id',
            'value' => 'required',
            'timestamp' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $sensor = Capteur::where('sensor_id', $request->sensor_id)->first();
        
        // Enregistrer la lecture
        $reading = $sensor->readings()->create([
            'value' => $request->value,
            'timestamp' => $request->timestamp ?? now(),
        ]);

        // Mettre à jour le capteur
        $sensor->update([
            'last_reading' => $request->value,
            'last_reading_at' => $request->timestamp ?? now(),
            'status' => 'active',
        ]);

        // Vérifier les seuils et générer des alertes si nécessaire
        if (
            ($sensor->threshold_high && $request->value > $sensor->threshold_high) || 
            ($sensor->threshold_low && $request->value < $sensor->threshold_low)
        ) {
            // Logique pour créer une alerte
            if ($sensor->patient_id) {
                $severity = $sensor->alert_level ?? 'medium';
                
                \App\Models\Alert::create([
                    'title' => 'Valeur anormale détectée',
                    'message' => "Le capteur {$sensor->sensor_id} a détecté une valeur anormale: {$request->value}",
                    'severity' => $severity,
                    'type' => 'sensor',
                    'patient_id' => $sensor->patient_id,
                    'data' => json_encode([
                        'sensor_id' => $sensor->id,
                        'value' => $request->value,
                        'threshold_low' => $sensor->threshold_low,
                        'threshold_high' => $sensor->threshold_high,
                    ]),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Data received successfully',
            'reading_id' => $reading->id
        ]);
    }
}