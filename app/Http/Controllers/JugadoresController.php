<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\validatePlayers;
use App\Player;
use App\Skill;

class JugadoresController extends Controller
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

//Gestión de llamadas AJAX-----------------------------------------------------

/**
 * Método para obtener las habilidades disponibles para un jugador según su subida
 *
 * @param id del jugador que está subiendo de nivel
 * @param subida que está teniendo, con dos posibles valores: 'normal' y 'doble'
 *
 * @return html con el código de las option del selector de habilidad
 */
public function getSkills($id, $subida) {
  //Primero se comprueba que el jugador existe
  $player = Player::find($id);

  $responseHtml = "";

  if($player != null) {
    $validCategories = ($subida == "doble") ? $player->position->positionalCategories
      : $player->position->positionalCategories->where('requieredoble', 0);
    foreach($validCategories as $posCategory) {
      $responseHtml .= '<optgroup label="'.$posCategory->category->nombre.'">';
      foreach($posCategory->category->skills as $skill)
        if ($player->skills->find($skill->id) == null)
          $responseHtml .= '<option value="'.$skill->id.'">'.$skill->nombre.'</option>';
      $responseHtml .= '</optgroup>';
    }
  }

  return $responseHtml;
}

//Fin de Gestión de llamadas AJAX----------------------------------------------

//Métodos de gestión CRUD------------------------------------------------------

    /**
     * Dirigir al formulario de edición de un jugador
     *
     * @param id del jugador que se va a editar
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function edit($id) {

        //Se carga el jugador
        $player = Player::find($id);

        //Sólo administradores y comisionados pueden editar jugadores
        if ($player != null && $this->userRol != 3) {
          return view('jugadores/editar', ["player" => $player, "skills"
            => Skill::all()->sortBy('nombre_corto')]);
        } else {
          return redirect('home');
        }
    }

    /**
     * Grabar los cambios realizados sobre un jugador
     *
     * @param id del jugador que se va a editar
     * @param request con los datos enviados por el formulario
     * @return \Illuminate\Contracts\Support\Renderable
    */
    public function saveEdit($id, validatePlayers $request) {

        //El Request se encarga de comprobar las autorizaciones
        $player = Player::find($id);

        //Si el jugador no existiera se redirigiría al home
        if ($player == null)
          return redirect('home');

        //Se llama al método que actualiza los datos
        $player->edit($request);

        //Y se redirige a la página de detalle del equipo actualizado
        return redirect('equipos/detalle/'.$player->equipo);
    }

//Fin de métodos de Gestión CRUD-----------------------------------------------

//Inicio de otros métodos -----------------------------------------------------

  /**
   * Método que gestiona la subida de un jugador
   *
   * @param id del jugador que está subiendo de nivel
   * @param subida que indica qué está subiendo, si una habilidad o una característica
   *
   * @return \Illuminate\Contracts\Support\Renderable
   */
  public function uplevel($id, $subida) {
    //Primero se comprueba que el jugador existe y se tiene permiso para subirlo
    $player = Player::find($id);

    if ($player != null && $player->px >= $player->getPxToNextLevel()
      && ($this->userRol != 3 || $this->userId == $player->team->usuario)) {
      $player->uplevel($subida);
      return redirect('equipos/detalle/'.$player->equipo);
    } else
      return redirect("home");
  }

//Fin de otros métodos---------------------------------------------------------

}
