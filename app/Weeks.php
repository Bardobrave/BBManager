<?php

namespace App;

use Illuminate\database\Eloquent\Model;
use App\Matches;

class Weeks extends Model
{
    //Tabla del modelo
    protected $table = "JORNADAS";

    //Métodos de definición de relaciones del modelo----------------

    //Relación de pertenencia con las ligas
    public function league() {
      return $this->belongsTo('App\League', 'liga');
    }

    //Relación uno a muchos con los partidos
    public function matches() {
      return $this->hasMany('App\Matches', 'jornada');
    }

    //Fin de métodos de definición de relaciones del modelo-----------

    //Métodos de funcionalidad ---------------------------------------

    /**
     * Método que construye los emparejamientos de una jornada
     *
     * @param emparejamientos de la jornada a construir
     */
    public function assignMatches($emparejamientos) {
      for($x = 0; $x < count($emparejamientos); $x++) {
        $match = new Matches;
        $match->jornada = $this->id;
        $match->anfitrion = $emparejamientos[$x]->anfitrion;
        $match->visitante = $emparejamientos[$x]->visitante;
        $match->save();
      }
    }

    /**
     * Método para crear una nueva jornada
     *
     * @param request con los datos de la jornada
     */
    public function create($league, $request) {
      $this->liga = $league->id;
      if ($league->weeks->count() == 0)
        $this->numJornada = 1;
      else
        $this->numjornada = $league->weeks->sortByDesc("numjornada")->first()->numjornada + 1;

      $this->eliminatoria = ($request->input("eliminatoria") == 1);
      $this->observaciones = $request->input("observaciones");
      $this->save();
      $this->assignMatches(json_decode($request->input("emparejamientos")));
    }

    /**
     * Método para modificar una jornada
     *
     * @param request con los datos de la jornada
     */
    public function edit($request) {
      $this->eliminatoria = ($request->input("eliminatoria") == 1);
      $this->observaciones = $request->input("observaciones");
      $this->save();

      /*Para reordenar emparejamientos, y dado que los partidos aún no se han jugado,
        ya que de lo contrario no podríamos estar editando la jornada, se eliminan
        todos los que tuviera definidos, y después se crean de cero*/
      $this->eraseMatches();
      $this->assignMatches(json_decode($request->input("emparejamientos")));
    }

    /**
     * Método para eliminar una jornada
     */
    public function erase() {
      //Primero se deben eliminar todos los partidos de la jornada.
      $this->eraseMatches();

      //Finalmente puede eliminarse el propio elemento
      $this->delete();
    }

    /**
     * Método que elimina todos los emparejamientos de una jornada
     */
    public function eraseMatches() {
      foreach($this->matches as $match) {
        $match->delete();
      }
    }


    //Fin de métodos de funcionalidad---------------------------------
}
