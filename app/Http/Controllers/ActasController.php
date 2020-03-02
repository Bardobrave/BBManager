<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Matches;
use App\MatchTeam;

class ActasController extends Controller
{

    //Variables con el rol y el id del usuario en sesión
    public $userRol;
    public $userId;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->middleware('auth');
        $this->middleware('auth.check'); //Carga userRol y userId
    }

//--Gestión de llamadas AJAX---------------------------------------------------

//Fin de gestión de llamadas AJAX ---------------------------------------------

//Métodos de gestión CRUD------------------------------------------------------

/**
 * Crear un acta nueva
 *
 * @param id del partido al que pertenece el acta
 *
 * @return \Illuminate\Contracts\Support\Renderable
 */
public function create($id) {
  //Se comprueba que el partido existe, no tiene acta y los equipos están listos
  $match = Matches::find($id);

  /*Sólamente los administradores, comisionados y jugadores asociados al partido
  pueden crear el acta. El partido, además, no debe tener ya un acta asociada, y
  ambos equipos deben estar preparados para jugar*/
  if ($match != null && $match->teams->count() == 0 && ($this->userRol != 3
    || $this->userId == $match->local->user->id || $this->userId
    == $match->away->user->id) && $match->local->preparado && $match->away->preparado) {
      //Se crean las dos partes del acta
      $match->createMatchSheet($match->local->id, $match->away->id);

      //Si el usuario en sesión es un jugador, se redirige a su parte del acta
      if ($this->userRol == 3) {
        if ($this->userId == $match->local->user->id)
          $sheet = MatchTeam::find($match->teams->where("equipo", $match->local->id));
        else
          $sheet = MatchTeam::find($match->teams->where("equipo", $match->away->id));

        return view('actas/editar', ["sheet" => $sheet]);
      } else
        /*Si el usuario es un comisionado o administrador, les llevamos al detalle
        vacío del acta, para que escojan qué lado editar*/
        return redirect('actas/detalle/'.$id);
  } else
    return redirect('home');
}

/**
 * Dirigir al formulario de edición de una sección de un acta
 *
 * @param id de la sección que se va a editar
 *
 * @return \Illuminate\Contracts\Support\Renderable
 */
public function edit($id) {
    //Se carga la sección del acta
    $sheet = MatchTeam::find($id);

    /*Sólo administradores, comisionados y el jugador dueño de esa sección,
    pueden editar el acta, y el jugador sólo si no se ha finalizado */
    if ($this->userRol != 3 || ($sheet->equipo->user->id == $this->userId
      && !$sheet->actafinalizada)) {
      return view('actas/editar', ["sheet" => $sheet]);
    } else {
      return redirect('home');
    }
}

/**
 * Grabar los cambios realizados sobre un acta
 *
 * @param id de la sección del acta que se va a editar
 * @param request con los datos enviados por el formulario
 */
public function saveEdit($id, Request $request) {
  //Se comprueba que la sección exista
  $sheet = MatchTeam::find($id);

  /*Sólo administradores, comisionados y el jugador dueño de esa sección,
  pueden editar el acta, y el jugador sólo si no se ha finalizado */
  if ($this->userRol != 3 || ($sheet->equipo->user->id == $this->userId
    && !$sheet->actafinalizada)) {
    $sheet->edit($request);
    return redirect('/actas/detalle/'.$sheet->match->id);
  } else
    return redirect('home');
}

/**
* Acceso al detalle de una hoja de acta
*
* @param id del partido al que pertenece el acta
*
* @return \Illuminate\Contracts\Support\Renderable
*/
public function detail($id) {
  //Se comprueba que el partido existe y tiene acta creada.
  $match = Matches::find($id);

  /*Sólamente los administradores, comisionados y jugadores asociados al partido
  pueden ver el acta. El acta debe existir*/
  if ($match != null && $match->teams->count() != 0 && ($this->userRol != 3
    || $this->userId == $match->local->user->id || $this->userId
    == $match->away->user->id)) {

      /*Se actualiza el valor de sesión de última página visitada*/
      session(['lastVisited' => '/actas/detalle/'.$id]);

      return view("/actas/detalle", [ "match" => $match ]);
  } else
    return redirect("home");
}

//Fin de métodos de gestión CRUD-----------------------------------------------

//Otros métodos de gestión ----------------------------------------------------

  /**
   * Reapertura de un acta
   *
   * @param id del acta que se va a reabrir
   *
   * @return \Illuminate\Contracts\Support\Renderable
   */
  public function reopen($id) {
    //Primero se comprueba si el acta existe y el usuario es admin o comisionado
    $sheet = MatchTeam::find($id);

    if ($sheet != null && $this->userRol != 3 && $sheet->actafinalizada) {
      //En este caso, si además está finalizada, hay que retrotraer los efectos del acta
      $sheet->reopen();

      //Y redirigir a la ficha del acta reabierta
      return redirect("/actas/editar/".$id);
    } else
      return redirect("home");
  }

//Fin de otros métodos de gestión----------------------------------------------

}
