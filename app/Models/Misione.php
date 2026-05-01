<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Misione extends Model
{
    protected $fillable = [
        'nombre',
        'slug',
        'informacion',
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
}
