<?php

namespace App\Models;

use App\Models\Concerns\RecordsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recurso extends Model
{
    use RecordsActivity, SoftDeletes;

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
