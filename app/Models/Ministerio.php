<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ministerio extends Model
{
    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'imagen',
        'categoria',
        'orden',
        'activo',
        'url_externa',
    ];

    protected $casts = [
        'orden' => 'integer',
        'activo' => 'boolean',
    ];

    public function noticias()
    {
        return $this->hasMany(Noticia::class);
    }
}
