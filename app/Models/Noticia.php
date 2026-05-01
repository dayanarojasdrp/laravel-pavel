<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Noticia extends Model
{
    protected $fillable = [
        'titulo',
        'slug',
        'resumen',
        'contenido',
        'imagen',
        'autor',
        'publicado_en',
        'estado',
        'destacada',
        'categoria',
        'meta_title',
        'meta_description',
        'ministerio_id',
    ];

    protected $casts = [
        'publicado_en' => 'datetime',
        'destacada' => 'boolean',
    ];

    public function ministerio()
    {
        return $this->belongsTo(Ministerio::class);
    }
}
