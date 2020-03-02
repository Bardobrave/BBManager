<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PartidosController extends Controller
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
        $this->middleware('auth.check');
    }

//Métodos de gestión AJAX------------------------------------------------------


//Fin de gestión de métodos AJAX-----------------------------------------------

//Gestión de métodos CRUD------------------------------------------------------

    /**
     * Mostrar el detalle de un partido.
     *
     * @param id del partido cuyo detalle se va a mostrar
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function detail($id)
    {
        //Primero se carga el usuario
        $match = Matches::find($id);

        /* Si el usuario que solicita el detalle es administrador, comisionado
           o uno de los jugadores que han jugado el partido */
        if ($match != null && ($this->userRol != 3 || $match->local->user->id
          == $this->userId || $match->away->user->id == $this->userId)) {

          /*Se actualiza el valor de sesión de última página visitada*/
          session(['lastVisited' => '/partidos/detalle/'.$id]);

          return view('partidos/detalle', ["match" => $match]);
        } else {
          return redirect('home');
        }
    }

    /**
     * Dirigir al formulario de edición del acta
     *
     * @param id del acta que se va a editar
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function edit($id) {

        //Se carga el acta
        $acta = MatchTeam::find($id);

        /*Si solicita la edición un administrador o comisionado, o el jugador
          del acta, se redirige al formulario, si no se le saca al home */
        if ($acta != null && ($this->userRol != 3 || $acta->team->id == $this->userId)) {
          return view('partidos/editar', ["acta" => $acta]);
        } else {
          return redirect('home');
        }
    }

    /**
     * Grabar los cambios realizados sobre un acta
     *
     * @param id del acta que se va a editar
     * @param request con los datos enviados por el formulario
     * @return \Illuminate\Contracts\Support\Renderable
    */
    public function saveEdit($id, Request $request) {

        $acta = MatchTeam::find($id);

        /*Si solicita la edición un administrador o comisionado, o el jugador
          del acta, se redirige al formulario, si no se le saca al home */
        if ($acta != null && ($this->userRol != 3 || $acta->team->id == $this->userId)) {
          $acta->edit($request);

          //Y se redirige a la página de detalle del partido actualizado
          return redirect('partidos/detalle/'.$acta->match->id);
        } else
          return redirect('home');
    }

    /**
     * Crear un acta nueva
     *
     * @param id del partido al que pertenece el acta
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create($id) {

      //Comprobación de que el partido existe
      $match = Matches::find($id);

     //Sólo el administrador y los jugadores del partido pueden iniciar un acta
     if ($match != null && ($this->userRol != 3 || $this->local->user->id
      == $this->userId || $this->away->user->id == $this->userId)) {
       return view('partidos/crear', [ "match" => $match]);
     } else {
       return redirect('home');
     }
    }

    /**
     * Grabar un acta nueva
     *
     * @param id del partido al que pertenece el acta
     * @param local booleano que indica qué equipo está gestionando su acta
     * @param request con los datos del formulario
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function saveCreate($id, $local, Request $request) {

      $match = Matches::find($id);

      if ($match != null && ($this->userRol != 3 || ($match->local->user->id
        == $this->userId && $local == 1) || ($match->away->user->id
        == $this->userId && $local == 0))) {

        $acta = new MatchTeam;
        $id = $acta->crear($match, $local, $this->userId, $request);

        //Se redirige al detalle del partido
        return redirect('partidos/detalle/'.$id);
      } else
        return redirect('home');
    }

//Fin de métodos de gestión CRUD-----------------------------------------------

//Resto de métodos de gestión--------------------------------------------------

}
