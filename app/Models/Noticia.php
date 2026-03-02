<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Noticia extends Model
{
    protected $fillable = ['titulo', 'contenido', 'imagen', 'ministerio_id'];

    public function ministerio()
    {
        return $this->belongsTo(Ministerio::class);
    }
}
