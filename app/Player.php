<?php

namespace App;

use Illuminate\database\Eloquent\Model;
use Illuminate\Http\Request as controllerRequest;
use Illuminate\Support\Facades\DB;

class Player extends Model
{
    //Tabla del modelo
    protected $table = "JUGADORES";

//Definición de relaciones del modelo-------------------------------------------

    //Relación con la posición del jugador
    public function position() {
      return $this->belongsTo('App\Positional', 'posicion');
    }

    //Relación muchos a muchos con las habilidades
    public function skills() {
      return $this->belongsToMany('App\Skill', 'HABILIDADES_JUGADORES', 'jugador', 'habilidad');
    }

    //Relación de pertenencia a un equipo
    public function team() {
      return $this->belongsTo('App\Team', 'equipo');
    }

//Fin de relaciones del modelo--------------------------------------------------

//Definición de métodos funcionales--------------------------------------------

    /**
     * Método para añadir un jugador de leva a un equipo
     *
     * @param id del equipo al que hay que añadirlo
     * @param position en la que juega el jugador
     */
    public function addLevaToTeam($id, $position, $numero) {
      $this->numero = $numero;
      $this->nombre = "leva";
      $this->equipo = $id;
      $this->posicion = $position->id;
      $this->ma = $position->ma;
      $this->fue = $position->fue;
      $this->agl = $position->agl;
      $this->av = $position->av;
      $this->precio = $position->precio;
      $this->save();

      //Se recupera el id del jugador recién creado
      $idPlayer = Player::where('equipo', $this->equipo)->where('numero', $numero)
        ->where("activo", 1)->first()->id;

      /*Tras crear el jugador, hay que crear tantas entradas de habilidades
        como indique su posicion */
      foreach($position->skills as $skill) {
        $playerSkill = new PlayerSkill;
        $playerSkill->addToPlayer($idPlayer, $skill->id);
      }

      //Hay que añadirle la habilidad de solitario, ya que todos los levas lo son
      $playerSkill = new PlayerSkill;
      $playerSkill->addToPlayer($idPlayer, Skill::where("nombre", "SOLITARIO")->first()->id);

      //Al finalizar retorna el id del nuevo jugador
      return $idPlayer;
    }

    /**
     * Método para añadir un jugador nuevo a un equipo
     *
     * @param id del equipo al que hay que añadirlo
     * @param position en la que juega el jugador
     *
     * @return idPlayer id del jugador recién creado.
     */
    public function addToTeam($id, $position, $request) {
      $this->numero = $request->input('numero');
      $this->nombre = $request->input('nombre');
      $this->equipo = $id;
      $this->posicion = $request->input('posicion');
      $this->ma = $position->ma;
      $this->fue = $position->fue;
      $this->agl = $position->agl;
      $this->av = $position->av;
      $this->precio = $position->precio;
      $this->save();

      //Se recupera el id del jugador recién creado
      $idPlayer = Player::where('equipo', $this->equipo)->where('nombre', $this->nombre)->first()->id;

      /*Tras crear el jugador, hay que crear tantas entradas de habilidades
        como indique su posicion */
      foreach($position->skills as $skill) {
        $playerSkill = new PlayerSkill;
        $playerSkill->addToPlayer($idPlayer, $skill->id);
      }

      //Al finalizar retorna el id del nuevo jugador
      return $idPlayer;
    }

    /**
     * Método para buscar y borrar todas las habilidades de un jugador
     *
     */
    public function deleteSkills() {

      //Se recorren todas sus habilidades en base de datos y se eliminan
      foreach(PlayerSkill::where("jugador", $this->id) as $skill)
        $skill->delete();
    }

