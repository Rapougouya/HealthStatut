<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Alerte;
use App\Models\Capteur;
use App\Models\SensorReading;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HistoryController extends Controller
{
    /**
     * Afficher la page d'historique et statistiques.
     */
    public function index(Request $request)
    {
        // Récupérer les filtres depuis la requête
        $dateFrom = $request->input('date_from', Carbon::now()->subMonth()->toDateString());
        $dateTo = $request->input('date_to', Carbon::now()->toDateString());
        $patientId = $request->input('patient_id');
        $view = $request->input('view', 'daily');
        
        // Récupérer les patients pour le filtre
        $patients = User::where('role_id', 4)->get(); // Rôle patient
        
        // Récupérer les données pour la période sélectionnée
        $readings = SensorReading::whereHas('sensor', function($query) use ($patientId) {
                if ($patientId) {
                    $query->where('patient_id', $patientId);
                }
            })
            ->whereBetween('timestamp', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->orderBy('timestamp', 'desc')
            ->paginate(15);
        
        // Préparer les données pour les graphiques selon la vue sélectionnée
        $chartData = $this->prepareChartData($patientId, $dateFrom, $dateTo, $view);
        
        // Calculer les statistiques
        $stats = $this->calculateStats($patientId, $dateFrom, $dateTo);
        
        return view('historiques.index', compact(
            'dateFrom',
            'dateTo',
            'patientId',
            'view',
            'chartData',
            'patients',
            'readings',
            'stats'
        ));
    }
    
    /**
     * Exporter les données en CSV
     */
    public function export(Request $request)
    {
        $dateFrom = $request->input('date_from', Carbon::now()->subMonth()->toDateString());
        $dateTo = $request->input('date_to', Carbon::now()->toDateString());
        $patientId = $request->input('patient_id');
        $type = $request->input('type', 'all'); // Type de données à exporter: all, heartrate, spo2, etc.
        
        // Récupérer les données pour l'export
        $query = SensorReading::with('sensor')
            ->whereHas('sensor', function($query) use ($patientId, $type) {
                if ($patientId) {
                    $query->where('patient_id', $patientId);
                }
                if ($type && $type != 'all') {
                    $query->where('type', $type);
                }
            })
            ->whereBetween('timestamp', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->orderBy('timestamp', 'desc');
            
        // Logique d'export CSV (à implémenter)
        $filename = 'historique-donnees-' . Carbon::now()->format('Y-m-d') . '.csv';
        
        // Pour l'instant, redirigez avec un message
        return redirect()->route('history.index')
            ->with('success', 'Export des données en cours de développement.');
    }
    
    /**
     * Afficher les détails d'une période spécifique
     */
    public function details(Request $request, $patientId = null, $date = null)
    {
        // Si la date n'est pas spécifiée, utiliser aujourd'hui
        if (!$date) {
            $date = Carbon::now()->toDateString();
        }
        
        // Récupérer les données du patient pour cette date
        $readings = SensorReading::whereHas('sensor', function($query) use ($patientId) {
                if ($patientId) {
                    $query->where('patient_id', $patientId);
                }
            })
            ->whereBetween('timestamp', [$date . ' 00:00:00', $date . ' 23:59:59'])
            ->orderBy('timestamp')
            ->get();
        
        // Récupérer les alertes du patient pour cette date
        $alerts = Alerte::where('patient_id', $patientId)
            ->whereBetween('created_at', [$date . ' 00:00:00', $date . ' 23:59:59'])
            ->orderBy('created_at')
            ->get();
        
        // Récupérer les infos du patient
        $patient = $patientId ? User::find($patientId) : null;
        
        return view('history.details', compact('readings', 'alerts', 'patient', 'date'));
    }
    
    /**
     * Préparer les données pour les graphiques
     */
    private function prepareChartData($patientId, $dateFrom, $dateTo, $view)
    {
        // Formater les données selon la vue (quotidienne, hebdomadaire, mensuelle)
        $format = $view == 'daily' ? 'Y-m-d H' : ($view == 'weekly' ? 'Y-m-d' : 'Y-m');
        $groupBy = $view == 'daily' ? 'hour' : ($view == 'weekly' ? 'day' : 'month');
        
        // Pour l'instant, retournez des données simulées
        // Dans un environnement réel, vous récupéreriez ces données de votre base de données
        
        return [
            'heartRate' => [
                'labels' => ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
                'data' => [75, 78, 82, 79, 76, 80, 77]
            ],
            'spo2' => [
                'labels' => ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
                'data' => [97, 98, 96, 97, 98, 97, 96]
            ],
            'temperature' => [
                'labels' => ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
                'data' => [36.8, 36.9, 37.1, 37.0, 36.7, 36.8, 36.9]
            ],
            'bloodPressure' => [
                'labels' => ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
                'systolic' => [125, 130, 128, 135, 127, 126, 129],
                'diastolic' => [80, 82, 79, 84, 78, 80, 81]
            ]
        ];
    }
    
    /**
     * Calculer les statistiques des données
     */
    private function calculateStats($patientId, $dateFrom, $dateTo)
    {
        // Dans un environnement réel, calculer ces statistiques à partir des données réelles
        return [
            'heartRate' => [
                'avg' => 78,
                'min' => 62,
                'max' => 95
            ],
            'spo2' => [
                'avg' => 97,
                'min' => 94,
                'max' => 99
            ],
            'temperature' => [
                'avg' => 37.1,
                'min' => 36.5,
                'max' => 37.8
            ],
            'bloodPressure' => [
                'avg' => '125/80',
                'min' => '115/75',
                'max' => '145/90'
            ]
        ];
    }
}