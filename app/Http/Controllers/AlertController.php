<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alerte;
use App\Models\AlertSetting;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AlertController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Récupérer les filtres depuis la requête
        $severity = $request->input('severity', 'all');
        $type = $request->input('type', 'all');
        $patientId = $request->input('patient_id', 'all');
        $search = $request->input('search');

        // Récupérer les alertes actives avec filtres
        $activeAlerts = Alerte::active()
            ->bySeverity($severity)
            ->byType($type)
            ->byPatient($patientId)
            ->search($search)
            ->with('patient')
            ->orderBy('created_at', 'desc')
            ->get();

        // Récupérer les patients pour le filtre
        $patients = User::whereHas('role', function ($query) {
            $query->where('nom', 'patient');
        })->get();
        $alertes = Alerte::with(['patient', 'capteur', 'signeVital', 'utilisateur'])->get();

        // Récupérer les compteurs
        $activeAlertsCount = Alerte::active()->count();
        $resolvedAlertsCount = Alerte::resolved()->count();

        return view('alertes.index', compact(
            'activeAlerts', 
            'activeAlertsCount', 
            'resolvedAlertsCount',
            'patients',
            'alertes'
        ));
    }

    /**
     * Afficher l'historique des alertes.
     */
    public function history(Request $request)
    {
        $dateFrom = $request->input('date_from', Carbon::now()->subMonth()->toDateString());
        $dateTo = $request->input('date_to', Carbon::now()->toDateString());
        $type = $request->input('type', 'all');

        // Ajouter un jour à dateTo pour inclure les alertes du jour sélectionné
        $dateToEnd = Carbon::parse($dateTo)->addDay();

        $historyAlerts = Alerte::resolved()
            ->whereBetween('resolved_at', [$dateFrom, $dateToEnd])
            ->byType($type)
            ->with(['patient', 'resolvedBy'])
            ->orderBy('resolved_at', 'desc')
            ->paginate(15);

        $currentHistoryPage = $historyAlerts->currentPage();
        $totalHistoryPages = $historyAlerts->lastPage();

        return view('alertes.history', compact(
            'historyAlerts', 
            'currentHistoryPage', 
            'totalHistoryPages',
            'dateFrom',
            'dateTo',
            'type'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $patients = User::where('role', 'patient')->get();
        return view('alertes.create', compact('patients'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'severity' => 'required|in:critical,high,medium,low',
            'type' => 'required|string',
            'patient_id' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $alert = Alerte::create([
            'title' => $request->title,
            'message' => $request->message,
            'severite' => $request->severity,  // map bien ici
            'type' => $request->type,
            'patient_id' => $request->patient_id,
            'statut' => 'nouvelle',  // ajoute ça par défaut
            'settings' => $request->data ? json_decode($request->data, true) : null
        ]);

        return redirect()->route('alertes.index')
            ->with('success', 'Alerte créée avec succès');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $alert = Alerte::with(['patient', 'resolvedBy'])->findOrFail($id);
        return view('alertes.show', compact('alert'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $alert = Alerte::findOrFail($id);
        $patients = User::where('role', 'patient')->get();
        
        return view('alertes.edit', compact('alert', 'patients'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'severity' => 'required|in:critical,high,medium,low',
            'type' => 'required|string',
            'patient_id' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $alert = Alerte::findOrFail($id);
        $alert->update([
            'title' => $request->title,
            'message' => $request->message,
            'severity' => $request->severity,
            'type' => $request->type,
            'patient_id' => $request->patient_id,
            'data' => $request->data ? json_decode($request->data, true) : null
        ]);

        return redirect()->route('alertes.index')
            ->with('success', 'Alerte mise à jour avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $alert = Alerte::findOrFail($id);
        $alert->delete();

        return redirect()->route('alertes.index')
            ->with('success', 'Alerte supprimée avec succès');
    }

    /**
    * Get and save alert settings.
    */
    public function settings()
{
    $settings = Alerte::where('user_id', Auth::id())->first();
    
    // Crée un enregistrement vide si aucun n'existe
    if (!$settings) {
        $settings = new Alerte();
        $settings->user_id = Auth::id();
        $settings->save();
    }
    
    return view('alertes.settings', compact('settings'));
}

private function getDefaultSettings()
{
    return [
        'heart_rate_low' => 60,
        'heart_rate_high' => 100,
        'notify_email' => true,
        // ... Tous vos paramètres avec valeurs par défaut
    ];
}

    /**
     * Save alert settings.
     */
    public function saveSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'heart_rate_low' => 'required|integer|min:40|max:100',
            'heart_rate_high' => 'required|integer|min:60|max:180|gt:heart_rate_low',
            'heart_rate_severity' => 'required|in:critical,high,medium,low',
            'spo2_low' => 'required|integer|min:80|max:100',
            'spo2_severity' => 'required|in:critical,high,medium,low',
            'temp_low' => 'required|numeric|min:34.0|max:37.0',
            'temp_high' => 'required|numeric|min:37.0|max:42.0|gt:temp_low',
            'temp_severity' => 'required|in:critical,high,medium,low',
            'bp_sys_low' => 'required|integer|min:70|max:120',
            'bp_sys_high' => 'required|integer|min:120|max:200|gt:bp_sys_low',
            'bp_dia_low' => 'required|integer|min:40|max:80',
            'bp_dia_high' => 'required|integer|min:80|max:120|gt:bp_dia_low',
            'bp_severity' => 'required|in:critical,high,medium,low',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        
        // Gérer les checkboxes qui ne sont pas envoyées si elles ne sont pas cochées
        $data['notify_email'] = $request->has('notify_email');
        $data['notify_sms'] = $request->has('notify_sms');
        $data['notify_app'] = $request->has('notify_app');
        $data['notify_critical_only'] = $request->has('notify_critical_only');
        $data['user_id'] = Auth::id();

        Alerte::updateOrCreate(
            ['user_id' => Auth::id()],
            $data
        );

        return redirect()->route('alertes.settings')
            ->with('success', 'Paramètres d\'alerte enregistrés avec succès');
    }


    /**
     * Mark an alert as resolved.
     */
    public function resolve(string $id)
    {
        $alert = Alerte::findOrFail($id);
        
        if (!$alert->resolved) {
            $alert->update([
                'resolved' => true,
                'resolved_at' => now(),
                'resolved_by' => Auth::id()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Alerte résolue avec succès'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Cette alerte a déjà été résolue'
        ]);
    }

    /**
     * Mark all active alerts as resolved.
     */
    public function resolveAll()
    {
        Alerte::active()->update([
            'resolved' => true,
            'resolved_at' => now(),
            'resolved_by' => Auth::id()
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Toutes les alertes ont été résolues'
        ]);
    }

    /**
     * Reset alert settings to defaults.
     */
    public function resetSettings()
    {
        $settings = Alerte::where('user_id', Auth::id())->first();
        if ($settings) {
            $settings->delete();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Paramètres réinitialisés avec succès'
        ]);
    }

    /**
     * Contact a patient about an alert.
     */
    public function contactPatient(string $id)
    {
        $alert = Alerte::with('patient')->findOrFail($id);
        
        // Ici, vous pourriez implémenter l'envoi d'un email, SMS, ou notification
        // Pour l'instant, retournons juste une réponse JSON

        return response()->json([
            'success' => true,
            'message' => 'Demande de contact pour le patient ' . $alert->patient->name
        ]);
    }

    /**
     * API pour créer une nouvelle alerte.
     */
    public function apiStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'severity' => 'required|in:critical,high,medium,low',
            'type' => 'required|string',
            'patient_id' => 'required|exists:users,id',
            'api_key' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Vérifier l'API key (à implémenter selon votre logique)
        // ...

        $alert = Alerte::create([
            'title' => $request->title,
            'message' => $request->message,
            'severity' => $request->severity,
            'type' => $request->type,
            'patient_id' => $request->patient_id,
            'data' => $request->data ? json_decode($request->data, true) : null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Alerte créée avec succès',
            'alert' => $alert
        ]);
    }
}