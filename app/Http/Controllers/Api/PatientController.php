<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Http\Requests\Api\StorePatientRequest;
use App\Http\Requests\Api\UpdatePatientRequest;
use App\Http\Resources\PatientResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PatientController extends Controller
{
    /**
     * Afficher la liste des patients
     */
    public function index(Request $request): JsonResponse
    {
        $query = Patient::query();
        
        // Filtrage par recherche
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Filtrage par sexe
        if ($request->has('sexe') && $request->get('sexe') !== 'all') {
            $query->where('sexe', $request->get('sexe'));
        }
        
        // Tri
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        // Pagination
        $perPage = $request->get('per_page', 15);
        $patients = $query->paginate($perPage);
        
        return response()->json([
            'success' => true,
            'data' => PatientResource::collection($patients),
            'meta' => [
                'current_page' => $patients->currentPage(),
                'last_page' => $patients->lastPage(),
                'per_page' => $patients->perPage(),
                'total' => $patients->total(),
            ]
        ]);
    }
    
    /**
     * Créer un nouveau patient
     */
    public function store(StorePatientRequest $request): JsonResponse
    {
        $patient = Patient::create($request->validated());
        
        return response()->json([
            'success' => true,
            'message' => 'Patient créé avec succès',
            'data' => new PatientResource($patient)
        ], 201);
    }
    
    /**
     * Afficher un patient spécifique
     */
    public function show(Patient $patient): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new PatientResource($patient)
        ]);
    }
    
    /**
     * Mettre à jour un patient
     */
    public function update(UpdatePatientRequest $request, Patient $patient): JsonResponse
    {
        $patient->update($request->validated());
        
        return response()->json([
            'success' => true,
            'message' => 'Patient mis à jour avec succès',
            'data' => new PatientResource($patient)
        ]);
    }
    
    /**
     * Supprimer un patient
     */
    public function destroy(Patient $patient): JsonResponse
    {
        $patient->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Patient supprimé avec succès'
        ]);
    }
    
    /**
     * Obtenir les statistiques des patients
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total' => Patient::count(),
            'hommes' => Patient::where('sexe', 'M')->count(),
            'femmes' => Patient::where('sexe', 'F')->count(),
            'nouveaux_ce_mois' => Patient::whereMonth('created_at', now()->month)->count(),
        ];
        
        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}