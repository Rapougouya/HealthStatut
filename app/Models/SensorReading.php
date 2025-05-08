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
    ];

    /**
     * Les attributs à transformer en types natifs.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'value' => 'float',
        'timestamp' => 'datetime',
    ];

    /**
     * Obtenir le capteur associé à cette lecture.
     */
    public function sensor()
    {
        return $this->belongsTo(Capteur::class, 'capteur_id');
    }
}
