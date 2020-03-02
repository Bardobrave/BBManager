<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\validateTeams;
use App\Team;
use App\Race;
use App\Player;

class EquiposController extends Controller
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

//--- Gestión de llamadas AJAX---------------------------------------------------
    /**
     * Gestión de la llamada AJAX para controlar si un nombre está repetido en base de datos
     *
     * @param nombre del equipo que hay que comprobar
     * @param id del equipo que, de existir, se excluye de la búsqueda
     * @return boolean
     */
    public function checkSingleName($nombre, $id = null) {
      if ($id != null)
        $team = Team::where('nombre', $nombre)->where('id', '<>', $id)->first();
      else
        $team = Team::where('nombre', $nombre)->first();
      return (($team != null) ? 'true' : 'false');
    }

    /**
     * Gestión de la llamada AJAX para controlar si el nombre de un jugador está repetido en el equipo
     *
     * @param nombre del jugador que hay que comprobar
     * @param id del equipo en el que hay que buscar
     * @return boolean
     */
    public function checkSinglePlayer($nombre, $idEquipo, $idJugador = null) {
      if ($idJugador != null)
        $player = Player::where("equipo", $idEquipo)->where("id", "<>", $idJugador)
          ->where("nombre", $nombre)->first();
      else
        $player = Player::where("equipo", $id)->where("nombre", $nombre)->first();
      return (($player != null) ? 'true' : 'false');
    }

//----FIN de Gestión de llamadas AJAX-------------------------------------------

//Métodos específicos de Listas, Creación, Detalle, Edición y Borrado-----------

    /**
     * Mostrar una lista de todos los equipos.
     *
     * @param request con las características de la búsqueda
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function list(Request $request)
    {
        //Sólo los administradores y comisionados pueden ver un listado global de todos los equipos
        if ($this->userRol != 3) {

          /*Dado que el controlador de listas es un posible punto de retorno,
            se almacena en sesión como última página visitada */
          session(['lastVisited' => '/equipos/lista']);

          return view('equipos/lista', Team::listAll($request));
        } else {
          return redirect('equipos/listaPorUsuario/'.$this->userId);
        }
    }

    /**
     * Mostrar una lista de todos los equipos de un determinado usuario.
     *
     * @param id del usuario cuyos equipos se van a listar
     * @param request con las características de la búsqueda
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function listByUser($id, Request $request)
    {
        /*La lista de equipos de un jugador sólo puede ser vista por administradores,
          comisionados o por el propio jugador */
        if ($this->userRol != 3 || $this->userId == $id) {

          /*Dado que el controlador de listas es un posible punto de retorno,
            se almacena en sesión como última página visitada */
          session(['lastVisited' => '/equipos/listaPorUsuario/'.$id]);

          return view('equipos/lista', Team::listByUser($request, $id));
        } else {
          return redirect('equipos/listaPorUsuario/'.$userId);
        }
    }

    /**
     * Mostrar el detalle de un equipo.
     *
     * @param id del equipo cuyo detalle se va a mostrar
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function detail($id)
    {
        //Primero se carga el equipo
        $team = Team::find($id);

        //Todo el mundo puede ver el detalle de un equipo cualquiera, siempre que existiera
        if ($team != null) {

          /*Se actualiza el valor de sesión de última página visitada*/
          session(['lastVisited' => '/equipos/detalle/'.$id]);

          return view('equipos/detalle', ["team" => $team, "aspirantes" => $team->getAspirantes()]);
        } else {
          return redirect('home');
        }
    }

    /**
     * Crear un equipo nuevo
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create() {
       return view('equipos/crear', [ "razas" => Race::orderBy('nombre')->get()]);
    }

    /**
     * Grabar un equipo nuevo
     *
     * @param request con los datos del formulario
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function saveCreate(validateTeams $request) {

        $team = new Team;
        $id = $team->crear($this->userId, $request);

        //Se redirige al detalle del nuevo equipo
        return redirect('equipos/detalle/'.$id);
    }

    /**
     * Dirigir al formulario de edición de un equipo
     *
     * @param id del equipo que se va a editar
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function edit($id) {

        //Se carga el equipo
        $team = Team::find($id);

        /*Si solicita la edición el administrador, un comisionado, o el propio
          usuario dueño del equipo que se intenta editar, y dicho equipo existe,
          se redirige al formulario. Si no se le saca al home */
        if ($team != null && ($this->userRol != 3 || $team->user->id == $this->userId)) {
          return view('equipos/editar', ["team" => $team, "razas" => Race::orderBy('nombre')->get()]);
        } else {
          return redirect('home');
        }
    }

    /**
     * Grabar los cambios realizados sobre un equipo
     *
     * @param id del equipo que se va a editar
     * @param request con los datos enviados por el formulario
     * @return \Illuminate\Contracts\Support\Renderable
    */
    public function saveEdit($id, validateTeams $request) {

        //El Request se encarga de comprobar las autorizaciones
        $team = Team::find($id);

        //Si el equipo no existiera se redirigiría al home
        if ($team == null)
          return redirect('home');

        //Se llama al método que actualiza los datos
        $team->edit($this->userRol, $request);

        //Y se redirige a la página de detalle del equipo actualizado
        return redirect('equipos/detalle/'.$team->id);
    }

    /**
     * Mostrar la páina de confirmación de borrado de un equipo
     *
     * @param id del equipo que se quiere eliminar
     * @return \Illuminate\Contracts\Support\Renderable
    */
    public function delete($id){

      //Se comprueba que el equipo existe
      $team = Team::find($id);

      /*Sólo el administrador, los comisionados y los dueños, pueden eliminar
        equipos, que deben existir y no haber jugado, o estar jugando, ninguna liga */
      if ($team != null && ($this->userRol != 3 || $team->usuario()->id == $this->userId)
          && $team->leagues->count() == 0) {
        return view('equipos/borrar', [ "team" => $team ]);
      } else {
        return redirect('home');
      }
    }

    /**
     * Eliminar un equipo
     *
     * @param id del equipo que se va a eliminar
     * @return \Illuminate\Contracts\Support\Renderable
    */
    public function saveDelete($id){

      //Se comprueba que el equipo existe
      $team = Team::find($id);

      /*Sólo el administrador, los comisionados y los dueños, pueden eliminar
        equipos, que deben existir y no haber jugado, o estar jugando, ninguna liga */
      if ($team != null && ($this->userRol != 3 || $team->usuario()->id == $this->userId)
          && $team->leagues()->count() == 0) {

        //Eliminación del equipo
        $team->eliminar();

        //Se redirige al listado de equipos
        return redirect('equipos/lista');
      } else {
        return redirect('home');
      }
    }

