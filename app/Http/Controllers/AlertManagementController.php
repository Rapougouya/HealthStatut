<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\User;
use App\Services\AlertService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class AlertManagementController extends Controller
{
    protected $alertService;

    public function __construct(AlertService $alertService)
    {
        $this->alertService = $alertService;
    }

    /**
     * Obtenir les statistiques des alertes
     */
    public function getStats(): JsonResponse
    {
        $stats = [
            'total_alerts' => Alert::count(),
            'active_alerts' => Alert::where('resolved', false)->count(),
            'critical_alerts' => Alert::where('severity', 'critical')->where('resolved', false)->count(),
            'resolved_today' => Alert::where('resolved', true)
                                   ->whereDate('resolved_at', today())
                                   ->count(),
            'alerts_by_severity' => Alert::selectRaw('severity, COUNT(*) as count')
                                        ->where('resolved', false)
                                        ->groupBy('severity')
                                        ->pluck('count', 'severity'),
            'alerts_by_type' => Alert::selectRaw('type, COUNT(*) as count')
                                    ->where('resolved', false)
                                    ->groupBy('type')
                                    ->pluck('count', 'type'),
            'recent_alerts' => Alert::with('patient')
                                   ->where('resolved', false)
                                   ->orderBy('created_at', 'desc')
                                   ->limit(5)
                                   ->get()
                                   ->map(function($alert) {
                                       return [
                                           'id' => $alert->id,
                                           'title' => $alert->title,
                                           'severity' => $alert->severity,
                                           'patient_name' => $alert->patient->nom_complet,
                                           'created_at' => $alert->created_at->diffForHumans()
                                       ];
                                   })
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Obtenir les alertes en temps réel
     */
    public function getRealTimeAlerts(): JsonResponse
    {
        $alerts = Alert::with(['patient'])
                      ->where('resolved', false)
                      ->orderBy('severity', 'desc')
                      ->orderBy('created_at', 'desc')
                      ->limit(20)
                      ->get()
                      ->map(function($alert) {
                          return [
                              'id' => $alert->id,
                              'title' => $alert->title,
                              'message' => $alert->message,
                              'severity' => $alert->severity,
                              'type' => $alert->type,
                              'patient' => [
                                  'id' => $alert->patient->id,
                                  'name' => $alert->patient->nom_complet,
                                  'room' => $alert->patient->chambre ?? 'Non assigné'
                              ],
                              'created_at' => $alert->created_at->toISOString(),
                              'time_ago' => $alert->created_at->diffForHumans()
                          ];
                      });

        return response()->json([
            'success' => true,
            'data' => $alerts
        ]);
    }

    /**
     * Déclencher une alerte manuelle
     */
    public function triggerManualAlert(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'severity' => 'required|in:critical,high,medium,low',
            'patient_id' => 'required|exists:users,id',
            'additional_data' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        $patient = User::findOrFail($request->patient_id);
        
        $alert = $this->alertService->createManualAlert($request->validated(), $patient);

        return response()->json([
            'success' => true,
            'message' => 'Alerte manuelle créée avec succès',
            'data' => [
                'id' => $alert->id,
                'title' => $alert->title,
                'severity' => $alert->severity,
                'patient_name' => $patient->nom_complet
            ]
        ], 201);
    }

    /**
     * Tester le système d'alertes
     */
    public function testAlert(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:heart_rate,spo2,temperature,blood_pressure',
            'patient_id' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        $patient = User::findOrFail($request->patient_id);
        
        // Créer une alerte de test
        $testData = $this->getTestAlertData($request->type);
        
        $alert = Alert::create([
            'title' => $testData['title'],
            'message' => $testData['message'],
            'severity' => 'medium',
            'type' => 'test_' . $request->type,
            'patient_id' => $patient->id,
            'data' => ['test' => true, 'type' => $request->type]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Alerte de test créée avec succès',
            'data' => [
                'id' => $alert->id,
                'title' => $alert->title,
                'patient_name' => $patient->nom_complet,
                'note' => 'Ceci est une alerte de test - elle peut être résolue immédiatement'
            ]
        ], 201);
    }

    /**
     * Obtenir les données pour les alertes de test
     */
    private function getTestAlertData(string $type): array
    {
        $testData = [
            'heart_rate' => [
                'title' => 'Test - Fréquence cardiaque',
                'message' => 'Ceci est un test du système d\'alerte pour la fréquence cardiaque'
            ],
            'spo2' => [
                'title' => 'Test - Saturation en oxygène',
                'message' => 'Ceci est un test du système d\'alerte pour la SpO2'
            ],
            'temperature' => [
                'title' => 'Test - Température corporelle',
                'message' => 'Ceci est un test du système d\'alerte pour la température'
            ],
            'blood_pressure' => [
                'title' => 'Test - Tension artérielle',
                'message' => 'Ceci est un test du système d\'alerte pour la tension artérielle'
            ]
        ];

        return $testData[$type] ?? [
            'title' => 'Test - Alerte générique',
            'message' => 'Ceci est un test du système d\'alerte'
        ];
    }
}