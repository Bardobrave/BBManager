<?php

namespace App;

use Illuminate\database\Eloquent\Model;
use App\Team;

class MatchTeam extends Model
{
    //Tabla del modelo
    protected $table = "PARTIDOS_EQUIPOS";

    //Relación de pertenencia a un partido
    public function match() {
      return $this->belongsTo('App\Matches', 'partido');
    }

    //Relación de pertenencia a un equipo
    public function team() {
      return $this->belongsTo('App\Team', 'equipo');
    }

    //Relación uno a muchos con las anotaciones
    public function annotations() {
      return $this->hasMany('App\Annotations', 'partido');
    }

    /**
     * Método para crear una parte de un acta
     *
     * @param idMatch id del partido al que pertenecen
     * @param idTeam id del equipo al que pertenece la parte del acta
     */
    public function createSheet($idMatch, $idTeam) {
      $team = Team::find($idTeam);
      $this->partido = $idMatch;
      $this->equipo = $idTeam;
      $this->tesoinicial = $team->tesoreria;
      $this->ffinicial = $team->ff;
      $this->save();
    }

    /**
     * Método que graba los datos de un acta modificada
     *
     * @param request con la información del acta
     */
    public function edit($request) {
      $this->gastoinducements = $request->input("gastoinducements");
      $this->inducements = $request->input("inducements");
      $this->espectadores = $request->input("espectadores");
      $this->fffinal = $request->input("fffinal");
      $this->recaudacion = $request->input("recaudacion");
      $this->tesofinal = $request->input("tesofinal");
      $this->actafinalizada = $request->input("actafinalizada");
      if ($request->input("tdcontra") != "")
        $this->tdc = $request->input("tdcontra");
      $this->manageAnnotations(json_decode($request->input("annotations")));
      $this->save();
      $this->refresh(); //Hay que recuperar las nuevas anotaciones

      /*Tras actualizar los datos del acta, si se da por finalizada, hay que
        actualizar las estadísticas del partido y del equipo*/
      if ($this->actafinalizada) {
        $this->updateMatchStats();
        $this->updateTeamStats();
        $this->updatePlayerStats();
        $this->save();
        $this->team->spike();
        $this->team->save();
      }
    }

    /**
     * Método que gestiona las anotaciones del acta
     *
     * @param annotations array con las anotaciones del formulario
     */
    public function manageAnnotations($annotations) {
      /*En annotations nos llegarán elementos con id y elementos sin id, los
        que tengan id ya estaban en la base de datos, y los que tengan un id = new
        son nuevos, por tanto hay dos tareas que llevar a cabo:
        1) Eliminar todas las anotaciones asociadas al partido cuyo id no aparezca
          en el objeto annotations
        2) Añadir una entrada de anotación por cada elemento que tenga id = new
      */
      $cAnnotations = collect($annotations);
      //Eliminación de las anotaciones cuyo id no aparece en el array
      foreach($this->annotations as $currentAnnotation) {
        if ($cAnnotations->where("id", $currentAnnotation->id)->count() == 0)
          $currentAnnotation->delete();
      }

      //Añadir las anotaciones nuevas
      foreach($annotations as $newAnnotation) {
        if ($newAnnotation->id == 'new') {
          $annotation = new Annotations;
          $annotation->newAnnotation($this->id, $newAnnotation);
        }
      }
    }

    /**
     * Método que reabre un acta y deshace los cambios que se produjeron al cerrarla
     */
    public function reopen() {
      $this->actaFinalizada = false;
      $this->revertPlayerStats();
      $this->revertTeamStats();
      $this->revertMatchStats();
      $this->save();
      $this->team->spike();
      $this->team->save();
    }

    /**
     * Método que revierte las modificaciones al partido del acta
     */
    public function revertMatchStats() {
      $actaRival = $this->match->teams->where("equipo", "<>", $this->equipo)
        ->first();

      $this->tdf = 0;
      $actaRival->tdc = 0;
      $this->hf = 0;
      $this->hc = 0;
      $this->mf = 0;
      $this->mc = 0;
      $this->pases = 0;
      $this->yardaspase = 0;
      $this->intf = 0;
      $actaRival->intc = 0;
      $actaRival->save();
    }

