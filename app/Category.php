<?php

namespace App;

use Illuminate\database\Eloquent\Model;

class Category extends Model
{
    //Tabla del modelo
    protected $table = "CATEGORIAS";

    //Relación uno a muchos
    public function skills() {
      return $this->hasMany('App\Skill', 'categoria');
    }

    //Relación muchos a muchos entre posiciones y categorías
    public function positionals() {
      return $this->belongsToMany('App\Positional', 'POSICIONES_CATEGORIAS', 'categoria', 'posicion');
    }
}
