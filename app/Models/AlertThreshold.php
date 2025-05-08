<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlertThreshold extends Model
{
    use HasFactory;

    protected $fillable = [
        'heart_rate_critical_min',
        'heart_rate_critical_max',
        'heart_rate_warning_min',
        'heart_rate_warning_max',
        'temperature_critical_min',
        'temperature_critical_max',
        'temperature_warning_min',
        'temperature_warning_max',
        'spo2_critical_min',
        'spo2_warning_min',
        'bp_sys_critical_min',
        'bp_sys_critical_max',
        'bp_sys_warning_min',
        'bp_sys_warning_max',
        'bp_dia_critical_min',
        'bp_dia_critical_max',
        'bp_dia_warning_min',
        'bp_dia_warning_max',
        'is_default',
        'user_id',
        'patient_id'
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'heart_rate_critical_min' => 'integer',
        'heart_rate_critical_max' => 'integer',
        'heart_rate_warning_min' => 'integer',
        'heart_rate_warning_max' => 'integer',
        'temperature_critical_min' => 'float',
        'temperature_critical_max' => 'float',
        'temperature_warning_min' => 'float',
        'temperature_warning_max' => 'float',
        'spo2_critical_min' => 'integer',
        'spo2_warning_min' => 'integer',
        'bp_sys_critical_min' => 'integer',
        'bp_sys_critical_max' => 'integer',
        'bp_sys_warning_min' => 'integer',
        'bp_sys_warning_max' => 'integer',
        'bp_dia_critical_min' => 'integer',
        'bp_dia_critical_max' => 'integer',
        'bp_dia_warning_min' => 'integer',
        'bp_dia_warning_max' => 'integer',
    ];

    /**
     * Obtenir l'utilisateur associé à ces seuils d'alerte.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtenir le patient associé à ces seuils d'alerte.
     */
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * Obtenir les seuils d'alerte par défaut
     */
    public static function getDefault()
    {
        return self::where('is_default', true)->first();
    }
}

