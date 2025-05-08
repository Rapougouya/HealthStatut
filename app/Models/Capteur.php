<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Capteur extends Model
{
    use HasFactory;

    protected $table = 'capteurs';
    
    protected $fillable = [
        'nom', 'type', 'numero_serie', 'modele', 'statut',
        'patient_id', 'derniere_maintenance', 'niveau_batterie', 'adresse_mac'
    ];

    protected $casts = [
        'derniere_maintenance' => 'date',
    ];

    // Relations
    public function patients()
    {
        return $this->belongsToMany(
            Patient::class,
            'capteur_patient',
            'capteur_id',   // clé étrangère du capteur dans la table pivot
            'patient_id'    // clé étrangère du patient dans la table pivot
        )
        ->withPivot('associe_a', 'dissocie_a', 'actif')
        ->withTimestamps();
    }

    
    public function signesVitaux()
    {
        return $this->hasMany(SigneVital::class);
    }
    
    public function alertes()
    {
        return $this->hasMany(Alerte::class);
    }
    
    public function seuilsAlerte()
    {
        return $this->hasMany(Alerte::class);
    }
    
    // Accesseurs et mutateurs
    public function estActif()
    {
        return $this->statut === 'actif';
    }
    
    public function derniereMesure()
    {
        return $this->signesVitaux()->latest('enregistre_a')->first();
    }
}
