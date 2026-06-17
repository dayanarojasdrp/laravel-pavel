<?php

namespace App\Models;

use App\Models\Concerns\RecordsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Noticia extends Model
{
    use RecordsActivity, SoftDeletes;

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
