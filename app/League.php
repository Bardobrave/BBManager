<?php

namespace App;

use Illuminate\database\Eloquent\Model;
use Illuminate\Http\Request as controllerRequest;
use Illuminate\Support\Facades\DB;

class League extends Model
{
    //Tabla del modelo
    protected $table = "LIGAS";

    //Relación muchos a muchos con equipos
    public function teams() {
      return $this->belongsToMany('App\Team', 'LIGAS_EQUIPOS', 'liga', 'equipo');
    }

    //Relación uno a muchos con LIGAS_EQUIPOS
    public function leagueTeams() {
      return $this->hasMany('App\LeagueTeam', 'liga');
    }

    //Relación uno a muchos con jornadas
    public function weeks() {
      return $this->hasMany('App\Weeks', 'liga');
    }

    //Métodos de funcionalidad -----------------------------------------------

    /**
     * Método para crear la inscripción de un equipo en la liga
     *
     * @param equipo que se quiere inscribir
     */
    public function apply($team) {
      //Se calcula el número de ligas jugadas por el equipo
      $ligasJugadas = $team->leagues->count();

      //Si el equipo no está activo no puede inscribirse
      if (!$team->activo)
        return;

      /*Si el equipo es nuevo y no ha jugado ninguna liga, su presupuesto debe ser
       menor o igual que el máximo presupuesto de la liga*/
      if ($ligasJugadas == 0 && $team->presupuesto > $this->maximopresupuesto)
        return;

      //Si la liga es de novatos el equipo no puede haber jugado en otra liga
      if ($this->liganovatos && $ligasJugadas != 0)
        return;

      /*Si el equipo ha jugado ya tres ligas o está inscrito en una que no ha
       finalizado todavía, no puede inscribirse*/
      if ($ligasJugadas >= 3 || $team->leagues->where("finalizada", 0)->count() > 0)
        return;

      //Si no se dan los casos anteriores, puede crearse la inscripción
      $inscripcion = new LeagueTeam;
      $inscripcion->addToLeague($this->id, $team->id);
    }

    /**
     * Método para reordenar los equipos en los grupos
     *
     * @param grupos matriz con los equipos que pertenecen a cada grupo
     */
    public function arrangeGroups($grupos) {

      for($x = 1; $x <= $this->numgrupos; $x++) {
        foreach($grupos[$x] as $teamId) {
          $leagueTeam = $this->leagueTeams->where("equipo", $teamId)->first();
          $leagueTeam->grupo = $x;
          $leagueTeam->save();
        }
      }
    }

