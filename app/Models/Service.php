<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $table = 'services';
    
    protected $fillable = [
        'nom', 'description'
    ];

    // Relations
    public function patients()
    {
        return $this->hasMany(Patient::class);
    }
    
    public function utilisateurs()
    {
        return $this->hasMany(User::class);
    }
}
