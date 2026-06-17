<?php

namespace App\Models;

use App\Models\Concerns\RecordsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MensajeContacto extends Model
{
    use RecordsActivity, SoftDeletes;

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
