<?php

namespace App;

use Illuminate\database\Eloquent\Model;
use App\AnnotationType;

class Annotations extends Model
{
    //Tabla del modelo
    protected $table = "ANOTACIONES";

    //Relación de pertenencia a un tipo de anotacion
    public function type() {
      return $this->belongsTo('App\AnnotationType', 'tipo');
    }

    //Relación de pertenencia a un partido
    public function match() {
      return $this->belongsTo('App\MatchTeam', 'partido');
    }

    //Relación uno a uno con el jugador activo
    public function active() {
      return $this->hasOne('App\Player', 'id', 'jugadoractivo');
    }

    //Relación uno a uno con el jugador pasivo
    public function pasive() {
      return $this->hasOne('App\Player', 'id', 'jugadorpasivo');
    }

    /**
     * Método que carga una anotación en base de datos a partir de un elemento
     *
     * @param partido al que pertenece la anotación
     * @param annotation objeto de datos con la infomación de la anotación
     */
    public function newAnnotation($partido, $annotation) {
      $this->partido = $partido;
      $this->tipo = AnnotationType::where("tipo", $annotation->tipo)->first()->id;
      $this->jugadoractivo = $annotation->activo;
      if ($annotation->pasivo != '')
        $this->jugadorpasivo = $annotation->pasivo;
      if ($annotation->efecto != '')
        $this->efecto = $annotation->efecto;
      $this->save();
    }
}