    /**
     * Método que calcula las estadísticas de la liga
     *
     * @return stats array de arrays con las estadísticas
     */
    public function calculateStats() {
      //Calculando los tres equipos de la liga con más TD a favor durante la misma
      $scorers = DB::select(
          DB::raw(
            'SELECT e.nombre, sum(pe.tdf) as td'
              .' FROM EQUIPOS e'
              .' INNER JOIN PARTIDOS_EQUIPOS pe on pe.equipo = e.id'
              .' INNER JOIN PARTIDOS p on p.id = pe.partido'
              .' INNER JOIN JORNADAS j on j.id = p.jornada'
              .' WHERE j.liga = '.$this->id
              .' GROUP BY e.nombre ORDER BY td DESC LIMIT 3'
          )
      );

      //Calculando los tres equipos más defensivos
      $defenders = DB::select(
          DB::raw(
            'SELECT e.nombre, sum(pe.tdc) as td'
              .' FROM EQUIPOS e'
              .' INNER JOIN PARTIDOS_EQUIPOS pe on pe.equipo = e.id'
              .' INNER JOIN PARTIDOS p on p.id = pe.partido'
              .' INNER JOIN JORNADAS j on j.id = p.jornada'
              .' WHERE j.liga = '.$this->id
              .' GROUP BY e.nombre ORDER BY td ASC LIMIT 3'
          )
      );

      //Calculando los tres equipos con más bajas provocadas
      $injurers = DB::select(
          DB::raw(
            'SELECT e.nombre, SUM(pe.mf) as muertos, SUM(pe.hf) as heridos, (SUM(pe.mf) * 2) + SUM(pe.hf) as coeficiente'
              .' FROM EQUIPOS e '
              .' INNER JOIN PARTIDOS_EQUIPOS pe ON pe.equipo = e.id'
              .' INNER JOIN PARTIDOS p ON p.id = pe.partido'
              .' INNER JOIN JORNADAS j ON j.id = p.jornada'
              .' WHERE j.liga = '.$this->id
              .' GROUP BY e.nombre ORDER BY coeficiente DESC LIMIT 3 '
          )
      );

      //Calculando los equipos más pupas
      $stitchys = DB::select(
          DB::raw(
            'SELECT e.nombre, SUM(pe.mc) as muertos, SUM(pe.hc) as heridos, (SUM(pe.mc) * 2) + SUM(pe.hc) as coeficiente'
              .' FROM EQUIPOS e '
              .' INNER JOIN PARTIDOS_EQUIPOS pe ON pe.equipo = e.id'
              .' INNER JOIN PARTIDOS p ON p.id = pe.partido'
              .' INNER JOIN JORNADAS j ON j.id = p.jornada'
              .' WHERE j.liga = '.$this->id
              .' GROUP BY e.nombre ORDER BY coeficiente DESC LIMIT 3 '
          )
      );

      //Calculando los equipos más pasadores
      $throwers = DB::select(
          DB::raw(
            'SELECT e.nombre, SUM(pe.pases) as pases, SUM(pe.yardaspase) as yardas, (SUM(pe.pases) * 3) + SUM(pe.yardaspase) as coeficiente'
              .' FROM EQUIPOS e '
              .' INNER JOIN PARTIDOS_EQUIPOS pe ON pe.equipo = e.id'
              .' INNER JOIN PARTIDOS p ON p.id = pe.partido'
              .' INNER JOIN JORNADAS j ON j.id = p.jornada'
              .' WHERE j.liga = '.$this->id
              .' GROUP BY e.nombre ORDER BY coeficiente DESC LIMIT 3 '
          )
      );

      //Calculando los equipos más interceptores
      $interceptors = DB::select(
          DB::raw(
            'SELECT e.nombre, SUM(pe.intf) as intercepciones'
              .' FROM EQUIPOS e '
              .' INNER JOIN PARTIDOS_EQUIPOS pe ON pe.equipo = e.id'
              .' INNER JOIN PARTIDOS p ON p.id = pe.partido'
              .' INNER JOIN JORNADAS j ON j.id = p.jornada'
              .' WHERE j.liga = '.$this->id
              .' GROUP BY e.nombre ORDER BY intercepciones DESC LIMIT 3 '
          )
      );

      //Calculando los jugadores más anotadores
      $playerScorers = DB::select(
        DB::raw(
          'SELECT ju.nombre, e.nombre as equipo, COUNT(a.id) as td'
            .' FROM JUGADORES ju'
            .' INNER JOIN ANOTACIONES a ON a.jugadoractivo = ju.id'
            .' INNER JOIN TIPOS_ANOTACIONES ta ON ta.id = a.tipo and ta.tipo = \'TD\''
            .' INNER JOIN PARTIDOS_EQUIPOS pe ON pe.id = a.partido'
            .' INNER JOIN PARTIDOS p ON p.id = pe.partido'
            .' INNER JOIN JORNADAS j ON j.id = p.jornada'
            .' INNER JOIN EQUIPOS e ON e.id = ju.equipo'
            .' WHERE j.liga = '.$this->id
            .' GROUP BY e.nombre, ju.nombre ORDER BY td DESC LIMIT 3'
        )
      );

      //Calculando los jugadores más violentos
      $playerInjurers = DB::select(
        DB::raw(
          'SELECT ju.nombre, e.nombre as equipo, '
            .' SUM(CASE WHEN a.efecto < 41 THEN 1 ELSE 0 END) as heridos_leves,'
            .' SUM(CASE WHEN a.efecto >= 41 AND a.efecto < 61 THEN 1 ELSE 0 END) as heridos_graves,'
            .' SUM(CASE WHEN a.efecto >= 61 THEN 1 ELSE 0 END) as muertos,'
            .' SUM(CASE WHEN a.efecto < 41 THEN 1 WHEN a.efecto >= 41 AND a.efecto < 61 THEN 2 WHEN a.efecto >= 61 THEN 3 ELSE 0 END) as coeficiente'
            .' FROM JUGADORES ju'
            .' INNER JOIN ANOTACIONES a ON a.jugadoractivo = ju.id'
            .' INNER JOIN TIPOS_ANOTACIONES ta ON ta.id = a.tipo and ta.tipo = \'HERIDO\''
            .' INNER JOIN PARTIDOS_EQUIPOS pe ON pe.id = a.partido'
            .' INNER JOIN PARTIDOS p ON p.id = pe.partido'
            .' INNER JOIN JORNADAS j ON j.id = p.jornada'
            .' INNER JOIN EQUIPOS e ON e.id = ju.equipo'
            .' WHERE j.liga = '.$this->id
            .' GROUP BY e.nombre, ju.nombre ORDER BY coeficiente DESC LIMIT 3 '
        )
      );

      //Calculando los jugadores más pupas
      $playerStitchys = DB::select(
        DB::raw(
          'SELECT ju.nombre, e.nombre as equipo, COUNT(a.id) as heridas'
            .' FROM JUGADORES ju'
            .' INNER JOIN ANOTACIONES a ON a.jugadoractivo = ju.id'
            .' INNER JOIN TIPOS_ANOTACIONES ta ON ta.id = a.tipo and ta.tipo = \'LESIONADO\''
            .' INNER JOIN PARTIDOS_EQUIPOS pe ON pe.id = a.partido'
            .' INNER JOIN PARTIDOS p ON p.id = pe.partido'
            .' INNER JOIN JORNADAS j ON j.id = p.jornada'
            .' INNER JOIN EQUIPOS e ON e.id = ju.equipo'
            .' WHERE j.liga = '.$this->id
            .' GROUP BY e.nombre, ju.nombre ORDER BY heridas DESC LIMIT 3 '
        )
      );

      //Calculando los mejores pasadores
      $playerThrowers = DB::select(
        DB::raw(
          'SELECT ju.nombre, e.nombre as equipo, COUNT(a.id) as pases, SUM(a.efecto) as yardasPase,'
            .' (COUNT(a.id)*3) + SUM(a.efecto) as coeficiente'
            .' FROM JUGADORES ju'
            .' INNER JOIN ANOTACIONES a ON a.jugadoractivo = ju.id'
            .' INNER JOIN TIPOS_ANOTACIONES ta ON ta.id = a.tipo and ta.tipo = \'PASE\''
            .' INNER JOIN PARTIDOS_EQUIPOS pe ON pe.id = a.partido'
            .' INNER JOIN PARTIDOS p ON p.id = pe.partido'
            .' INNER JOIN JORNADAS j ON j.id = p.jornada'
            .' INNER JOIN EQUIPOS e ON e.id = ju.equipo'
            .' WHERE j.liga = '.$this->id
            .' GROUP BY e.nombre, ju.nombre ORDER BY coeficiente DESC LIMIT 3'
          )
      );

      //Calculando el público más sanguinario
      $bloodyFans = DB::select(
        DB::raw(
          'SELECT e.nombre, COUNT(a.id) as bajas'
            .' FROM ANOTACIONES a'
            .' INNER JOIN TIPOS_ANOTACIONES ta ON ta.id = a.tipo AND ta.tipo = \'LESIONADO\''
            .' INNER JOIN PARTIDOS_EQUIPOS pe ON pe.id = a.partido'
            .' INNER JOIN PARTIDOS p ON p.id = pe.partido'
            .' INNER JOIN JORNADAS j ON j.id = p.jornada'
            .' INNER JOIN EQUIPOS e ON (e.id = p.anfitrion OR e.id = p.visitante)'
            .' INNER JOIN JUGADORES ju ON (ju.id = a.jugadoractivo AND ju.equipo <> e.id)'
            .' WHERE j.liga = '.$this->id.' AND a.efecto LIKE \'%público%\''
            .' GROUP BY e.nombre ORDER BY bajas DESC LIMIT 3'
        )
      );

      //Calculando el equipo más sucio
      $dirtyPlayers = DB::select(
        DB::raw(
          'SELECT e.nombre, COUNT(a.id) as bajas'
            .' FROM ANOTACIONES a'
            .' INNER JOIN TIPOS_ANOTACIONES ta ON ta.id = a.tipo AND ta.tipo = \'LESIONADO\''
            .' INNER JOIN PARTIDOS_EQUIPOS pe ON pe.id = a.partido'
            .' INNER JOIN PARTIDOS p ON p.id = pe.partido'
            .' INNER JOIN JORNADAS j ON j.id = p.jornada'
            .' INNER JOIN EQUIPOS e ON (e.id = p.anfitrion OR e.id = p.visitante)'
            .' INNER JOIN JUGADORES ju ON (ju.id = a.jugadoractivo AND ju.equipo <> e.id)'
            .' WHERE j.liga = '.$this->id.' AND a.efecto LIKE \'%falta%\''
            .' GROUP BY e.nombre ORDER BY bajas DESC LIMIT 3'
        )
      );

      return ["scorers" => $scorers, "defenders" => $defenders, "injurers" => $injurers,
          "stitchys" => $stitchys, "throwers" => $throwers, "interceptors" => $interceptors,
          "playerScorers" => $playerScorers, "playerInjurers" => $playerInjurers,
          "playerStitchys" => $playerStitchys, "playerThrowers" => $playerThrowers,
          "bloodyFans" => $bloodyFans, "dirtyPlayers" => $dirtyPlayers];
    }

