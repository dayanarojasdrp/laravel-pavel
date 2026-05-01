<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MensajeContacto extends Model
{
    protected $table = 'mensajes_contacto';

    protected $fillable = [
        'nombre',
        'email',
        'telefono',
        'asunto',
        'mensaje',
        'estado',
        'leido',
        'respondido_en',
        'notas_internas',
    ];

    protected $casts = [
        'leido' => 'boolean',
        'respondido_en' => 'datetime',
    ];
}
