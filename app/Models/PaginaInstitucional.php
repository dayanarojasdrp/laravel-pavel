<?php

namespace App\Models;

use App\Models\Concerns\RecordsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaginaInstitucional extends Model
{
    use RecordsActivity, SoftDeletes;

    protected $table = 'paginas_institucionales';

    protected $fillable = [
        'titulo',
        'slug',
        'contenido',
        'resumen',
        'imagen',
        'seccion',
        'orden',
        'activo',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'orden' => 'integer',
        'activo' => 'boolean',
    ];
}
