<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recurso extends Model
{
    protected $fillable = [
        'nombre',
        'slug',
        'informacion',
        'imagen',
        'categoria',
        'tipo',
        'archivo_url',
        'link',
        'descargable',
        'destacado',
        'orden',
        'activo',
    ];

    protected $casts = [
        'descargable' => 'boolean',
        'destacado' => 'boolean',
        'orden' => 'integer',
        'activo' => 'boolean',
    ];
}
