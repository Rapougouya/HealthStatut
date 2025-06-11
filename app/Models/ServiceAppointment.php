<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceAppointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'patient_id', 
        'doctor_id',
        'scheduled_at',
        'completed_at',
        'status',
        'notes'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Relation avec le service
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Relation avec le patient
     */
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * Relation avec le médecin
     */
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * Relation avec les résultats
     */
    public function results()
    {
        return $this->hasMany(ServiceResult::class);
    }
}
