<?php

namespace App;

use Illuminate\database\Eloquent\Model;

class AnnotationType extends Model
{
    //Tabla del modelo
    protected $table = "TIPOS_ANOTACIONES";

    //Relación de pertenencia a un tipo de anotacion
    public function annotations() {
      return $this->hasMany('App\Annotations', 'tipo');
    }
}
