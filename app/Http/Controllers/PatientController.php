<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Capteur;
use App\Models\Service;
use App\Models\SigneVital;
use App\Models\Activite; // Importation du modèle Activite
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\Paginator; // Importation de Paginator (optionnel)

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $query = Patient::query();
        $patient = Patient::find(1);
        // Filtrage par recherche
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('numero_dossier', 'like', "%{$search}%");
            });
        }
        
        // Filtrage par service
        if ($request->has('service') && !empty($request->service)) {
            $query->where('service_id', $request->service);
        }
        
        // Filtrage par statut (basé sur les alertes)
        if ($request->has('statut') && !empty($request->statut)) {
            switch ($request->statut) {
                case 'critique':
                    $query->whereHas('alertes', function ($q) {
                        $q->where('severite', 'critique')
                          ->whereIn('statut', ['nouvelle', 'vue']);
                    });
                    break;
                    
                case 'attention':
                    $query->whereHas('alertes', function ($q) {
                        $q->where('severite', 'haute')
                          ->whereIn('statut', ['nouvelle', 'vue']);
                    });
                    break;
                    
                case 'normal':
                    $query->whereDoesntHave('alertes', function ($q) {
                        $q->whereIn('statut', ['nouvelle', 'vue']);
                    });
                    break;
            }
        }
        
        // Chargement des relations
        $query->with([
            'service',
            'signesVitaux' => function ($q) {
                $q->latest('enregistre_a')->take(1);
            },
            'alertesActives' => function ($q) {
                $q->whereIn('statut', ['nouvelle', 'vue'])->latest();
            }
        ]);
        
        // Pagination
        $patients = $query->paginate(12)->appends(request()->query());
        
        // Services pour le filtre
        $services = Service::all();

        $lastVitalUpdate = SigneVital::latest()->first();
        $vitalSigns = SigneVital::all();
        
        return view('patients.index', compact('patients', 'services', 'patient', 'lastVitalUpdate', 'vitalSigns'));

    }

    public function create()
{
    $services = Service::all();
    $sensors = Capteur::all(); // on ne filtre plus les capteurs

    return view('patients.create', compact('services', 'sensors'));
}


