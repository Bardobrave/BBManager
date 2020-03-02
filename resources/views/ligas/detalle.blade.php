@extends('layouts.app')

@push('scripts')
  <script src="{{ asset('js/jumpToTabs.js') }}" type="text/javascript" defer></script>
@endpush

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Detalle de la liga</div>
                @php( $inscrito = ($league->teams->where("usuario", Auth::user()->id)->count() != 0))
                <div class="card-body">
                    <h3>{{ $league->nombre }}</h3>
                    <div class="tab-content" id="infoLiga">
                      <ul class="nav nav-tabs" id="league-tab" role="tablist">
                        <li class="nav-item">
                          <a class="nav-link active" id="detail-tab" data-toggle="tab" href="#detail" aria-controls="detail" aria-selected="true">Detalle</a>
                        </li>
                        <!-- TO DO: Que la pestaña de inscritos sólo pueda verse cuando el jugador en sesión ya haya solicitado acceder a la liga-->
                        @if($league->abierta && $inscrito)
                          <li class="nav-item">
                            <a class="nav-link" id="applicants-tab" data-toggle="tab" href="#applicants" aria-controls="applicants" aria-selected="false">Inscritos</a>
                          </li>
                        @endif
                        @if(!$league->abierta)
                          <li class="nav-item">
                            <a class="nav-link" id="group-tab" data-toggle="tab" href="#groups" aria-control="groups" aria-selected="false">Grupos</a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link" id="calendar-tab" data-toggle="tab" href="#calendar" aria-control="calendar" aria-selected="false">Calendario</a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link" id="standings-tab" data-toggle="tab" href="#standings" aria-control="standings" aria-selected="false">Clasificación</a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link" id="statistics-tab" data-toggle="tab" href="#statistics" aria-control="statistics" aria-selected="false">Estadísticas</a>
                          </li>
                        @endif
                      </ul>
                      <!--DETAIL-->
                      <div class="tab-pane fade show active" id="detail" role="tabpanel" aria-labelledby="detail-tab">
                        <div class="form-group">
                          <strong>Descripción: </strong>{{ $league->descripcion }}
                        </div>
                        <div class="form-group">
                          <strong>Estado: </strong>
                          @if($league->abierta)
                            Abierta para inscripciones.
                          @else
                            @if($league->iniciada && !$league->finalizada)
                              Liga en curso
                            @else
                              @if(!$league->iniciada && !$league->finalizada)
                                Liga cerrada. Pendiente de comenzar
                              @else
                                @if($league->finalizada)
                                  Liga finalizada
                                @endif
                              @endif
                            @endif
                          @endif
                        </div>
                        <div class="form-group">
                          <span class="badge badge-dark">{{ ($league->liganovatos == 1) ? 'Liga de novatos' : 'Liga standard' }}</span>
                        </div>
                        <div class="form-group">
                          <strong>Máximo presupuesto equipos nuevos: </strong>{{ $league->maximopresupuesto }}
                        </div>
                        <div class="form-group">
                          <strong>Puntos asignados por victoria / empate / derrota: </strong>{{ $league->puntosvictoria.' / '.$league->puntosempate.' / '.$league->puntosderrota }}
                        </div>
                        <div class="float-right">
                          @if($league->abierta && !$inscrito)
                            <a href="{{ url('/ligas/aplicar/'.$league->id) }}"><button class="btn btn-primary">Apuntarse</button></a>
                          @endif
                          @if(Auth::user()->rol != 3 && !$league->iniciada && !$league->finalizada)
                            <a href="{{ url('/ligas/editar/'.$league->id) }}"><button class="btn btn-primary">Editar</button></a>
                            @if($league->abierta)
                              <a href="{{ url('/ligas/periodoInscripciones/cerrar/'.$league->id) }}"><button class="btn btn-primary">Cerrar</button></a>
                            @else
                              <a href="{{ url('/ligas/periodoInscripciones/abrir/'.$league->id) }}"><button class="btn btn-primary">Abrir</button></a>
                              <a href="{{ url('/ligas/iniciar/'.$league->id) }}"><button class="btn btn-primary">Iniciar</button></a>
                            @endif
                            @if($league->leagueTeams->count() == 0)
                              <a href="{{ url('/ligas/eliminar/'.$league->id) }}"><button class="btn btn-danger">Borrar</button></a>
                            @endif
                          @endif
                          @if (Auth::user()->rol != 3 && $league->iniciada && !$league->finalizada)
                            <a href="{{ url('/ligas/finalizar/'.$league->id) }}"><button class="btn btn-warning">Finalizar</button></a>
                          @endif
                        </div>
                      </div>
                      @if($league->abierta && $inscrito)
                        <!-- APPLICANTS -->
                        <div class="tab-pane fade" id="applicants" role="tabpanel" aria-labelledby="applicants-tab">
                          <table class="table table-bordered table-hover">
                            <thead class="thead-dark">
                              <tr>
                                <th scope="col">Equipo</th>
                                <th scope="col">Raza</th>
                                <th scope="col">Valoración</th>
                                <th scope="col">Entrenador</th>
                                <th scope="col"></th>
                              </tr>
                            </thead>
                            <tbody>
                              @foreach ($league->leagueTeams as $inscripcion)
                                <tr>
                                  <td scope="col"><a href="{{ url('/equipos/detalle/'.$inscripcion->team->id) }}">{{ $inscripcion->team->nombre }}</a></td>
                                  <td scope="col">{{ $inscripcion->team->race->nombre }}</td>
                                  <td scope="col">{{ $inscripcion->team->valoracion }}</td>
                                  <td scope="col">{{ $inscripcion->team->user->name }}</td>
                                  <td scope="col" class="text-nowrap">
                                    @if (Auth::user()->rol != 3 && !$inscripcion->aceptado)
                                      <a href="{{ url('/ligas/gestionarInscripcion/aceptar/'.$inscripcion->id) }}"><span class="fas fa-check-circle" title="Admitir"> </span></a>
                                      <a href="{{ url('/ligas/gestionarInscripcion/rechazar/'.$inscripcion->id) }}"><span class="fas fa-times-circle" title="Rechazar"> </span></a>
                                    @endif
                                    @if ($inscripcion->aceptado)
                                      <span class="fas fa-check-square" title="Equipo admitido a la liga"> </span>
                                    @endif
                                  </td>
                                </tr>
                              @endforeach
                            <tbody>
                          </table>
                        </div>
                      @endif
                      @if(!$league->abierta)
                        <!-- GROUPS -->
                        <div class="tab-pane fade" id="groups" role="tabpanel" aria-labelledby="groups-tab">
                          <div id="grupos">
                            @for($grupo = 1; $grupo <= $league->numgrupos; $grupo++)
                              <table class="table table-bordered table-hover">
                                <thead class="thead-dark">
                                  <tr>
                                    <th scope="col" colspan="5">GRUPO {{ $grupo }}</th>
                                  <tr>
                                    <th scope="col">Equipo</th>
                                    <th scope="col">Raza</th>
                                    <th scope="col">Valoración</th>
                                    <th scope="col">Entrenador</th>
                                    <th scope="col">Puntos</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  @foreach($league->leagueTeams->where("grupo", $grupo)->sortByDesc("puntos") as $leagueTeam)
                                    <tr>
                                      <td scope="col"><a href="{{ url('/equipos/detalle/'.$leagueTeam->team->id) }}">{{ $leagueTeam->team->nombre }}</a></td>
                                      <td scope="col">{{ $leagueTeam->team->race->nombre }}</td>
                                      <td scope="col">{{ $leagueTeam->team->valoracion }}</td>
                                      <td scope="col">{{ $leagueTeam->team->user->name }}</td>
                                      <td scope="col">{{ $leagueTeam->puntos }}</td>
                                    </tr>
                                  @endforeach
                                </tbody>
                              </table>
                              <br/>
                            @endfor
                          </div>
                          @if (Auth::user()->rol != 3 && !$league->abierta && !$league->finalizada)
                            <div>
                              <a href="{{ url('/ligas/asignarGrupos/'.$league->id) }}"><button class="btn btn-primary">Asignar Grupos</button></a>
                            </div>
                          @endif
                        </div>
                        <!--CALENDAR-->
                        <div class="tab-pane fade" id="calendar" role="tabpanel" aria-labelledby="calendar-tab">
                          <div id="jornadas">
                            @foreach($league->weeks as $jornada)
                              <div id="jornada_{{ $jornada->numJornada }}">
                                <div class="weekHeader col-md-12 float-left">
                                  JORNADA {{ $jornada->numjornada }} {{ $jornada->observaciones }}
                                  @if(!$league->abierta && !$league->finalizada && $jornada->doesntHave('matches.teams'))
                                    <span class="float-right">
                                      <a href="{{ url('/jornadas/editar/'.$jornada->id) }}" class="fas fa-edit" title="Editar jornada"></a>
                                      <a href="{{ url('/jornadas/eliminar/'.$jornada->id) }}" class="fas fa-trash-alt" title="Borrar jornada"></a>
                                    </span>
                                  @endif
                                </div>
                                @foreach($jornada->matches as $match)
                                  <div class="weekMatch col-md-12 float-left">
                                    <div class="weekTeams col-md-12 float-left">
                                      @if ($match->local->user->id == Auth::user()->id || $match->away->user->id == Auth::user()->id || Auth::user()->rol != 3)
                                        @if ($match->local->preparado && $match->away->preparado && $match->teams->count() == 0
                                          && $jornada->numjornada == $jornada->league->leagueTeams->where('equipo', $match->local->id)->first()->jornada
                                          && $jornada->numjornada == $jornada->league->leagueTeams->where('equipo', $match->away->id)->first()->jornada)
                                            <a href="{{ url('/actas/crear/'.$match->id) }}" class="float-right fas fa-football-ball" title="Iniciar acta"></a>
                                        @elseif($match->teams->count() == 2)
                                          <a href="{{ url('/actas/detalle/'.$match->id) }}" class="float-right fas fa-stream" title="Ver acta"></a>
                                        @endif
                                      @endif
                                      <a href="{{ url('/equipos/detalle/'.$league->teams->find($match->anfitrion)->id) }}">{{ $league->teams->find($match->anfitrion)->nombre }}</a>
                                      - VS -
                                      <a href="{{ url('/equipos/detalle/'.$league->teams->find($match->visitante)->id) }}">{{ $league->teams->find($match->visitante)->nombre }}</a>
                                    </div>
                                    @if(count($match->teams) == 0 || (!$match->teams[0]->actafinalizada && !$match->teams[1]->actafinalizada))
                                      <div class="resultadoPartido col-md-12 float-left">
                                        <td colspan="3">Pendiente de jugar</td>
                                      </div>
                                    @else
                                      <div class="resultadoPartido col-md-12 float-left">
                                        @if($match->teams[0]->actafinalizada)
                                          {{ $match->teams[0]->tdf }} - {{ $match->teams[0]->tdc }}
                                        @else
                                          {{ $match->teams[1]->tdc }} - {{ $match->teams[1]->tdf }}
                                        @endif
                                      </div>
                                      <div class="matchResult col-md-12 float-left">
                                        <a href="{{ url('/actas/detalle/'.$match->id) }}">Ver acta</a>
                                      </div>
                                    @endif
                                  </div>
                                @endforeach
                              </div>
                            @endforeach
                          </div>
                          @if (Auth::user()->rol != 3 && !$league->abierta && !$league->finalizada)
                            <div>
                              <a href="{{ url('/jornadas/crear/'.$league->id) }}"><button class="btn btn-primary">Añadir jornada</button></a>
                            </div>
                          @endif
                        </div>
                        <!-- STANDINGS -->
                        <div class="tab-pane fade" id="standings" role="tabpanel" aria-labelledby="standings-tab">
                          <table class="table table-bordered table-hover">
                            <thead class="thead-dark">
                              <tr>
                                <th scope="col" colspan="5">CLASIFICACION GENERAL</th>
                              <tr>
                                <th scope="col">Equipo</th>
                                <th scope="col">Raza</th>
                                <th scope="col">Valoración</th>
                                <th scope="col">Entrenador</th>
                                <th scope="col">Puntos</th>
                              </tr>
                            </thead>
                            <tbody>
                              @foreach($league->leagueTeams->sortByDesc("puntos") as $leagueTeam)
                                <tr>
                                  <td scope="col"><a href="{{ url('/equipos/detalle/'.$leagueTeam->team->id) }}">{{ $leagueTeam->team->nombre }}</a></td>
                                  <td scope="col">{{ $leagueTeam->team->race->nombre }}</td>
                                  <td scope="col">{{ $leagueTeam->team->valoracion }}</td>
                                  <td scope="col">{{ $leagueTeam->team->user->name }}</td>
                                  <td scope="col">{{ $leagueTeam->puntos }}</td>
                                </tr>
                              @endforeach
                            </tbody>
                          </table>
                        </div>
                        <!-- STATISTICS -->
                        <div class="tab-pane fade" id="statistics" role="tabpanel" aria-labelledly="standings-tab">
                          <table class="table table-bordered table-hover">
                            <thead class="thead-dark">
                              <tr>
                                <th scope="col">Equipos más anotadores</th>
                                <th scope="col">TD a favor</th>
                              </tr>
                            </thead>
                            <tbody>
                              @foreach($stats['scorers'] as $scorer)
                                <tr>
                                  <td scope="col">
                                    {{ $scorer->nombre }}
                                  </td>
                                  <td scope="col">
                                    {{ $scorer->td }}
                                  </td>
                                </tr>
                              @endforeach
                            </tbody>
                          </table>
                          <table class="table table-bordered table-hover">
                            <thead class="thead-dark">
                              <tr>
                                <th scope="col">Equipos más defensivos</th>
                                <th scope="col">TD en contra</th>
                              </tr>
                            </thead>
                            <tbody>
                              @foreach($stats['defenders'] as $defender)
                                <tr>
                                  <td scope="col">
                                    {{ $defender->nombre }}
                                  </td>
                                  <td scope="col">
                                    {{ $defender->td }}
                                  </td>
                                </tr>
                              @endforeach
                            </tbody>
                          </table>
                          <table class="table table-bordered table-hover">
                            <thead class="thead-dark">
                              <tr>
                                <th scope="col">Equipos más violentos</th>
                                <th scope="col">Muertos</th>
                                <th scope="col">Heridos</th>
                              </tr>
                            </thead>
                            <tbody>
                              @foreach($stats['injurers'] as $injurer)
                                <tr>
                                  <td>
                                    {{ $injurer->nombre }}
                                  </td>
                                  <td>
                                    {{ $injurer->muertos }}
                                  </td>
                                  <td>
                                    {{ $injurer->heridos }}
                                  </td>
                                </tr>
                              @endforeach
                            </tbody>
                          </table>
                          <table class="table table-bordered table-hover">
                            <thead class="thead-dark">
                              <tr>
                                <th scope="col">Equipos más pupas</th>
                                <th scope="col">Fallecidos</th>
                                <th scope="col">Lesionados</th>
                              </tr>
                            </thead>
                            <tbody>
                              @foreach($stats['stitchys'] as $stitchy)
                                <tr>
                                  <td>
                                    {{ $stitchy->nombre }}
                                  </td>
                                  <td>
                                    {{ $stitchy->muertos }}
                                  </td>
                                  <td>
                                    {{ $stitchy->heridos }}
                                  </td>
                                </tr>
                              @endforeach
                            </tbody>
                          </table>
                          <table class="table table-bordered table-hover">
                            <thead class="thead-dark">
                              <tr>
                                <th scope="col">Equipos con mejor juego de pase</th>
                                <th scope="col">Pases</th>
                                <th scope="col">Yardas de pase</th>
                              </tr>
                            </thead>
                            <tbody>
                              @foreach($stats['throwers'] as $thrower)
                                <tr>
                                  <td>
                                    {{ $thrower->nombre }}
                                  </td>
                                  <td>
                                    {{ $thrower->pases }}
                                  </td>
                                  <td>
                                    {{ $thrower->yardas }}
                                  </td>
                                </tr>
                              @endforeach
                            </tbody>
                          </table>
                          <table class="table table-bordered table-hover">
                            <thead class="thead-dark">
                              <tr>
                                <th scope="col">Equipos con mejor defensa contra el pase</th>
                                <th scope="col">intercepciones</th>
                              </tr>
                            </thead>
                            <tbody>
                              @foreach($stats['interceptors'] as $interceptor)
                                <tr>
                                  <td>
                                    {{ $interceptor->nombre }}
                                  </td>
                                  <td>
                                    {{ $interceptor->intercepciones }}
                                  </td>
                                </tr>
                              @endforeach
                            </tbody>
                          </table>
                          <table class="table table-bordered table-hover">
                            <thead class="thead-dark">
                              <tr>
                                <th scope="col">Jugadores más anotadores</th>
                                <th scope="col">Equipo</td>
                                <th scope="col">TD</th>
                              </tr>
                            </thead>
                            <tbody>
                              @foreach($stats['playerScorers'] as $playerScorer)
                                <tr>
                                  <td>
                                    {{ $playerScorer->nombre }}
                                  </td>
                                  <td>
                                    {{ $playerScorer->equipo }}
                                  </td>
                                  <td>
                                    {{ $playerScorer->td }}
                                  </td>
                                </tr>
                              @endforeach
                            </tbody>
                          </table>
                          <table class="table table-bordered table-hover">
                            <thead class="thead-dark">
                              <tr>
                                <th scope="col">Jugadores más violentos</th>
                                <th scope="col">Equipo</td>
                                <th scope="col">Muertos</th>
                                <th scope="col">Heridos graves</th>
                                <th scope="col">Heridos</th>
                              </tr>
                            </thead>
                            <tbody>
                              @foreach($stats['playerInjurers'] as $playerInjurer)
                                <tr>
                                  <td>
                                    {{ $playerInjurer->nombre }}
                                  </td>
                                  <td>
                                    {{ $playerInjurer->equipo }}
                                  </td>
                                  <td>
                                    {{ $playerInjurer->muertos }}
                                  </td>
                                  <td>
                                    {{ $playerInjurer->heridos_graves }}
                                  </td>
                                  <td>
                                    {{ $playerInjurer->heridos_leves }}
                                  </td>
                                </tr>
                              @endforeach
                            </tbody>
                          </table>
                          <table class="table table-bordered table-hover">
                            <thead class="thead-dark">
                              <tr>
                                <th scope="col">Jugadores más pupas</th>
                                <th scope="col">Equipo</td>
                                <th scope="col">Lesiones</th>
                              </tr>
                            </thead>
                            <tbody>
                              @foreach($stats['playerStitchys'] as $playerStitchy)
                                <tr>
                                  <td>
                                    {{ $playerStitchy->nombre }}
                                  </td>
                                  <td>
                                    {{ $playerStitchy->equipo }}
                                  </td>
                                  <td>
                                    {{ $playerStitchy->heridas }}
                                  </td>
                                </tr>
                              @endforeach
                            </tbody>
                          </table>
                          <table class="table table-bordered table-hover">
                            <thead class="thead-dark">
                              <tr>
                                <th scope="col">Mejores pasadores</th>
                                <th scope="col">Equipo</td>
                                <th scope="col">Pases</th>
                                <th scope="col">Yardas de pase</th>
                              </tr>
                            </thead>
                            <tbody>
                              @foreach($stats['playerThrowers'] as $playerThrower)
                                <tr>
                                  <td>
                                    {{ $playerThrower->nombre }}
                                  </td>
                                  <td>
                                    {{ $playerThrower->equipo }}
                                  </td>
                                  <td>
                                    {{ $playerThrower->pases }}
                                  </td>
                                  <td>
                                    {{ $playerThrower->yardasPase }}
                                  </td>
                                </tr>
                              @endforeach
                            </tbody>
                          </table>
                          <table class="table table-bordered table-hover">
                            <thead class="thead-dark">
                              <tr>
                                <th scope="col">Público más agresivo</th>
                                <th scope="col">Bajas</th>
                              </tr>
                            </thead>
                            <tbody>
                              @foreach($stats['bloodyFans'] as $bloodyFan)
                                <tr>
                                  <td>
                                    {{ $bloodyFan->nombre }}
                                  </td>
                                  <td>
                                    {{ $bloodyFan->bajas }}
                                  </td>
                                </tr>
                              @endforeach
                            </tbody>
                          </table>
                          <table class="table table-bordered table-hover">
                            <thead class="thead-dark">
                              <tr>
                                <th scope="col">Equipo más sucio</th>
                                <th scope="col">Bajas mediante faltas</th>
                              </tr>
                            </thead>
                            <tbody>
                              @foreach($stats['dirtyPlayers'] as $dirtyPlayer)
                                <tr>
                                  <td>
                                    {{ $dirtyPlayer->nombre }}
                                  </td>
                                  <td>
                                    {{ $dirtyPlayer->bajas }}
                                  </td>
                                </tr>
                              @endforeach
                            </tbody>
                          </table>
                        </div>
                      @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
