<?php

namespace App;

use Illuminate\database\Eloquent\Model;

class LeagueTeam extends Model
{
    //Tabla del modelo
    protected $table = "LIGAS_EQUIPOS";

    //Relación de pertenencia a una liga
    public function league() {
      return $this->belongsTo('App\League', 'liga');
    }

    //Relación de pertenencia a un equipo
    public function team() {
      return $this->belongsTo('App\Team', 'equipo');
    }

    /**
     * Método para añadir un nuev equipo a una liga
     *
     * @param idLeague id de la liga a la que se añade al equipo
     * @param idTeam id del equipo que se le añade
     */
    public function addToLeague($idLeague, $idTeam) {
      $this->liga = $idLeague;
      $this->equipo = $idTeam;
      $this->save();
    }

    /**
     * Método que gestiona la inscripción en base a un parámetro
     *
     * @param resultado de la inscripción
     */
    public function manage($result) {
      switch($result) {
        case 'aceptar':
          $this->aceptado = 1;
          $this->save();
          break;
        case 'rechazar':
          $this->delete();
          break;
      }
    }

}
