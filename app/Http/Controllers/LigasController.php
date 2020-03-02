<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\validateLeagues;
use App\League;
use App\User;
use App\Team;
use App\LeagueTeam;

class LigasController extends Controller
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
  /**
   * Gestión de la llamada AJAX para controlar si un nombre de liga está repetido en base de datos
   *
   * @param nombre de la liga que hay que comprobar
   * @param id de la liga que, de existir, se excluye de la búsqueda
   * @return boolean
   */
  public function checkSingleName($nombre, $id = null) {
    if ($id != null)
      $league = League::where('nombre', $nombre)->where('id', '<>', $id)->first();
    else
      $league = League::where('nombre', $nombre)->first();
    return (($league != null) ? 'true' : 'false');
  }
//Fin de gestión de llamadas AJAX ---------------------------------------------

//Métodos de gestión CRUD------------------------------------------------------

/**
 * Mostrar una lista de todas las ligas.
 *
 * @param request con las características de la búsqueda
 * @return \Illuminate\Contracts\Support\Renderable
 */
public function list(Request $request) {

    /*Dado que el controlador de listas es un posible punto de retorno,
      se almacena en sesión como última página visitada */
    session(['lastVisited' => '/ligas/lista']);

    return view('ligas/lista', League::listAll($request));
}

/**
 * Mostrar el detalle de una liga.
 *
 * @param id de la liga cuyo detalle se va a mostrar
 * @return \Illuminate\Contracts\Support\Renderable
 */
public function detail($id)
{
    //Primero se carga la liga
    $league = League::find($id);

    //Todo el mundo puede ver el detalle de una liga cualquiera, siempre que exista
    if ($league != null) {

      /*Se actualiza el valor de sesión de última página visitada*/
      session(['lastVisited' => '/ligas/detalle/'.$id]);

      return view('ligas/detalle', ["league" => $league, "stats" => $league->calculateStats()]);
    } else {
      return redirect('home');
    }
}

/**
 * Crear una liga nueva
 *
 * @return \Illuminate\Contracts\Support\Renderable
 */
public function create() {
  //Sólamente los administradores y comisionados pueden crear ligas
  if ($this->userRol != 3)
    return view('ligas/crear');
  else
    return redirect('home');
}

/**
 * Grabar una liga nueva
 *
 * @param request con los datos del formulario
 * @return \Illuminate\Contracts\Support\Renderable
 */
public function saveCreate(validateLeagues $request) {

    $league = new League;
    $id = $league->create($request);

    //Se redirige al detalle de la nueva liga
    return redirect('ligas/detalle/'.$id);
}

/**
 * Dirigir al formulario de edición de una liga
 *
 * @param id de la liga que se va a editar
 * @return \Illuminate\Contracts\Support\Renderable
 */
public function edit($id) {

    //Se carga la liga
    $league = League::find($id);

    //Sólo administradores y comisionados pueden editar ligas
    if ($league != null && $this->userRol != 3) {
      return view('ligas/editar', ["league" => $league]);
    } else {
      return redirect('home');
    }
}

/**
 * Grabar los cambios realizados sobre una liga
 *
 * @param id de la liga que se va a editar
 * @param request con los datos enviados por el formulario
 * @return \Illuminate\Contracts\Support\Renderable
*/
public function saveEdit($id, validateLeagues $request) {

    //El Request se encarga de comprobar las autorizaciones
    $league = League::find($id);

    //Si la liga no existiera se redirigiría al home
    if ($league == null)
      return redirect('home');

    //Se llama al método que actualiza los datos
    $league->edit($request);

    //Y se redirige a la página de detalle de la liga actualizada
    return redirect('ligas/detalle/'.$league->id);
}

/**
 * Mostrar la páina de confirmación de borrado de una liga
 *
 * @param id de la liga que se quiere eliminar
 * @return \Illuminate\Contracts\Support\Renderable
*/
public function delete($id){

  //Se comprueba que la liga existe
  $league = League::find($id);

  /*Sólo administradores y comisionados pueden borrar ligas, y éstas no deben
    tener ningún equipo aplicando */
  if ($league != null && $this->userRol != 3 && $league->leagueTeams->count() == 0) {
    return view('ligas/borrar', [ "league" => $league ]);
  } else {
    return redirect('home');
  }
}

/**
 * Eliminar una liga
 *
 * @param id de la liga que se va a eliminar
 * @return \Illuminate\Contracts\Support\Renderable
*/
public function saveDelete($id){

  //Se comprueba que la liga existe
  $league = League::find($id);

  /*Sólo administradores y comisionados pueden borrar ligas, y éstas no deben
    tener ningún equipo aplicando */
  if ($league != null && $this->userRol != 3 && $league->leagueTeams->count() == 0) {

    //Eliminación de la liga
    $league->delete();

    //Se redirige al listado de ligas
    return redirect('ligas/lista');
  } else {
    return redirect('home');
  }
}

//Fin de métodos de gestión CRUD-----------------------------------------------

