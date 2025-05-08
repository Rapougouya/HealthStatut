<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parametre extends Model
{
    protected $table = 'parametres';

    protected $fillable = [
        'user_id', // Optionnel : si les paramètres sont liés à un utilisateur
        'heart_rate_critical_min',
        'heart_rate_critical_max',
        'heart_rate_warning_min',
        'heart_rate_warning_max',
        'temperature_critical_min',
        'temperature_critical_max',
        'temperature_warning_min',
        'temperature_warning_max',
        'notif_alertes_critiques',
        'notif_alertes_hautes',
        'notif_alertes_moyennes',
        'email_alertes_critiques',
        'email_rapports_quotidiens',
        'email_mises_a_jour',
        'theme',
        'compact_mode',
        'items_per_page',
        'is_default', // Pour indiquer si ce sont les paramètres par défaut
    ];

    protected $casts = [
        'notif_alertes_critiques' => 'boolean',
        'notif_alertes_hautes' => 'boolean',
        'notif_alertes_moyennes' => 'boolean',
        'email_alertes_critiques' => 'boolean',
        'email_rapports_quotidiens' => 'boolean',
        'email_mises_a_jour' => 'boolean',
        'compact_mode' => 'boolean',
        'is_default' => 'boolean',
    ];

    // Relation avec l'utilisateur (optionnel)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
