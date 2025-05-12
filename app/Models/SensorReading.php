<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SensorReading extends Model
{
    use HasFactory;

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'capteur_id',
        'value',
        'timestamp',
        'raw_data',
        'signal_strength',
        'battery_level',
        'status_code',
        'connection_type',   // Type de connexion (WiFi, Bluetooth, etc.)
        'latency',
    ];

    /**
     * Les attributs à transformer en types natifs.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'value' => 'float',
        'timestamp' => 'datetime',
        'raw_data' => 'array',
        'signal_strength' => 'integer',
        'battery_level' => 'integer',
        'status_code' => 'integer',
        'latency' => 'integer',
    ];

    /**
     * Obtenir le capteur associé à cette lecture.
     */
    public function sensor()
    {
        return $this->belongsTo(Sensor::class);
    }

    /**
     * Vérifie si la valeur est dans les seuils acceptables.
     *
     * @return bool
     */
    public function isWithinThresholds()
    {
        $sensor = $this->sensor;
        
        if (!$sensor->threshold_low && !$sensor->threshold_high) {
            return true; // Pas de seuils définis
        }
        
        if ($sensor->threshold_low && $this->value < $sensor->threshold_low) {
            return false;
        }
        
        if ($sensor->threshold_high && $this->value > $sensor->threshold_high) {
            return false;
        }
        
        return true;
    }

    /**
     * Vérifie si la lecture a une bonne qualité de signal.
     *
     * @return bool
     */
    public function hasGoodSignal()
    {
        // Le signal est considéré bon si supérieur à -70 dBm (échelle typique: -30 à -90)
        return $this->signal_strength >= -70;
    }

    /**
     * Vérifie si la connexion est stable.
     *
     * @return bool
     */
    public function hasStableConnection()
    {
        // On considère que la connexion est stable si la latence est inférieure à 100ms
        return $this->latency < 100;
    }
}