    /**
     * Método que revierte las estadísticas sobre los jugadores
     */
    public function revertPlayerStats() {

      $leagueTeam = $this->match->week->league->leagueTeams->where("equipo", $this->equipo)->first();
      /*La reversión de los jugadores lesionados sólo se lleva a cabo cuando estamos
        dando marcha atrás a la última jornada. Si ya se han jugado más partidos, esta
        información ya está obsoleta*/
      if ($this->match->week->numjornada == $leagueTeam->jornada - 1) {
        //Se obtiene un array con los jugadores que estaban lesionados en ese partido
        $lesionadosPrevios = explode('|', $this->lesionadosPrevios);

        /*Antes de comenzar con las anotaciones se decrementa en uno el número de
          partidos jugados por cada uno de los jugadores que no aparecen en el array,
          ya que estos no pudieron jugarlo */
        foreach($this->team->players->whereNotIn("id", $lesionadosPrevios) as $currentPlayer) {
          $currentPlayer->jugados--;
          $currentPlayer->save();
        }

        $this->lesionadosPrevios = '';
      }

      /*Después se aplican las estadísticas del partido a los jugadores */
      foreach($this->annotations as $currentAnnotation) {
        $player = $currentAnnotation->active;
        switch($currentAnnotation->type->tipo) {
          case "PASE":
             $player->pases--;
             $player->yardaspase -= $currentAnnotation->efecto;
             $player->px--;
             break;
          case "HERIDO":
             $completeEffect = explode('|', $currentAnnotation->efecto);
             if (intval($completeEffect[0]) < 61)
               $player->hf--;
             else
               $player->mf--;

             if ($completeEffect[1] == 'false' && $completeEffect[2] == 'false')
               $player->px -= 2;
             break;
          case "LESIONADO":
             $completeEffect = explode('|', $currentAnnotation->efecto);
             if (intval($completeEffect[0]) < 61) {
               $player->hc--;
               if (intval($completeEffect[0]) >= 41) {
                 $player->lesionado = false;
                 switch (intval($completeEffect[0])) {
                   case 51:
                   case 52:
                     $player->niggling--;
                     break;
                   case 53:
                   case 54:
                     $player->ma++;
                     break;
                   case 55:
                   case 56:
                     $player->av++;
                     break;
                   case 57:
                     $player->agl++;
                     break;
                   case 58:
                     $player->fue++;
                     break;
                 }
               }
             } else {
               $player->muerto = false;
               $player->activo = true;
             }
             if ($completeEffect[2] == 'true' || $completeEffect[3] == 'true')
               $player->curado--;
             break;
          case "INTERCEPCION":
             $player->intercepciones--;
             $player->px -= 3;
             break;
          case "TD":
             $player->td--;
             $player->px -= 3;
             break;
          case "MVP":
             $player->mvp--;
             $player->px -= 5;
             break;
        }
        $player->save();
      }
    }

    /**
     * Método que revierte las estadísticas del partido sobre el equipo
     */
    public function revertTeamStats() {
      $team = $this->team;
      $liga = $this->match->week->league;
      $leagueTeam = $liga->leagueTeams->where("equipo", $this->equipo)->first();
      $golaverage = $this->tdf - $this->tdc;

      $team->tesoreria = $this->tesofinal - $this->recaudacion;
      $team->ff = $this->ffinicial;
      $team->jugados--;
      if ($golaverage > 0) {
        $team->ganados--;
        $leagueTeam->puntos -= $liga->puntosvictoria;
      } else if ($golaverage == 0) {
        $team->empatados--;
        $leagueTeam->puntos -= $liga->puntosempate;
      } else {
        $team->perdidos--;
        $leagueTeam->puntos -= $liga->puntosderrota;
      }
      $team->tdf -= $this->tdf;
      $team->tdc -= $this->tdc;
      $team->hf -= $this->hf;
      $team->hc -= $this->hc;
      $team->mf -= $this->mf;
      $team->mc -= $this->mc;
      $team->pases -= $this->pases;
      $team->yardaspase -= $this->yardaspase;
      $team->intercepciones -= $this->intf;
      $team->intercepcionesc -= $this->intc;

      $leagueTeam->save();
      $team->save();
    }

    /**
     * Método que actualiza las estadísticas del partido para este equipo
     */
    public function updateMatchStats() {
      $actaRival = $this->match->teams->where("equipo", "<>", $this->equipo)
        ->first();

      $this->tdf = $this->annotations()->whereHas("type", function ($q) {
        $q->where("tipo", "TD");
      })->count();
      $actaRival->tdc = $this->tdf;
      $this->hf = $this->annotations()->whereHas("type", function($q) {
        $q->where("tipo", "HERIDO");
      })->where("efecto", "<", "61")->count();
      $this->hc = $this->annotations()->whereHas("type", function($q) {
        $q->where("tipo", "LESIONADO");
      })->where("efecto", "<", "61")->count();
      $this->mf = $this->annotations()->whereHas("type", function($q) {
        $q->where("tipo", "HERIDO");
      })->where("efecto", ">=", "61")->count();
      $this->mc = $this->annotations()->whereHas("type", function($q) {
        $q->where("tipo", "LESIONADO");
      })->where("efecto", ">=", "61")->count();
      $this->pases = $this->annotations()->whereHas("type", function($q) {
        $q->where("tipo", "PASE");
      })->count();
      $this->yardaspase = $this->annotations()->whereHas("type", function($q) {
        $q->where("tipo", "PASE");
      })->sum("efecto");
      $this->intf = $this->annotations()->whereHas("type", function($q) {
        $q->where("tipo", "INTERCEPCION");
      })->count();
      $actaRival->intc = $this->intf;

      $actaRival->save();
    }

