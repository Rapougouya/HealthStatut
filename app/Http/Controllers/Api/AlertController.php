<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Http\Requests\Api\StoreAlertRequest;
use App\Http\Resources\AlertResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AlertController extends Controller
{
    /**
     * Lister toutes les alertes
     */
    public function index(Request $request): JsonResponse
    {
        $query = Alert::with(['patient', 'resolvedBy']);
        
        // Filtrage par statut
        if ($request->has('resolved')) {
            $query->where('resolved', $request->boolean('resolved'));
        }
        
        // Filtrage par sévérité
        if ($request->has('severity') && $request->get('severity') !== 'all') {
            $query->where('severity', $request->get('severity'));
        }
        
        // Filtrage par type
        if ($request->has('type') && $request->get('type') !== 'all') {
            $query->where('type', $request->get('type'));
        }
        
        // Filtrage par patient
        if ($request->has('patient_id') && $request->get('patient_id') !== 'all') {
            $query->where('patient_id', $request->get('patient_id'));
        }
        
        // Recherche
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }
        
        $alerts = $query->orderBy('created_at', 'desc')
                       ->paginate($request->get('per_page', 15));
        
        return response()->json([
            'success' => true,
            'data' => AlertResource::collection($alerts),
            'meta' => [
                'current_page' => $alerts->currentPage(),
                'last_page' => $alerts->lastPage(),
                'per_page' => $alerts->perPage(),
                'total' => $alerts->total(),
            ]
        ]);
    }
    
    /**
     * Créer une nouvelle alerte
     */
    public function store(StoreAlertRequest $request): JsonResponse
    {
        $alert = Alert::create($request->validated());
        
        return response()->json([
            'success' => true,
            'message' => 'Alerte créée avec succès',
            'data' => new AlertResource($alert->load(['patient', 'resolvedBy']))
        ], 201);
    }
    
    /**
     * Afficher une alerte spécifique
     */
    public function show(Alert $alert): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new AlertResource($alert->load(['patient', 'resolvedBy']))
        ]);
    }
    
    /**
     * Résoudre une alerte
     */
    public function resolve(Request $request, Alert $alert): JsonResponse
    {
        $alert->update([
            'resolved' => true,
            'resolved_at' => now(),
            'resolved_by' => auth()->id(),
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Alerte résolue avec succès',
            'data' => new AlertResource($alert->load(['patient', 'resolvedBy']))
        ]);
    }
    
    /**
     * Rouvrir une alerte
     */
    public function reopen(Alert $alert): JsonResponse
    {
        $alert->update([
            'resolved' => false,
            'resolved_at' => null,
            'resolved_by' => null,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Alerte rouverte avec succès',
            'data' => new AlertResource($alert->load(['patient', 'resolvedBy']))
        ]);
    }
    
    /**
     * Supprimer une alerte
     */
    public function destroy(Alert $alert): JsonResponse
    {
        $alert->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Alerte supprimée avec succès'
        ]);
    }
    
    /**
     * Obtenir les statistiques des alertes
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total' => Alert::count(),
            'actives' => Alert::where('resolved', false)->count(),
            'resolues' => Alert::where('resolved', true)->count(),
            'par_severite' => Alert::selectRaw('severity, COUNT(*) as count')
                                  ->groupBy('severity')
                                  ->pluck('count', 'severity'),
            'par_type' => Alert::selectRaw('type, COUNT(*) as count')
                              ->groupBy('type')
                              ->pluck('count', 'type'),
            'critiques_non_resolues' => Alert::where('severity', 'critical')
                                            ->where('resolved', false)
                                            ->count(),
        ];
        
        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
    
    /**
     * Marquer plusieurs alertes comme lues
     */
    public function markAsRead(Request $request): JsonResponse
    {
        $alertIds = $request->validate([
            'alert_ids' => 'required|array',
            'alert_ids.*' => 'exists:alerts,id'
        ])['alert_ids'];
        
        Alert::whereIn('id', $alertIds)->update([
            'resolved' => true,
            'resolved_at' => now(),
            'resolved_by' => auth()->id(),
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Alertes marquées comme résolues avec succès'
        ]);
    }
}