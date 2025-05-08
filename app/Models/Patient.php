<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Service; // Importation du modèle Service
use App\Models\Capteur;
use App\Models\Laboratoire;
use App\Models\Prescription;
use App\Models\Note;
use App\Models\Alergie;
use App\Models\Antecedent;


class Patient extends Model
{
    use HasFactory;

    protected $table = 'patients';
    
    protected $fillable = [
        'nom', 'prenom', 'date_naissance', 'sexe', 'adresse', 
        'telephone', 'email', 'numero_dossier', 'taille', 
        'poids', 'service_id'
    ];

    protected $casts = [
        'date_naissance' => 'date',
    ];

    // app/Models/Patient.php

    public function alertesActives()
    {
        return $this->hasMany(Alerte::class)->where('statut', 'actif'); // ou 'active', selon ton champ
    }

    // Relations
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function capteurs()
    {
        return $this->belongsToMany(Capteur::class, 'capteur_patient')
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
    
    public function rapports()
    {
        return $this->hasMany(Rapport::class);
    }
    
    public function seuilsAlerte()
    {
        return $this->hasMany(Alerte::class);
    }
    
    public function prescriptions()
    {
        // Retourne toutes les prescriptions d'un patient
        return $this->hasMany(Prescription::class);
    }

    public function laboratoires()
    {
        // Retourne tous les laboratoires associés à ce patient
        return $this->hasMany(Laboratoire::class);
    }

    public function antecedents()
    {
        // Retourne tous les antécédents d'un patient
        return $this->hasMany(Antecedent::class);
    }

    public function allergies()
    {
        // Retourne toutes les allergies d'un patient
        return $this->hasMany(Alergie::class);
    }

    public function notes()
    {
        // Retourne toutes les notes d'un patient
        return $this->hasMany(Note::class);
    }

    // Accesseurs (getters)
    public function getNomCompletAttribute()
    {
        return "{$this->prenom} {$this->nom}";
    }
    
    public function getAgeAttribute()
    {
        return $this->date_naissance->age;
    }

    public function getAge()
{
    return $this->date_naissance->diffInYears(now());
}

}