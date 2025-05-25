<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\AlertCreated;

class Alerte extends Model
{
    use HasFactory;

    protected $table = 'alertes';
    
    protected $fillable = [
        'user_id',
        'patient_id', 
        'capteur_id', 
        'signe_vital_id', 
        'type',
        'message', 
        'statut', 
        'severite', 
        'confirmee_par', 
        'confirmee_a'
    ];

    protected $casts = [
        'confirmee_a' => 'datetime',
    ];

    /**
     * The event map for the model.
     */
    protected $dispatchesEvents = [
        'created' => AlertCreated::class,
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
    
    public function signeVital()
    {
        return $this->belongsTo(SigneVital::class);
    }
    
    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'confirmee_par');
    }
    
    // Scopes
    public function scopeNonTraitees($query)
    {
        return $query->whereIn('statut', ['nouvelle', 'vue']);
    }
    
    public function scopeCritiques($query)
    {
        return $query->where('severite', 'critique');
    }
    
    public function scopeActive($query)
    {
        return $query->whereIn('statut', ['nouvelle', 'vue']);
    }

    public function scopeBySeverity($query, $severite)
    {
        return $query->where('severite', $severite);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    // ✅ Nouveau scope : search
    public function scopeSearch($query, $term)
    {
        $term = "%$term%";

        return $query->where(function ($query) use ($term) {
            $query->where('message', 'like', $term)
                  ->orWhere('type', 'like', $term)
                  ->orWhere('severite', 'like', $term)
                  ->orWhere('statut', 'like', $term);
            // Ajoute ici d'autres colonnes si besoin
        });
    }

    // Scopes
    public function scopeResolved($query)
    {
        return $query->where('statut', 'resolue');
    }

}
