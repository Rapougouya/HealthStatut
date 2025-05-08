<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\SigneVital;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Importation de DB

class VitalSignsController extends Controller
{
    public function getPatientData(Patient $patient, Request $request)
    {
        $type = $request->type ?? 'rythme_cardiaque';
        $period = $request->period ?? 'day';
        
        $query = $patient->signesVitaux()
            ->select('id', $type, 'enregistre_a')
            ->orderBy('enregistre_a');
            
        switch ($period) {
            case 'day':
                $query->where('enregistre_a', '>=', now()->subDay());
                break;
                
            case 'week':
                $query->where('enregistre_a', '>=', now()->subWeek());
                break;
                
            case 'month':
                $query->where('enregistre_a', '>=', now()->subMonth());
                // Pour les données mensuelles, on agrège pour éviter trop de points
                return $this->aggregateMonthlyData($patient->id, $type);
                break;
        }
        
        $data = $query->get();
        
        return response()->json([
            'labels' => $data->map(function ($item) use ($period) {
                $format = $period === 'day' ? 'H:i' : 'd/m H:i';
                return $item->enregistre_a->format($format);
            }),
            'values' => $data->pluck($type)
        ]);
    }
    
    private function aggregateMonthlyData($patientId, $type)
    {
        // Agrégation des données par jour pour la vue mensuelle
        $data = SigneVital::where('patient_id', $patientId)
            ->where('enregistre_a', '>=', now()->subMonth())
            ->select(
                DB::raw('DATE(enregistre_a) as date'), // Utilisation de DB
                DB::raw("AVG({$type}) as average_value") // Utilisation de DB
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        return response()->json([
            'labels' => $data->map(function ($item) {
                return \Carbon\Carbon::parse($item->date)->format('d/m');
            }),
            'values' => $data->pluck('average_value')
        ]);
    }
    
    public function getSensorConfig(Request $request, $sensorId)
    {
        // Retourne la vue partielle avec le formulaire de configuration du capteur
        $capteur = \App\Models\Capteur::findOrFail($sensorId);
        $seuilsAlerte = $capteur->seuilsAlerte()->get();
        
        return view('patials.sensor-config', compact('capteur', 'seuilsAlerte'))
            ->render();
    }
}