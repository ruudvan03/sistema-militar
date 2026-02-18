<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 

class Document extends Model
{
    use HasFactory, SoftDeletes; 

    protected $fillable = [
        'user_id',
        'titulo',
        'ruta_archivo',
        'tipo',
        'estado' 
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}