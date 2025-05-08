<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
        'service_id',
        'role_id',
        'statut',
        'derniere_connexion',
        'theme',
        'compact_mode',
        'items_per_page',
        'notif_alertes_critiques',
        'notif_alertes_hautes',
        'notif_alertes_moyennes',
        'email_alertes_critiques',
        'email_rapports_quotidiens',
        'email_mises_a_jour',
        'avatar'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'derniere_connexion' => 'datetime',
        'notif_alertes_critiques' => 'boolean',
        'notif_alertes_hautes' => 'boolean',
        'notif_alertes_moyennes' => 'boolean',
        'email_alertes_critiques' => 'boolean',
        'email_rapports_quotidiens' => 'boolean',
        'email_mises_a_jour' => 'boolean',
        'compact_mode' => 'boolean',
    ];

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getNomCompletAttribute()
    {
        return "{$this->prenom} {$this->nom}";
    }

    /**
     * Get the service that the user belongs to.
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the role that the user has.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Determine if the user is an admin.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role === 'admin'; // Modifie selon ta logique
    }

    /**
     * Get the alert settings for the user.
     */
    //public function alertSettings()
    //{
        //return $this->hasOne(AlertSetting::class);
    //}

    /**
     * Get the alert thresholds for the user.
     */
    public function alertThresholds()
    {
        return $this->hasMany(AlertThreshold::class);
    }
}
