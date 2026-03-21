<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject; 

class User extends Authenticatable implements JWTSubject 
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'grado',
        'matricula',
        'area',
        'especialidad',
        'role',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'role' => $this->role,
            'matricula' => $this->matricula,
            'area' => $this->area
        ];
    }

    // --- RELACIONES DE BASE DE DATOS ---
    
    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}