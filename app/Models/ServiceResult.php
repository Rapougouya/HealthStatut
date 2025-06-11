<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_appointment_id',
        'result_data',
        'attachments',
        'created_by'
    ];

    protected $casts = [
        'result_data' => 'array',
    ];

    /**
     * Relation avec le rendez-vous de service
     */
    public function appointment()
    {
        return $this->belongsTo(ServiceAppointment::class, 'service_appointment_id');
    }

    /**
     * Relation avec l'utilisateur qui a créé le résultat
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relation avec le service via le rendez-vous
     */
    public function service()
    {
        return $this->hasOneThrough(Service::class, ServiceAppointment::class, 'id', 'id', 'service_appointment_id', 'service_id');
    }
}