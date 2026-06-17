<?php

namespace App\Models;

use App\Models\Concerns\RecordsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Misione extends Model
{
    use RecordsActivity, SoftDeletes;

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
