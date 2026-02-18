<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    // 1. CAMPOS QUE SE PUEDEN LLENAR MASIVAMENTE (Seguridad)
    // Sin esto, el comando Document::create() fallaría.
    protected $fillable = [
        'titulo',
        'ruta_archivo',
        'tipo',
        'user_id' 
    ];

    // 2. RELACIÓN INVERSA (Un Documento pertenece a un Usuario)
    // Esto permite usar $document->user->name en el Dashboard.
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}