    /**
     * Método para crear una liga nueva
     *
     * @param request con los datos que se pasaron por el formulario
     * @return id de la liga recién creada
     */
    public function create(controllerRequest $request) {

        $this->nombre = $request->input("nombre");
        $this->descripcion = $request->input("descripcion");
        $this->liganovatos = ($request->input("liganovatos") == 1) ? 1 : 0;
        $this->maximopresupuesto = $request->input("maximopresupuesto");
        $this->numgrupos = $request->input("numgrupos");
        $this->cruzargrupos = ($request->input("cruzargrupos") == 1) ? 1 : 0;
        $this->idavueltagrupo = ($request->input("idavueltagrupo") == 1) ? 1 : 0;
        $this->idavueltatodos = ($request->input("idavueltatodos") == 1) ? 1 : 0;
        $this->jornadadescanso = ($request->input("jornadadescanso") == 1) ? 1 : 0;
        $this->puntosvictoria = $request->input("puntosvictoria");
        $this->puntosempate = $request->input("puntosempate");
        $this->puntosderrota = $request->input("puntosderrota");
        $this->save();

        //Se retorna el id del equipo recién creado
        return League::where("nombre", $request->input("nombre"))->first()->id;
    }

    /**
     * Método para modificar los datos de una liga
     *
     * @param request con los datos que se pasaron por el formulario
     */
    public function edit(controllerRequest $request) {

        $this->nombre = $request->input("nombre");
        $this->descripcion = $request->input("descripcion");
        //Si la liga aún no tiene equipos apuntados, se puede cambiar el tipo de liga y su presupuesto base
        if ($this->leagueTeams->count() == 0) {
          $this->liganovatos = ($request->input("liganovatos") == 1) ? 1 : 0;
          $this->maximopresupuesto = $request->input("maximopresupuesto");
        }
        //Si la liga aún no ha comenzado, se pueden modificar sus condiciones de juego
        if (!$this->iniciada) {
          $this->numgrupos = $request->input("numgrupos");
          /*Si ya se han asignado grupos y se escoge un número menor de grupos,
            aquellos equipos asignados a grupos que desaparecen deben reasignarse
            al último grupo*/
          foreach ($this->leagueTeams->where('grupo', '>', $this->numgrupos) as $leagueTeam) {
            $leagueTeam->grupo = $this->numgrupos;
            $leagueTeam->save();
          }
          $this->cruzargrupos = ($request->input("cruzargrupos") == 1) ? 1 : 0;
          $this->idavueltagrupo = ($request->input("idavueltagrupo") == 1) ? 1 : 0;
          $this->idavueltatodos = ($request->input("idavueltatodos") == 1) ? 1 : 0;
          $this->jornadadescanso = ($request->input("jornadadescanso") == 1) ? 1 : 0;
          $this->puntosvictoria = $request->input("puntosvictoria");
          $this->puntosempate = $request->input("puntosempate");
          $this->puntosderrota = $request->input("puntosderrota");
        }
        $this->save();
    }

