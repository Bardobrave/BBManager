<?php

namespace App;

use Illuminate\database\Eloquent\Model;

class Matches extends Model
{
    //Tabla del modelo
    protected $table = "PARTIDOS";

    //Relación de pertenencia a una jornada
    public function week() {
      return $this->belongsTo('App\Weeks', 'jornada');
    }

    //Relación uno a muchos (2) con partidos_equipos
    public function teams() {
      return $this->hasMany('App\MatchTeam', 'partido');
    }

    //Relación uno a uno con el equipo anfitrion
    public function local() {
      return $this->hasOne('App\Team', 'id', 'anfitrion');
    }

    //Relación uno a uno con el equipo visitante
    public function away() {
      return $this->hasOne('App\Team', 'id', 'visitante');
    }

    /**
     * Método para crear las dos partes de un acta de un partido
     *
     * @param idHome id del equipo que juega en casa
     * @param idVisitor id del equipo visitante
     */
     public function createMatchSheet($idHome, $idVisitor) {
       //Se crea un primer parte del acta para el equipo anfitrión
       $homeSheet = new MatchTeam;
       $homeSheet->createSheet($this->id, $idHome);

       //Se crea otro parte de acta para el visitante
       $awaySheet = new MatchTeam;
       $awaySheet->createSheet($this->id, $idVisitor);
     }
}
