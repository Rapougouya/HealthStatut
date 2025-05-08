<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SigneVital extends Model
{
    use HasFactory;

    protected $table = 'signes_vitaux';
    
    protected $fillable = [
        'patient_id', 'capteur_id', 'rythme_cardiaque', 'temperature',
        'pression_arterielle_systolique', 'pression_arterielle_diastolique',
        'saturation_oxygene', 'frequence_respiratoire', 'glucose', 'enregistre_a'
    ];

    protected $casts = [
        'enregistre_a' => 'datetime',
    ];

    // Relations
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
    
    public function capteur()
    {
        return $this->belongsTo(Capteur::class);
    }
    
    public function alertes()
    {
        return $this->hasMany(Alerte::class);
    }
    
    // Accesseurs
    public function getPressionArterielleAttribute()
    {
        return "{$this->pression_arterielle_systolique}/{$this->pression_arterielle_diastolique}";
    }
}