    /**
     * Método que finaliza una liga
     */
    public function finish() {
      $this->finalizada = 1;
      $this->save();
    }

    /**
     * Método para listar todas las ligas
     *
     * @param request con las características de la búsqueda (ordenacion, paginado)
     * @return array con los datos que requerirá la vista para constuir el listado
     */
    public static function listAll(controllerRequest $request) {
      $sort = $request->input('sort');
      $ascdesc = $request->input('ascdesc');
      $page = $request->input('page');
      if ($sort != null)
        $leagues = League::orderBy($sort, $ascdesc)->paginate(10);
      else
        $leagues = League::paginate(10);
      return ["leagues" => $leagues, "sort" => $sort,
        "ascdesc" => $ascdesc, "page" => ($page == null) ? 1 : $page];
    }

    /**
     * Método para abrir y cerrar la fase de inscripciones de una liga
     *
     * @param operation que indica si la abrimos o la cerramos
     */
    public function toggleInscriptions($operation) {
      switch ($operation) {
        case 'abrir': $this->abierta = 1;
                      $this->save();
                      break;
        case 'cerrar': $this->abierta = 0;
                       $this->save();
                       break;
      }
    }

    /**
     * Método que inicializa una liga
     */
    public function start() {
      $this->iniciada = 1;
      $this->save();
    }
}