public function store(Request $request)
{
    $validated = $request->validate([
        'nom' => 'required|string|max:255',
        'prenom' => 'required|string|max:255',
        'date_naissance' => 'required|date',
        'sexe' => 'required|in:M,F,Autre',
        'adresse' => 'nullable|string|max:255',
        'telephone' => 'nullable|string|max:20',
        'email' => 'nullable|email|max:255',
        'numero_dossier' => 'required|string|max:100|unique:patients,numero_dossier',
        'taille' => 'nullable|numeric|min:0',
        'poids' => 'nullable|numeric|min:0',
        'service_id' => 'nullable|exists:services,id',
        'sensors' => 'required|array',
        'sensors.*' => 'exists:capteurs,id',
    ]);

    // Création du patient
    $patient = Patient::create([
        'nom' => $validated['nom'],
        'prenom' => $validated['prenom'],
        'date_naissance' => $validated['date_naissance'],
        'sexe' => $validated['sexe'],
        'adresse' => $validated['adresse'] ?? null,
        'telephone' => $validated['telephone'] ?? null,
        'email' => $validated['email'] ?? null,
        'numero_dossier' => $validated['numero_dossier'],
        'taille' => $validated['taille'] ?? null,
        'poids' => $validated['poids'] ?? null,
        'service_id' => $validated['service_id'] ?? null,
    ]);

    // Association des capteurs via la table pivot
    $patient->capteurs()->attach($validated['sensors']);

    return redirect()->route('patients.show', $patient)
        ->with('success', 'Patient ajouté avec succès.');
}
   


    public function edit(Patient $patient)
    {
        return view('patients.edit', compact('patient'));
    }

    public function liste()
{
    $patients = Patient::paginate(15);
    return view('patients.liste', compact('patients'));
}

    /**
     * Update the specified patient in storage.
     */
    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'date_naissance' => 'required|date',
            'sexe' => 'required|in:M,F',
            'adresse' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255'
        ]);

        $patient->update($validated);

        return redirect()->route('patients.show', $patient)
            ->with('success', 'Patient mis à jour avec succès.');
    }

    
    public function show(Patient $patient)
{
    // Charger les données nécessaires
    $patient->load([
        'service',
        'capteurs' => function($query) {
            $query->orderBy('statut', 'desc');
        },
        'alertes' => function($query) {
            $query->latest()->take(5);
        }
    ]);

    // Signes vitaux simulés (corrigé)
    $vitalSigns = [
        'heart_rate' => [
            'value' => rand(60, 100),
            'unit' => 'bpm',
            'alert' => false,
            'icon' => 'heart',
            'name' => 'Rythme cardiaque',
            'trend' => rand(-5, 5)
        ],
        'temperature' => [  
            'value' => number_format(36.5 + (rand(-5, 5)/10), 1),
            'unit' => '°C',
            'alert' => rand(0, 1),
            'icon' => 'temp',
            'name' => 'Température',
            'trend' => rand(-5, 5)
        ],
        'oxygen' => [
            'value' => rand(95, 100),
            'unit' => '%',
            'alert' => false,
            'icon' => 'oxygen',
            'name' => 'Saturation O₂',
            'trend' => rand(-2, 2)
        ],
        'blood_pressure' => [
            'value' => rand(110, 130) . '/' . rand(70, 85),
            'unit' => 'mmHg',
            'alert' => rand(0, 1),
            'icon' => 'blood-pressure',
            'name' => 'Pression artérielle',
            'trend' => null
        ]
    ];

    // Dernière mise à jour
    $lastVitalUpdate = now()->subMinutes(rand(5, 120));

    // Enregistrement activité
    Activite::create([
        'type' => 'patient',
        'icon' => 'view',
        'title' => 'Consultation dossier',
        'description' => 'Consultation de ' . $patient->prenom . ' ' . $patient->nom,
        'user_id' => auth()->id(),
    ]);

    return view('patients.show', compact('patient', 'vitalSigns', 'lastVitalUpdate'));
}
    private function prepareVitalSigns(Patient $patient)
    {
        $latestSigns = $patient->signesVitaux->first();
        
        if (!$latestSigns) {
            return [];
        }
        
        // Récupérer les signes vitaux précédents pour calculer les tendances
        $previousSigns = $patient->signesVitaux->skip(1)->first();
        
        $vitalSigns = [
            'heart_rate' => [
                'name' => 'Rythme cardiaque',
                'value' => $latestSigns->rythme_cardiaque,
                'unit' => 'bpm',
                'icon' => 'heart',
                'alert' => $latestSigns->rythme_cardiaque < 60 || $latestSigns->rythme_cardiaque > 100,
                'trend' => $previousSigns ? $this->calculateTrend($latestSigns->rythme_cardiaque, $previousSigns->rythme_cardiaque) : null
            ],
            'temperature' => [
                'name' => 'Température',
                'value' => $latestSigns->temperature,
                'unit' => '°C',
                'icon' => 'temp',
                'alert' => $latestSigns->temperature > 37.8,
                'trend' => $previousSigns ? $this->calculateTrend($latestSigns->temperature, $previousSigns->temperature) : null
            ],
            'oxygen' => [
                'name' => 'Saturation O₂',
                'value' => $latestSigns->saturation_oxygene,
                'unit' => '%',
                'icon' => 'oxygen',
                'alert' => $latestSigns->saturation_oxygene < 95,
                'trend' => $previousSigns ? $this->calculateTrend($latestSigns->saturation_oxygene, $previousSigns->saturation_oxygene) : null
            ],
            'blood_pressure' => [
                'name' => 'Pression artérielle',
                'value' => $latestSigns->pression_arterielle,
                'unit' => 'mmHg',
                'icon' => 'blood-pressure',
                'alert' => $latestSigns->pression_arterielle_systolique > 140 || $latestSigns->pression_arterielle_diastolique > 90,
                'trend' => null // Calculé différemment
            ],
            'respiratory' => [
                'name' => 'Fréq. respiratoire',
                'value' => $latestSigns->frequence_respiratoire,
                'unit' => 'rpm',
                'icon' => 'respiratory',
                'alert' => $latestSigns->frequence_respiratoire < 12 || $latestSigns->frequence_respiratoire > 20,
                'trend' => $previousSigns ? $this->calculateTrend($latestSigns->frequence_respiratoire, $previousSigns->frequence_respiratoire) : null
            ],
            'glucose' => [
                'name' => 'Glucose',
                'value' => $latestSigns->glucose,
                'unit' => 'mmol/L',
                'icon' => 'glucose',
                'alert' => $latestSigns->glucose < 3.9 || $latestSigns->glucose > 7.8,
                'trend' => $previousSigns ? $this->calculateTrend($latestSigns->glucose, $previousSigns->glucose) : null
            ]
        ];
        
        return $vitalSigns;
    }
    
    private function calculateTrend($current, $previous)
    {
        if (!$previous) return null;
        return round((($current - $previous) / $previous) * 100);
    }
    
    // Autres méthodes (create, store, edit, update) implémentées selon vos besoins
}