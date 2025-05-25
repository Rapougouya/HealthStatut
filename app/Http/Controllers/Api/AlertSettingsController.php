<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AlertSetting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class AlertSettingsController extends Controller
{
    /**
     * Obtenir les paramètres d'alerte de l'utilisateur.
     */
    public function show(): JsonResponse
    {
        $settings = AlertSetting::where('user_id', auth()->id())->first();
        
        if (!$settings) {
            // Retourner les valeurs par défaut
            $settings = new AlertSetting([
                'heart_rate_low' => 60,
                'heart_rate_high' => 100,
                'heart_rate_severity' => 'high',
                'spo2_low' => 94,
                'spo2_severity' => 'critical',
                'temp_low' => 36.0,
                'temp_high' => 38.0,
                'temp_severity' => 'high',
                'bp_sys_low' => 90,
                'bp_sys_high' => 140,
                'bp_dia_low' => 60,
                'bp_dia_high' => 90,
                'bp_severity' => 'medium',
                'notify_email' => true,
                'notify_sms' => true,
                'notify_app' => true,
                'notify_critical_only' => false,
            ]);
        }
        
        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }
    
    /**
     * Mettre à jour les paramètres d'alerte.
     */
    public function update(Request $request): JsonResponse
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
            'notify_email' => 'boolean',
            'notify_sms' => 'boolean',
            'notify_app' => 'boolean',
            'notify_critical_only' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $data['user_id'] = auth()->id();

        $settings = AlertSetting::updateOrCreate(
            ['user_id' => auth()->id()],
            $data
        );

        return response()->json([
            'success' => true,
            'message' => 'Paramètres d\'alerte mis à jour avec succès',
            'data' => $settings
        ]);
    }
    
    /**
     * Réinitialiser les paramètres aux valeurs par défaut.
     */
    public function reset(): JsonResponse
    {
        $defaultSettings = [
            'heart_rate_low' => 60,
            'heart_rate_high' => 100,
            'heart_rate_severity' => 'high',
            'spo2_low' => 94,
            'spo2_severity' => 'critical',
            'temp_low' => 36.0,
            'temp_high' => 38.0,
            'temp_severity' => 'high',
            'bp_sys_low' => 90,
            'bp_sys_high' => 140,
            'bp_dia_low' => 60,
            'bp_dia_high' => 90,
            'bp_severity' => 'medium',
            'notify_email' => true,
            'notify_sms' => true,
            'notify_app' => true,
            'notify_critical_only' => false,
            'user_id' => auth()->id(),
        ];

        $settings = AlertSetting::updateOrCreate(
            ['user_id' => auth()->id()],
            $defaultSettings
        );

        return response()->json([
            'success' => true,
            'message' => 'Paramètres réinitialisés aux valeurs par défaut',
            'data' => $settings
        ]);
    }
}