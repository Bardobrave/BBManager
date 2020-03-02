<?php

namespace App;

use Illuminate\database\Eloquent\Model;

class PlayerSkill extends Model
{
    //Tabla del modelo
    protected $table = "HABILIDADES_JUGADORES";

    //Relación de pertenencia a una habilidad
    public function skill() {
      return $this->belongsTo('App\Skill', 'habilidad');
    }

    //Relación de pertenencia a un jugador
    public function player() {
      return $this->belongsTo('App\Player', 'jugador');
    }

    /**
     * Método para añadir una nueva habilidad a un jugador
     *
     * @param idPlayer id del jugador al que se le añade la habilidad
     * @param idSkill id de la habilidad que se le añade
     */
    public function addToPlayer($idPlayer, $idSkill) {
      $this->jugador = $idPlayer;
      $this->habilidad = $idSkill;
      $this->save();
    }

}