    /**
     * Método para editar los datos de un jugador
     *
     * @param request con la información del formulario
     */
    public function edit($request) {
      /*Si el número está libre se graba, si el número lo tiene el propio jugador
        no hace falta grabarlo*/
      if ($this->team->players->where('activo', 1)->where('numero',
        $request->input('numero'))->count() == 0)
        $this->numero = $request->input('numero');

      //Si el nombre del jugador no está repetido en el equipo, se graba
      if ($this->team->players->where('nombre', $request->input('nombre'))->count() == 0)
        $this->nombre = $request->input('nombre');

      //Si la posición que se le asigna está libre, se graba
      if ($this->team->race->positionals->where('id', $request->input('posicion'))
        ->first()->maximo > $this->team->players->where('posicion',
        $request->input('posicion'))->count())
        $this->posicion = $request->input('posicion');

      //Se actualizan las habilidades del jugador en base al array que llega del formulario
      $this->updateSkills($request->input('skills'));

      //Se asignan valores a las características del jugador
      $this->ma = $request->input('ma');
      $this->fue = $request->input('fue');
      $this->agl = $request->input('agl');
      $this->av = $request->input('av');

      //Se actualizan los puntos de experiencia y el nivel
      $this->px = $request->input('px');
      $this->nivel = DB::table('NIVELES')->where('minpx', '<=', $this->px)->max('nivel');

      //Se actualiza el precio y las estadísticas del jugador
      $this->precio = $request->input('precio');
      $this->hf = $request->input('hf');
      $this->mf = $request->input('mf');
      $this->hc = $request->input('hc');
      $this->curado = $request->input('curado');
      $this->niggling = $request->input('niggling');
      $this->pases = $request->input('pases');
      $this->yardaspase = $request->input('yardaspase');
      $this->intercepciones = $request->input('intercepciones');
      $this->td = $request->input('td');
      $this->jugados = $request->input('jugados');
      $this->mvp = $request->input('mvp');
      $this->lesionado = ($request->input('lesionado') == 1);
      $this->activo = ($request->input('activo') == 1);
      $this->muerto = ($request->input('muerto') == 1);

      //Al modificar un jugador se debe actualizar el spike de su equipo
      $this->team->spike();

      //Se graban los cambios
      $this->team->save();
      $this->save();
    }

    /**
     * Método que elimina un jugador
     *
     */
    public function eliminar() {

      //Primero hay que eliminar todas sus habilidades
      $this->deleteSkills();

      //Luego se elimina el jugador
      $this->delete();
    }

    /**
     * Método que obtiene los px que necesita el jugador para llegar al siguiente nivel
     *
     * @return px a los que sube al siguiente Nivel
     */
    public function getPxToNextLevel() {
      return DB::select(
        DB::raw(
          'SELECT minpx '
            .'FROM NIVELES '
            .'WHERE nivel = '.($this->nivel + 1)
          )
        )[0]->minpx;
    }

    /**
     * Método que actualiza las habilidades de un jugador
     *
     * @param skills string con un array en formato json con los id de las skills
     *   que llegan del formulario
     */
    public function updateSkills($skills) {
      $aSkills = json_decode($skills);

      /*Hay que eliminar las habilidades que estén asociadas al jugador y no
        aparezcan en el array */
      foreach ($this->skills as $skillToErase) {
        if (!array_search($skillToErase->id, $aSkills))
          PlayerSkill::where('jugador', $this->id)->where('habilidad', $skillToErase->id)->first()->delete();
      }


      /*Para cada habilidad en el array que no esté asociada al jugador,
        hay que asociarla */
      foreach($aSkills as $skillToAdd) {
        //Si no hay esa habilidad asociada al jugador
        if (PlayerSkill::where('jugador', $this->id)->where('habilidad', $skillToAdd)->count() == 0) {
          $newSkill = new PlayerSkill;
          $newSkill->jugador = $this->id;
          $newSkill->habilidad = $skillToAdd;
          $newSkill->save();
        }
      }
    }

    /**
     * Método que ejecuta una subida en un jugador
     *
     * @param subida que indica cuál es la subida que se va a llevar a cabo
     */
    public function uplevel($subida) {
      switch ($subida) {
        case "ma": $this->ma++;
          $this->precio += 30000;
          break;

        case "fue": $this->fue++;
          $this->precio += 50000;
          break;

        case "agl": $this->agl++;
          $this->precio += 40000;
          break;

        case "av": $this->av++;
          $this->precio += 30000;
          break;

        default:
          //En otro caso se está subiendo una habilidad
          $newSkill = new PlayerSkill;
          $newSkill->habilidad = intval($subida);
          $newSkill->jugador = $this->id;
          $newSkill->save();
          $newSkill->refresh();
          $this->precio += ($this->position->positionalCategories->where("categoria",
            $newSkill->skill->categoria)->where("requieredoble", 0)->count() != 0)
            ? 20000 : 30000;
          break;
      }

      $this->nivel++;
      $this->save();
    }
}
