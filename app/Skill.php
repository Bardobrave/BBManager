<?php

namespace App;

use Illuminate\database\Eloquent\Model;

class Skill extends Model
{
    //Tabla del modelo
    protected $table = "HABILIDADES";

    //Relación muchos a muchos con jugadores
    public function players() {
      return $this->belongsToMany('App\Player', 'HABILIDADES_JUGADORES', 'habilidad', 'jugador');
    }

    //Relación muchos a muchos con los posicionales
    public function positionals() {
      return $this->belongsToMany('App\Positional', 'POSICIONES_HABILIDADES', 'habilidad', 'posicion');
    }

    //Relación de pertenencia a una categoría
    public function category() {
      return $this->belongsTo('App\Category', 'categoria');
    }

}