//Fin de métodos CRUD----------------------------------------------------------

//Resto de métodos extra de gestión-------------------------------------------
    /**
     * Método para activar un equipo recién creado
     *
     * @param id del equipo que se va a activar
     */
    public function activate($id) {

      $team = Team::find($id);

      /*Si el equipo existe y el usuario en sesión
        es administrador comisionado o el dueño del equipo, puede activarlo*/
      if ($team != null && ($this->userRol != 3 || $this->userId == $team->user->id)) {
        $team->activate();
      }

      return redirect('equipos/detalle/'.$team->id);
    }

    /**
     * Guardar los datos del nuevo jugador añadido
     *
     * @param id del equipo al que se añade el nuevo jugador
     * @param request con los datos del nuevo jugador
     */
    public function addPlayer($id, validateTeams $request) {

      //Se carga el equipo
      $team = Team::find($id);

      //Si el equipo existe se añade el jugador
      if ($team != null)
        $team->addPlayer($request);

      return redirect('equipos/detalle/'.$id);
    }

    /**
     * Despedir a un jugador del equipo
     *
     * @param id del jugador que se va a despedir
     */
    public function firePlayer($id, Request $request) {

      //Se carga el jugador a Eliminar
      $player = Player::find($id);

      //Si el jugador existe
      if ($player != null) {
        //Se carga el equipo
        $team = Team::find($player->equipo);

        $team->firePlayer($player);

        return redirect('equipos/detalle/'.$team->id);
      }
    }

    /**
     * Método para añadir o eliminar elementos al equipo: ayudantes, cheerleaders, rerolls o médico
     *
     * @param elemento que se va a añadir
     * @param id del equipo al que se le va a añadir
     * @param add booleano indicando si se añade o se elimina
     */
    public function manageElements($id, $elemento, $add) {

      //Lo primero se comprueba que el equipo existe
      $team = Team::find($id);

      //Si el equipo existe y el usuario en sesión tiene permiso para editarlo
      if ($team != null && ($this->userRol != 3 || $team->user->id == $this->userId)) {

        //Se comprueba qué elemento nos están pidiendo añadir
        switch ($elemento) {
          case "ayudante":
              ($add) ? $team->addTrainer() : $team->removeTrainer();
            break;
          case "animadora":
              ($add) ? $team->addCheerleader() : $team->removeCheerleader();
            break;
          case "reroll":
              ($add) ? $team->addReroll() : $team->removeReroll();
            break;
          case "apotecario":
              ($add) ? $team->addApothecary() : $team->removeApothecary();
            break;
        }

      }

      //Al finalizar, ya se haya hecho o no la modificación, se devuelve al detalle del equipo
      return redirect('equipos/detalle/'.$id);
    }

    /**
     * Método para dejar preparado para jugar un equipo recién creado
     *
     * @param id del equipo que se va a dejar preparado
     */
    public function prepare($id) {

      $team = Team::find($id);

      /*Si el equipo existe, no está preparado y el usuario en sesión
        es administrador comisionado o el dueño del equipo, puede pasarlo a preparado*/
      if ($team != null && $team->preparado == 0
        && ($this->userRol != 3 || $this->userId == $team->user->id)) {
        $team->prepare();
      }

      return redirect('equipos/detalle/'.$team->id);
    }
}
