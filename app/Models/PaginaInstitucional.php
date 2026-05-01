<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaginaInstitucional extends Model
{
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