    /**
     * Método que actualiza las estadísticas del partido para cada jugador del equipo
     */
    public function updatePlayerStats() {

      $leagueTeam = $this->match->week->league->leagueTeams->where("equipo", $this->equipo)->first();
      /*La actualización de los lesionados para el partido sólo se lleva a cabo
        si estamos grabando la jornada en curso. En caso de estar grabando la
        actualización de una jornada pasada se trata de información obsoleta que
        no queremos actualizar*/
      if ($this->match->week->numjornada == $leagueTeam->jornada - 1) {
        /*Antes de comenzar con las anotaciones se incrementa en uno el número de
          partidos jugados por cada uno de los jugadores que no estaban lesionados */
        foreach($this->team->players->where("lesionado", false) as $currentPlayer) {
          $currentPlayer->jugados++;
          $currentPlayer->save();
        }

        /*Después se restauran los jugadores lesionados a un estado normal*/
        foreach($this->team->players->where("lesionado", true) as $currentPlayer) {
          $currentPlayer->lesionado = false;
          //Se añade este jugador a la lista de lesionados previos, por si se revierte el acta
          $this->lesionadosprevios = $this->lesionadosprevios.$currentPlayer->id.'|';
          $currentPlayer->save();
        }
        $this->save();
      }

      /*Después se aplican las estadísticas del partido a los jugadores */
      foreach($this->annotations as $currentAnnotation) {
        $player = $currentAnnotation->active;
        switch($currentAnnotation->type->tipo) {
          case "PASE":
             $player->pases++;
             $player->yardaspase += $currentAnnotation->efecto;
             $player->px++;
             break;
          case "HERIDO":
             $completeEffect = explode('|', $currentAnnotation->efecto);
             if (intval($completeEffect[0]) < 61)
               $player->hf++;
             else
               $player->mf++;

             if($completeEffect[1] == 'false' && $completeEffect[2] == 'false')
               $player->px += 2;
             break;
          case "LESIONADO":
             $completeEffect = explode('|', $currentAnnotation->efecto);
             if (intval($completeEffect[0]) < 61) {
               $player->hc++;
               if (intval($completeEffect[0]) >= 41) {
                 $player->lesionado = true;
                 switch (intval($completeEffect[0])) {
                   case 51:
                   case 52:
                     $player->niggling++;
                     break;
                   case 53:
                   case 54:
                     $player->ma--;
                     break;
                   case 55:
                   case 56:
                     $player->av--;
                     break;
                   case 57:
                     $player->agl--;
                     break;
                   case 58:
                     $player->fue--;
                     break;
                 }
               }
             } else {
               $player->muerto = true;
               $player->activo = false;
             }
             if ($completeEffect[2] == 'true' || $completeEffect[3] == 'true')
               $player->curado++;
             break;
          case "INTERCEPCION":
             $player->intercepciones++;
             $player->px += 3;
             break;
          case "TD":
             $player->td++;
             $player->px += 3;
             break;
          case "MVP":
             $player->mvp++;
             $player->px += 5;
             break;
        }
        $player->save();
      }

    }

    /**
     * Método que actualiza las estadísticas del partido para el equipo
     */
     public function updateTeamStats() {
       $team = $this->team;
       $liga = $this->match->week->league;
       $leagueTeam = $liga->leagueTeams->where("equipo", $this->equipo)->first();
       $golaverage = $this->tdf - $this->tdc;

       $team->tesoreria = $this->tesofinal;
       $team->ff = $this->fffinal;
       $team->jugados++;
       $team->preparado = false;
       if ($leagueTeam->jornada == $this->match->week->numjornada)
         $leagueTeam->jornada++;
       if ($golaverage > 0) {
         $team->ganados++;
         $leagueTeam->puntos += $liga->puntosvictoria;
       } else if ($golaverage == 0) {
         $team->empatados++;
         $leagueTeam->puntos += $liga->puntosempate;
       } else {
         $team->perdidos++;
         $leagueTeam->puntos += $liga->puntosderrota;
       }
       $team->tdf += $this->tdf;
       $team->tdc += $this->tdc;
       $team->hf += $this->hf;
       $team->hc += $this->hc;
       $team->mf += $this->mf;
       $team->mc += $this->mc;
       $team->pases += $this->pases;
       $team->yardaspase += $this->yardaspase;
       $team->intercepciones += $this->intf;
       $team->intercepcionesc += $this->intc;

       $leagueTeam->save();
       $team->save();
     }



}
