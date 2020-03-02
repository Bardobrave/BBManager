<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\League;
use App\Weeks;

class JornadasController extends Controller
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
 * Crear una jornada nueva
 *
 * @param id de la liga a la que pertenece la jornada
 *
 * @return \Illuminate\Contracts\Support\Renderable
 */
public function create($id) {
  //Se comprueba que la liga existe y no está abierta
  $league = League::find($id);

  //Sólamente los administradores y comisionados pueden crear jornadas
  if ($this->userRol != 3 && $league != null && !$league->abierta && !$league->finalizada)
    return view('jornadas/crear', ["league" => $league]);
  else
    return redirect('home');
}

/**
 * Grabar una jornada nueva
 *
 * @param id de la liga sobre la que se crea la jornada
 * @param request con los datos del formulario
 */
public function saveCreate($id, Request $request) {
    //Se comprueba que la liga existe
    $league = League::find($id);

    //Si el usuario es administrador o comisionado, y la liga existe y no está abierta
    if ($this->userRol != 3 && $league != null && !$league->abierta && !$league->finalizada) {
      $week = new Weeks;
      $week->create($league, $request);
      return redirect('/ligas/detalle/'.$id.'#calendar');
    } else
      return redirect('home');
}

/**
 * Dirigir al formulario de edición de una jornada
 *
 * @param id de la jornada que se va a editar
 *
 * @return \Illuminate\Contracts\Support\Renderable
 */
public function edit($id) {
    //Se carga la jornada
    $week = Weeks::find($id);

    /*Sólo administradores y comisionados pueden editar jornadas, de las cuales
      no se haya jugado ningún partido aún */
    if ($this->userRol != 3 && !$week->league->finalizada && $week != null
      && $week->matches->where("jugado", 1)->count() == 0) {
      return view('jornadas/editar', ["week" => $week]);
    } else {
      return redirect('home');
    }
}

/**
 * Grabar los cambios realizados sobre una jornada
 *
 * @param id de la jornada que se va a editar
 * @param request con los datos enviados por el formulario
 */
public function saveEdit($id, Request $request) {
  //Se comprueba que la jornada exista
  $week = Weeks::find($id);

  /*Si el usuario es administrador o comisionado, y la jornada no tiene ningún
    partido jugado, entonces puede editarse*/
  if ($this->userRol != 3 && !$week->league->finalizada && $week != null
    && $week->matches->where("jugado", 1)->count() == 0) {
    $week->edit($request);
    return redirect('/ligas/detalle/'.$week->league->id.'#calendar');
  } else
    return redirect('home');
}

/**
 * Mostrar la páina de confirmación de borrado de una jornada
 *
 * @param id de la jornada que se quiere eliminar
 *
 * @return \Illuminate\Contracts\Support\Renderable
*/
public function delete($id){
  //Se carga la jornada
  $week = Weeks::find($id);

  /*Sólo administradores y comisionados pueden borrar jornadas, de las cuales
    no se haya jugado ningún partido aún */
  if ($this->userRol != 3 && !$week->league->finalizada && $week != null
    && $week->matches->where("jugado", 1)->count() == 0) {
    return view('jornadas/borrar', ["week" => $week]);
  } else
    return redirect('home');
}

/**
 * Eliminar una jornada
 *
 * @param id de la jornada que se va a eliminar
*/
public function saveDelete($id){
  //Se comprueba que la jornada existe
  $week = Weeks::find($id);

  /*Sólo administradores y comisionados pueden borrar jornadas, de las cuales
    no se haya jugado ningún partido aún */
  if ($this->userRol != 3 && !$week->league->finalizada && $week != null
    && $week->matches->where("jugado", 1)->count() == 0) {
    $week->erase();
    //Se redirige al detalle de la liga
    return redirect('ligas/detalle/'.$week->league->id.'#calendar');
  } else
    return redirect('home');
}

//Fin de métodos de gestión CRUD-----------------------------------------------
}
