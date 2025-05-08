<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';
    
    protected $fillable = [
        'nom', 'permissions'
    ];

    protected $casts = [
        'permissions' => 'json',
    ];

    // Relations
    public function utilisateurs()
    {
        return $this->hasMany(Utilisateur::class);
    }
}
