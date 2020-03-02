<?php

namespace App;

use Illuminate\database\Eloquent\Model;

class Positional extends Model
{
    //Tabla del modelo
    protected $table = "POSICIONES";

    //Relación de asociación a jugadores
    public function player() {
      return $this->hasMany('App\Player');
    }

    //Relación de pertenencia a una raza
    public function race() {
      return $this->belongsTo('App\Race', 'raza');
    }

    //Relación muchos a muchos entre posicionales y habilidades
    public function skills() {
      return $this->belongsToMany('App\Skill', 'POSICIONES_HABILIDADES', 'posicion', 'habilidad');
    }

    //Relación muchos a muchos entre posiciones y categorías
    public function categories() {
      return $this->belongsToMany('App\Category', 'POSICIONES_CATEGORIAS', 'posicion', 'categoria');
    }

    //Relación uno a muchos entre posiciones y posiciones_categorias
    public function positionalCategories() {
      return $this->hasMany('App\PositionalCategories', 'posicion');
    }

}
