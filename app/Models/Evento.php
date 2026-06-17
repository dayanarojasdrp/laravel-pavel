<?php

namespace App\Models;

use App\Models\Concerns\RecordsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Evento extends Model
{
    use RecordsActivity, SoftDeletes;

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
