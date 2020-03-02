<?php

namespace App;

use Illuminate\database\Eloquent\Model;

class Race extends Model
{
    //Tabla del modelo
    protected $table = "RAZAS";

    //Relación uno a muchos con los equipos
    public function team() {
      return $this->hasMany('App\Team', 'raza');
    }

    //Relación uno a muchos con las posiciones de la raza
    public function positionals() {
      return $this->hasMany('App\Positional', 'raza');
    }
}
