<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request as controllerRequest;
use Illuminate\Support\Facades\DB;

class Team extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nombre', 'raza', 'usuario', 'presupuesto',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
    ];

    protected $table = 'EQUIPOS';

//Definición de relaciones del modelo------------------------------------------

    /*
     * Definición de la relación muchos a muchos con ligas
     */
    public function leagues() {
      return $this->belongsToMany('App\League', 'LIGAS_EQUIPOS', 'equipo', 'liga');
    }

    /*
     * Definición de la relación uno a muchos con LIGAS_EQUIPOS
     */
    public function leaguesTeam() {
      return $this->hasMany('App\LeagueTeam', 'equipo');
    }

    /**
     * Definición de la relación con los jugadores
     */
    public function players() {
      return $this->hasMany('App\Player', 'equipo');
    }

    /**
     * Definición de la relación con la raza del equipo
     */
    public function race() {
      return $this->belongsTo('App\Race', 'raza');
    }

    /**
     * Definición de la relación con el usuario dueño del equipo
     */
    public function user() {
      return $this->belongsTo('App\User', 'usuario');
    }

//Fin de definición de relaciones del modelo------------------------------------

//Métodos de funcionalidad------------------------------------------------------

    /**
     * Método para activar un equipo
     *
     */
    public function activate() {

      //Si el equipo aún no está activado y tiene al menos 11 jugadores
      if ($this->activo == 0 && $this->players->count() > 10) {
        //Se activa el equipo
        $this->activo = 1;
        //Se pasa la tesorería sobrante al banco hasta el máximo de 200000
        $this->treasureSave();
        //Se lanza el cálculo del spike
        $this->spike();
        //Se graban los cambios
        $this->save();
      }
    }

    /**
     * Método para añadir un medico al equipo
     *
     */
    public function addApothecary() {

       if ($this->race->apotecario == 1 && $this->tesoreria + $this->banco >= 50000) {
         $this->apotecario = 1;
         $this->spend(50000);

         $this->spike();

         $this->save();
       }
    }

    /**
     * Método para añadir una animadora al equipo
     *
     */
     public function addCheerleader() {

         if ($this->tesoreria + $this->banco >= 10000) {
           $this->animadoras++;
           $this->spend(10000);

           $this->spike();

           $this->save();
         }
     }

    /**
     * Método para añadir un jugador de leva al equipo
     *
     */
    public function addLevas($cantidad) {

      $numero = 16;
      $position = $this->race->positionals->sortBy("precio")->first();
      for($x = 0; $x < $cantidad; $x++) {

        $player = new Player;

        //Se busca el número que debe llevar el jugador
        while ($this->players->where("numero", $numero)->where("activo", 1)->count() > 0)
         $numero--;

        //Se añade un nuevo jugador de la posición más barata
        $player->addLevaToTeam($this->id, $position, $numero);

        //Se reduce el número, ya que el leva que se ha creado ha adquirido ese número
        $numero--;
      }

      //Al finalizar el bucle se hace el cálculo de spike
      $this->spike();
      //Y se graba;
      $this->save();
    }

    /**
     * Método para añadir un jugador nuevo al equipo
     *
     * @param request con los datos del jugador que se añade
     *
     * @return id del jugador recién añadido
     */
    public function addPlayer($request) {

      $player = new Player;
      $idPlayer = 0;
      $position = $this->race->positionals->find($request->input('posicion'));

      /*Si el número del jugador no está repetido entre los activos, su
        nombre no está repetido en el equipo y su posición está libre, se añade*/
      /*Es necesario recurrir a una comparación sobre mayúsculas forzada porque parece
        que laravel, de algún modo, está ignorando el collation case insensitive de la bc*/
      if ($this->players->where("numero", $request->input("numero"))->where("activo", 1)->count() == 0
         && $this->players()->whereRaw("UPPER(nombre) = '".strtoupper($request->input("nombre"))."'")->count() == 0
         && $this->players->where("posicion", $request->input("posicion"))->where("activo", 1)->count()
           < $this->race->positionals->find($request->input("posicion"))->maximo) {

        //Obtenermos el id del nuevo jugador
        $idPlayer = $player->addToTeam($this->id, $position, $request);

        //Reducimos la tesoreria/banca del equipo en el valor del jugador
        $this->spend($player->precio);

        $this->spike();

        $this->save();
      } else
        session(["warning" => "Has intentado añadir un jugador con nombre o "
          ."número repetido, o con una posición que ya no está disponible"]);

      return $this;
    }

    /**
     * Método para añadir una reroll al equipo
     *
     */
    public function addReroll() {

       $liquido = $this->tesoreria + $this->banco;
       if ($this->rerolls <= 8 && (($this->activo == 0) ? $liquido >= $this->race->costerr
           : $liquido >= 2 * $this->race->costerr)) {
         $this->rerolls++;
         if ($this->activo == 0)
           $this->spend($this->race->costerr);
         else
           $this->spend(2 * $this->race->costerr);

         $this->spike();

         $this->save();
       }
    }

    /**
     * Método para añadir un ayudante de entrenador al equipo
     *
     */
     public function addTrainer() {

       if ($this->tesoreria + $this->banco >= 10000) {
         $this->ayudantes++;
         $this->spend(10000);

         $this->spike();

         $this->save();
       }
     }

    /**
     * Método para rellenar la información de un equipo nuevo
     *
     * @param idUsuario id del usuario que está creando el equipo
     * @param request con los datos pasados por el formulario
     * @return numeric
     */
     public function crear($idUsuario, $request) {
       $this->nombre = $request->input('nombre');
       $this->raza = $request->input('raza');
       $this->usuario = $idUsuario;
       $this->presupuesto = $request->input('presupuesto');
       $this->tesoreria = $this->presupuesto;

       //Se persiste el equipo nuevo en base de datos
       $this->save();

       //Se actualiza su spike
       $this->spike();

       //Se retorna el id del equipo recién creado
       return Team::where('nombre', $request->input('nombre'))->first()->id;
     }

    /**
     * Método para modificar los datos de un equipo
     *
     * @param userRol rol del usuario que solicita el cambio
     * @param request con los datos pasados por el formulario
     */
    public function edit($userRol, $request) {
      $this->nombre = $request->input('nombre');

      //Si el equipo no tiene jugadores, rerolls ni médico, puede cambiarse su raza
      if ($this->players->count() == 0 && $this->rerolls == 0 && $this->apotecario == 0)
        $this->raza = $request->input('raza');

      /*Puede cambiarse el presupuesto inicial siempre y cuando el equipo no esté activo
        y no se haya indicado un valor tal que la diferencia entre el valor nuevo y el
        original sea inferior a la cantidad de dinero que queda entre tesorería y banco.
        Es decir, si se reduce el presupuesto inicial, debe quedar dinero suficiente en
        las arcas para devolver el sobrante del presupuesto inicial, de lo contrario se
        estaría obteniendo dinero de forma fraudulenta. Los administradores y los
        comisionados pueden editar este valor sin restricciones, dando por sentado que
        saben lo que se hacen*/
      if (!$this->activo) {
        $diferencia = $this->presupuesto - $request->input('presupuesto');
        if ($userRol != 3 || $diferencia < 0 || $diferencia < $this->tesoreria + $this->banco) {
          $this->presupuesto = $request->input('presupuesto');

          //Al alterar el presupuesto original hay que alterar la tesoreria
          if ($diferencia < 0) {
            //Si la diferencia es negativa se está aumentando el presupuesto
            $this->tesoreria -= $diferencia; //Restamos el valor negativo, obteniendo una suma
          } else {
            //Si la diferencia es positiva se debe "gastar" dicha diferencia
            $this->spend($diferencia);
          }
        }
      }

      //Los administradores y comisionados pueden editar además otros valores
      if ($userRol != 3) {
        $this->ff = $request->input('ff');
        $this->tesoreria = $request->input('tesoreria');
        $this->banco = $request->input('banco');
        $this->jugados = $request->input('jugados');
        $this->ganados = $request->input('ganados');
        $this->perdidos = $request->input('perdidos');
        $this->empatados = $request->input('empatados');
        $this->tdf = $request->input('tdf');
        $this->tdc = $request->input('tdc');
        $this->hf = $request->input('hf');
        $this->hc = $request->input('hc');
        $this->mf = $request->input('mf');
        $this->mc = $request->input('mc');
        $this->pases = $request->input('pases');
        $this->yardaspase = $request->input('yardaspase');
        $this->intercepciones = $request->input('intercepciones');
        $this->intercepcionesc = $request->input('intercepcionesc');
      }

      //Recalcular el spike
      $this->spike();

      //Grabar los cambios
      $this->save();
    }

    /**
      * Método que elimina un equipo
      *
      */
     public function eliminar() {

       //Primero se eliminan todos los jugadores del equipo
       foreach ($this->players as $player) {
         $player->eliminar();
       }
       $this->delete();
     }

    /**
     * Método para despedir a un jugador
     *
     * @param player jugador que se va a despedir
     */
     public function firePlayer($player) {

       //Si el equipo no está activo, el jugador se elimina de base de datos
       if ($this->activo == 0) {
         //Primero deben borrarse sus habilidades
         $player->deleteSkills();

         //Se devuelve su coste a la tesorería
         $this->tesoreria += $player->position->precio;

         //Y se borra al jugador
         $player->delete();
       } else {
         /*Si el equipo está activo, el jugador no se borra, pasa a estar inactivo.
           De este modo se mantienen todas sus estadísticas, pero no se contabiliza
           para el spike ni aparece en la hoja general del equipo*/
         $player->activo = 0;
         $player->save();
       }

       //Se recalcula el spike sin el jugador eliminado y se guardan los cambios
       $this->spike();

       $this->save();
     }

     /**
      * Método que devuelve los 5 aspirantes más cercanos a subir de nivel
      *
      * @return aspirantes array con los 5 jugadores a los que faltan menos px para subir de nivel
      */
     public function getAspirantes() {
       $aspirantes = Player::hydrate(        //Convierte el resultado al modelo
         DB::select(                         //Lanza una consulta
           DB::raw(                          //Lanza una consulta en crudo
             'SELECT * '
                .'FROM JUGADORES '
                .'WHERE activo = 1 and equipo = '.$this->id.' '
                .'order by ((select max(minpx) from NIVELES where (nivel = JUGADORES.nivel + 1)) - px) '
                .'limit 5'
              )
            )
        );

        return $aspirantes;
     }

    /**
     * Método para listar todos los equipos
     *
     * @param request con las características de la búsqueda (ordenacion, paginado)
     * @return array con los datos que requerirá la vista para constuir el listado
     */
    public static function listAll(controllerRequest $request) {
      //Recoger datos del request
      $sort = $request->input('sort');
      $ascdesc = $request->input('ascdesc');
      $page = $request->input('page');
      $nombre = $request->input('nombre');
      $raza = $request->input('raza');
      $valoracionDesde = $request->input('valoracionDesde');
      $valoracionHasta = $request->input('valoracionHasta');

      //Filtrado de los datos
      $teams = Team::take(Team::count());

      if ($nombre != null)
        $teams = $teams->where('nombre', 'LIKE', '%'.$nombre.'%');

      if ($raza != 0)
        $teams = $teams->where('raza', $raza);

      if ($valoracionDesde != "" && is_numeric($valoracionDesde))
        $teams = $teams->where('valoracion', '>=', $valoracionDesde);

      if ($valoracionHasta != "" && is_numeric($valoracionHasta))
        $teams = $teams->where('valoracion', '<=', $valoracionHasta);

      if ($sort != null)
        $teams = $teams->orderBy($sort, $ascdesc)->paginate(10);
      else
        $teams = $teams->paginate(10);

      return ["teams" => $teams, "nombre" => $nombre, "raza" => $raza,
        "valoracionDesde" => $valoracionDesde, "valoracionHasta" => $valoracionHasta,
        "razas" => Race::all(), "sort" => $sort, "ascdesc" => $ascdesc,
        "page" => ($page == null) ? 1 : $page];
    }

    /**
     * Método para listar todos los equipos de un usuario
     *
     * @param request con las características de la búsqueda (ordenacion, paginado)
     * @param id del usuario cuyos equipos se van a listar
     * @return array con los datos que requerirá la vista para constuir el listado
     */
    public static function listByUser(controllerRequest $request, $id) {
      $sort = $request->input('sort');
      $ascdesc = $request->input('ascdesc');
      $page = $request->input('page');
      if ($sort != null)
        $teams = Team::where('usuario', $id)->orderBy($sort, $ascdesc)->paginate(10);
      else
        $teams = Team::where('usuario', $id)->paginate(10);
      return ["teams" => $teams, "sort" => $sort,
        "ascdesc" => $ascdesc, "page" => ($page == null) ? 1 : $page];
    }

    /**
     * Método para dejar preparado un equipo
     *
     */
    public function prepare() {

      //Si el equipo no está preparado
      if ($this->preparado == 0) {
        //Se contabilizan los levas necesarios
        $levas = 11 - $this->players->where("activo", 1)->where("lesionado", 0)->count();
        if ($levas > 0)
         $this->addLevas($levas);

        //Se marca como preparado
        $this->preparado = 1;
        //Se lanza el cálculo del spike
        $this->spike();
        //Se graban los cambios
        $this->save();
      }
    }

    /**
     * Método para quitar el médico al equipo
     *
     */
    public function removeApothecary() {

       if ($this->apotecario == 1) {
         $this->apotecario = 0;
         if ($this->activo == 0)
          $this->tesoreria += 50000;

         $this->spike();

         $this->save();
       }
    }

    /**
    * Método para reducir una cheerleader al equipo
    *
    */
    public function removeCheerleader() {

      if ($this->animadoras > 0) {
        $this->animadoras--;
        if ($this->activo == 0)
        $this->tesoreria += 10000;

        $this->spike();

        $this->save();
      }
    }

    /**
     * Método para reducir una reroll al equipo
     *
     */
     public function removeReroll() {

       if ($this->rerolls > 0) {
         $this->rerolls--;
         if ($this->activo == 0)
           $this->tesoreria += $this->race->costerr;

         $this->spike();

         $this->save();
       }
     }

     /**
      * Método para reducir un ayudante de entrenador al equipo
      *
      */
      public function removeTrainer() {

         if ($this->ayudantes > 0) {
           $this->ayudantes--;
           if ($this->activo == 0)
           $this->tesoreria += 10000;

           $this->spike();

           $this->save();
         }
      }

    /**
     * Método para restar una cantidad de la tesorería y el banco del equipo
     *
     * @param valor a descontar de la tesorería y el banco del equipo
     */
     public function spend($valor) {

       //Si el banco tiene dinero suficiente, se resta el valor del banco
       if ($this->banco >= $valor)
         $this->banco -= $valor;
       else {
         $valor -= $this->banco;
         $this->banco = 0;
         $this->tesoreria -= $valor;
       }
     }

    /**
    * Método para calcular el spike de un equipo
    *
    * @return numeric
    */
    public function spike() {
      $acumulado = 0;
      $players = Team::find($this->id)->players(); //Hay que traer el equipo de bd por si se ha actualizado
      $acumulado += $players->where('activo', 1)->where('muerto', 0)
       ->where('lesionado', 0)->sum('precio');

       //Hay que restar el valor base de los jugadores con la habilidad desechable
       $acumulado -= $players->whereHas('skills', function($query) {
         $query->where("nombre", "DESECHABLE");
       })->where('activo', 1)->where('muerto', 0)->where('lesionado', 0)->select('position')->sum('precio');

      $acumulado += ($this->rerolls * $this->race->costerr);
      $acumulado += ($this->ayudantes * 10000);
      $acumulado += ($this->animadoras * 10000);
      $acumulado += ($this->apotecario * 50000);
      $acumulado += $this->tesoreria;

      $this->valoracion = $acumulado / 10000;
    }

     /**
      * Método que pasa todo el dinero posible de la tesorería al banco
      *
      */
     public function treasureSave() {

       //Si hay sitio en el banco para cargar toda la tesorería
       if ($this->banco <= 200000 - $this->tesoreria) {
         $this->banco += $this->tesoreria;
         $this->tesoreria = 0;
       } else {
         //Si no, se traslada lo que se pueda hasta llenar el banco
         $this->tesoreria -= (200000 - $this->banco);
         $this->banco = 200000;
       }
     }

}
