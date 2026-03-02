<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ministerio extends Model
{
     protected $fillable = ['nombre'];

    public function noticias()
    {
        return $this->hasMany(Noticia::class);
    }
}
