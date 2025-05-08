<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rapport extends Model
{
    use HasFactory;

    protected $table = 'rapports';
    
    protected $fillable = [
        'nom', 'type', 'patient_id', 'cree_par', 'donnees'
    ];

    protected $casts = [
        'donnees' => 'json',
    ];

    // Relations
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
    
    public function createur()
    {
        return $this->belongsTo(Utilisateur::class, 'cree_par');
    }
}
