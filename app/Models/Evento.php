<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    protected $fillable = [
        'titulo',
        'slug',
        'descripcion',
        'resumen',
        'imagen',
        'categoria',
        'fecha_inicio',
        'fecha_fin',
        'lugar',
        'direccion',
        'ciudad',
        'estado',
        'destacado',
        'activo',
        'registro_url',
        'capacidad',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'destacado' => 'boolean',
        'activo' => 'boolean',
        'capacidad' => 'integer',
    ];
}
