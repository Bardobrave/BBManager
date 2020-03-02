<?php

namespace App;

use Illuminate\database\Eloquent\Model;

class Rol extends Model
{
    //Tabla del modelo
    protected $table = "ROLES";

    //Relación uno a muchos
    public function user() {
      return $this->hasMany('App\User');
    }
}
