<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category_id',
        'cost',
        'duration_minutes',
        'is_active'
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the category that owns the service.
     */
    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    /**
     * Get the users that belong to this service.
     */
    public function utilisateurs()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the patients that belong to this service.
     * Supposant que les patients sont des utilisateurs avec un rôle spécifique.
     */
    public function patients()
    {
        return $this->hasMany(User::class)->whereHas('role', function($query) {
            $query->where('nom', 'patient');
        });
    }

    /**
     * Get the sensors that belong to this service.
     */
    public function capteurs()
    {
        return $this->hasManyThrough(Capteur::class, User::class, 'service_id', 'patient_id');
    }

    /**
     * Relation many-to-many avec les médecins (doctors)
     */
    public function doctors()
    {
        return $this->belongsToMany(User::class, 'service_doctor', 'service_id', 'doctor_id')
                    ->withPivot('cost_override')
                    ->withTimestamps();
    }

    /**
     * Relation avec les rendez-vous de service
     */
    public function appointments()
    {
        return $this->hasMany(ServiceAppointment::class);
    }

    /**
     * Relation avec les résultats via les rendez-vous
     */
    public function results()
    {
        return $this->hasManyThrough(ServiceResult::class, ServiceAppointment::class, 'service_id', 'service_appointment_id');
    }
}