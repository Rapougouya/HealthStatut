<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Jobs\ProcessSensorDataJob;

class SensorReading extends Model
{
    use HasFactory;

    protected $fillable = [
        'capteur_id',
        'type',
        'value',
        'unit',
        'battery_level',
        'signal_strength',
        'raw_data',
        'metadata',
        'is_valid',
        'error_message',
        'measured_at'
    ];

    protected $casts = [
        'raw_data' => 'array',
        'metadata' => 'array',
        'is_valid' => 'boolean',
        'battery_level' => 'decimal:2',
        'measured_at' => 'datetime'
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::created(function (SensorReading $reading) {
            // Traitement asynchrone des données du capteur
            ProcessSensorDataJob::dispatch($reading);
        });
    }

    /**
     * Get the sensor that owns the reading.
     */
    public function sensor()
    {
        return $this->belongsTo(Capteur::class, 'capteur_id');
    }

    /**
     * Scope pour filtrer par type de capteur
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope pour filtrer les lectures valides
     */
    public function scopeValid($query)
    {
        return $query->where('is_valid', true);
    }

    /**
     * Scope pour filtrer par période
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('measured_at', [$startDate, $endDate]);
    }

    /**
     * Obtenir la valeur numérique si possible
     */
    public function getNumericValueAttribute()
    {
        if (is_numeric($this->value)) {
            return (float) $this->value;
        }
        
        // Pour la tension artérielle (format "120/80")
        if ($this->type === 'blood_pressure' && strpos($this->value, '/') !== false) {
            $parts = explode('/', $this->value);
            return [
                'systolic' => (int) $parts[0],
                'diastolic' => (int) $parts[1]
            ];
        }
        
        return null;
    }

    /**
     * Vérifier si la lecture est dans les limites normales
     */
    public function isWithinNormalRange(): bool
    {
        $normalRanges = [
            'heart_rate' => ['min' => 60, 'max' => 100],
            'spo2' => ['min' => 95, 'max' => 100],
            'temperature' => ['min' => 36.0, 'max' => 37.5],
        ];

        if (!isset($normalRanges[$this->type])) {
            return true; // Pas de plage définie
        }

        $numericValue = $this->numeric_value;
        if ($numericValue === null) {
            return true;
        }

        $range = $normalRanges[$this->type];
        return $numericValue >= $range['min'] && $numericValue <= $range['max'];
    }
}