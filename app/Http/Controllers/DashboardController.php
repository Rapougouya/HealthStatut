<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Alerte;
use App\Models\Capteur;
use App\Models\Activite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
      
    
       return view('patients.dashboard');
    }
    
    private function calculateTrend($entity)
    {
        // Calcul des tendances (comparaison avec la période précédente)
        switch ($entity) {
            case 'patients':
                $currentCount = Patient::where('created_at', '>=', now()->startOfMonth())->count();
                $previousCount = Patient::where('created_at', '>=', now()->subMonth()->startOfMonth())
                    ->where('created_at', '<', now()->startOfMonth())
                    ->count();
                break;
                
            case 'alertes':
                $currentCount = Alerte::where('created_at', '>=', now()->startOfDay())->count();
                $previousCount = Alerte::where('created_at', '>=', now()->subDay()->startOfDay())
                    ->where('created_at', '<', now()->startOfDay())
                    ->count();
                break;
                
            case 'critical':
                $currentCount = $this->getCriticalPatientsCount();
                $previousCount = $this->getCriticalPatientsCount(true);
                // Retourner une différence absolue plutôt qu'un pourcentage
                return $currentCount - $previousCount;
                
            case 'capteurs':
                $currentCount = Capteur::where('statut', 'actif')
                    ->where('created_at', '>=', now()->startOfMonth())
                    ->count();
                $previousCount = Capteur::where('statut', 'actif')
                    ->where('created_at', '>=', now()->subMonth()->startOfMonth())
                    ->where('created_at', '<', now()->startOfMonth())
                    ->count();
                break;
                
            default:
                return 0;
        }
        
        if ($previousCount == 0) {
            return $currentCount > 0 ? 100 : 0;
        }
        
        return round((($currentCount - $previousCount) / $previousCount) * 100);
    }
    
    private function getCriticalPatientsCount($yesterday = false)
    {
        $query = Patient::whereHas('alertes', function ($query) use ($yesterday) {
            $query->where('severite', 'critique')
                ->whereIn('statut', ['nouvelle', 'vue']);
                
            if ($yesterday) {
                $query->where('created_at', '>=', now()->subDay()->startOfDay())
                    ->where('created_at', '<', now()->startOfDay());
            } else {
                $query->where('created_at', '>=', now()->startOfDay());
            }
        });
        
        return $query->distinct()->count();
    }
    
    private function getPatientsWithAlerts()
    {
        return Patient::whereHas('alertes', function ($query) {
            $query->whereIn('statut', ['nouvelle', 'vue']);
        })
        ->with(['alertesActives' => function ($query) {
            $query->with('capteur')
                ->whereIn('statut', ['nouvelle', 'vue'])
                ->latest();
        }])
        ->withCount(['alertesActives as severiteMax' => function ($query) {
            $query->select(DB::raw('MAX(CASE
                WHEN severite = "critique" THEN 3
                WHEN severite = "haute" THEN 2
                WHEN severite = "moyenne" THEN 1
                ELSE 0
                END)'));
        }])
        ->orderByDesc('severiteMax')
        ->take(4)
        ->get()
        ->map(function ($patient) {
            // Transformer la valeur numérique de severiteMax en texte
            $severityMap = [
                3 => 'critique',
                2 => 'warning',
                1 => 'moderate',
                0 => 'normal'
            ];
            
            $patient->severiteMax = $severityMap[$patient->severiteMax] ?? 'normal';
            $patient->derniereAlerte = $patient->alertesActives->first()->created_at;
            
            return $patient;
        });
    }
}