//Resto de métodos de gestión del modelo---------------------------------------

  /**
   * Redirección al formulario de inscripción en liga
   *
   * @param id de la liga sobre la que se pide la solicitud
   * @return \Illuminate\Contracts\Support\Renderable
   */
  public function apply($id) {
    //Se comprueba que la liga exista
    $league = League::find($id);

    if ($league != null && $league->abierta) {
      //Se obtienen los equipos viables del jugador en sesión
      $user = User::find($this->userId);

      $listaTeams = [];

      //Se recorren los equipos activos del jugador
      foreach($user->teams->where("activo", 1) as $team) {
        //Si el equipo es nuevo y supera el presupuesto de la liga pasamos al siguiente
        if ($team->leagues->count() == 0 && $team->presupuesto > $league->maximopresupuesto)
          continue;

        //Si la liga es de novatos y el equipo ha jugado en otra liga pasamos al siguiente
        if ($league->liganovatos && $team->leagues->count() > 0)
          continue;

        //Si el equipo ha jugado 3 o más ligas, o está inscrito en una liga sin finalizar, pasamos
        if ($team->leagues->count() >= 3 || $team->leagues->where("finalizada", 0)->count() > 0)
          continue;

        //En otro caso, el equipo es válido y lo añadimos a la lista de equipos
        array_push($listaTeams, $team);
      }

      return view('/ligas/inscripcion', ["league" => $league, "listaTeams" => $listaTeams]);
    } else
      return redirect('home');
  }

  /**
   * Método que finaliza la liga
   *
   * @param id de la liga que se va a finalizar
   */
  public function finish($id) {
    $league = League::find($id);

    if($this->userRol != 3 && $league != null && $league->iniciada) {
      $league->finish();
      return redirect('/ligas/detalle/'.$id);
    } else {
      return redirect('home');
    }
  }

  /**
   * Cerrar o abrir las inscripciones a una liga
   *
   * @param operation que indica si se abre o se cierra
   * @param id de la liga que se quiere abrir o cerrar
   */
  public function inscriptionPhase($operation, $id) {
    //Se comprueba que la liga exista
    $league = League::find($id);

    //Sólo los administradores y los comisionados pueden operar la fase de inscripciones
    if ($this->userRol != 3 && $league != null && !$league->iniciada && !$league->finalizada) {
      $league->toggleInscriptions($operation);
      return redirect('/ligas/detalle/'.$id);
    } else
      return redirect('home');
  }

  /**
   * Gestionar los grupos de la liga
   *
   * @param id de la liga cuyos grupos se van a manipular
   *
   * @return \Illuminate\Contracts\Support\Renderable
   */
  public function manageGroups($id) {
    //Se comprueba que la liga existe
    $league = League::find($id);

    //Sólo administradores y comisionados pueden manipular grupos de ligas cerradas y no iniciadas
    if ($this->userRol != 3 && !$league->abierta && !$league->iniciada && !$league->finalizada)
      return view('ligas/gestionarGrupos', ["league" => $league]);
    else
      return redirect('home');
  }

  /**
   * Grabar los cambios sobre los grupos de la liga
   *
   * @param id de la liga cuyos grupos se van a manipular
   * @param request con los datos de la agrupación de equipos
   */
  public function manageGroupsSave($id, Request $request) {
    //Se comprueba que la liga existe
    $league = League::find($id);

    //Sólo administradores y comisionados pueden manipular grupos de ligas cerradas y no iniciadas
    if ($this->userRol != 3 && !$league->abierta && !$league->iniciada && !$league->finalizada) {
      $league->arrangeGroups(json_decode($request->grupos));
      return redirect('/ligas/detalle/'.$id.'#groups');
    } else
      return redirect('home');
  }

  /**
   * Gestionar la inscripción del equipo
   *
   * @param id de la inscripción
   * @param resultado de la inscripción
   */
  public function manageInscription($result, $id) {
    //Se comprueba que la inscripción exista
    $inscripcion = LeagueTeam::find($id);

    //Sólo los comisionados y administradores pueden aceptar inscripciones
    if ($this->userRol != 3 && $inscripcion != null) {
      $inscripcion->manage($result);
      return redirect('/ligas/detalle/'.$inscripcion->liga.'#applicants');
    } else
      return redirect('home');
  }

  /**
   * Grabación de la solicitud de inscripción
   *
   * @param id con el identificador de la liga a la que se inscribe
   * @param request con los datos del formulario
   */
  public function saveApply($id, Request $request) {

    $league = League::find($id);
    $team = Team::find($request->input("equipo"));

    if ($league != null && $team != null && $league->abierta && !$league->iniciada
      && !$league->finalizada) {
      $league->apply($team);
      return redirect('/ligas/detalle/'.$id);
    } else
      return redirect('home');
  }

  /**
   * Método que inicia la liga
   *
   * @param id de la liga que se va a iniciar
   */
  public function start($id) {
    $league = League::find($id);

    if($this->userRol != 3 && $league != null && !$league->abierta && !$league->finalizada) {
      $league->start();
      return redirect('/ligas/detalle/'.$id);
    } else {
      return redirect('home');
    }
  }

//Fin de resto de métodos------------------------------------------------------

}
