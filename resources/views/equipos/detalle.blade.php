@extends('layouts.app')

@push('scripts')
  <script src="{{ asset('js/plugins/validatorPlugin.js') }}" type="text/javascript" defer></script>
  <script src="{{ asset('js/validators/equipos.js') }}" type="text/javascript" defer></script>
  <script src="{{ asset('js/subirJugadores.js') }}" type="text/javascript" defer></script>
@endpush

@section('content')
<!--Hay que determinar si el equipo tiene fondos para comprar jugadores-->
@php($canHirePlayers = ($team->race->positionals->min('precio') <= ($team->tesoreria + $team->banco)) && ($team->players->count() < 16))
@php($permisoEdicion = (Auth::user()->rol != 3 || (Auth::user()->id == $team->user->id) && $team->preparado == 0))
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Detalle del equipo</div>
                <div class="card-body">
                    @if (session("warning") != "")
                      <div class="alert alert-danger" role="alert">
                        {{ session("warning") }}
                        @php(session(["warning" => ""]))
                      </div>
                    @endif
                    <h3>{{ $team->nombre }}</h3>
                    <div style="text-align:right">
                      @if ($permisoEdicion)
                        <a href="{{ url('/equipos/editar/'.$team->id) }}"><button class="btn btn-primary">Editar</button></a>
                        @if ($canHirePlayers)
                          <button id="showPlayerModal" class="btn btn-primary" data-toggle="modal" data-target="#addPlayer">Comprar jugador</button>
                        @endif
                        @if ($team->activo == 0 && $team->players->count() >= 11)
                          <a href="{{ url('/equipos/activar/'.$team->id) }}"><button class="btn btn-warning">Activar</button></a>
                        @elseif ($team->activo == 1 && $team->preparado == 0)
                          <button id="showPrepareModal" class="btn btn-success" data-toggle="modal" data-target="#warnPreparation">Preparado para jugar</button></a>
                        @endif
                      @endif
                    </div>
                    <div class="tab-content" id="infoEquipo">
                      <ul class="nav nav-tabs" id="team-tab" role="tablist">
                        <li class="nav-item">
                          <a class="nav-link active" id="roster-tab" data-toggle="tab" href="#roster" aria-controls="roster" aria-selected="true">roster</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="stats-tab" data-toggle="tab" href="#stats" aria-controls="stats" aria-selected="false">Estadísticas</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="hallofame-tab" data-toggle="tab" href="#hallofame" aria-control="hallofame" aria-selected="false">Salón de la fama</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="nextToLevel-tab" data-toggle="tab" href="#nextToLevel" aria-control="nextToLevel" aria-selected="false">Aspirantes</a>
                        </li>
                      </ul>
                      <!--ROSTER-->
                      <div class="tab-pane fade show active" id="roster" role="tabpanel" aria-labelledby="roster-tab">
                        <table id="roster-table" class="table table-bordered table-hover table-sm table-responsive">
                          <thead class="thead-dark">
                            <tr>
                              <th scope="col">Nº</th>
                              <th scope="col">Nombre</th>
                              <th scope="col">Posición</th>
                              <th scope="col">MO</th>
                              <th scope="col">FU</th>
                              <th scope="col">AG</th>
                              <th scope="col">AR</th>
                              <th scope="col">Habilidades</th>
                              <th scope="col">LP</th>
                              <th scope="col">PE</th>
                              <th scope="col">Valor</th>
                              <th scope="col"></th>
                            </tr>
                          </thead>
                          <tbody class="thead-dark">
                            @php($accumulatedValue = 0)
                            @for ($playerSlot = 1; $playerSlot <= 16; $playerSlot++)
                              @php($jugador = $team->players->where('numero', $playerSlot)->where('activo', 1)->first())
                              <tr id="_playerSlot_{{ $playerSlot }}" @if($jugador != null && $jugador->lesionado) class="lesionado" @endif>
                                @if ($jugador != null)
                                  <td scope="col">{{ $jugador->numero }}</td>
                                  <td scope="col">{{ $jugador->nombre }}</td>
                                  <td scope="col">{{ $jugador->position->nombre }}</td>
                                  <td scope="col">{{ $jugador->ma }}</td>
                                  <td scope="col">{{ $jugador->fue }}</td>
                                  <td scope="col">{{ $jugador->agl }}</td>
                                  <td scope="col">{{ $jugador->av }}</td>
                                  <td scope="col">
                                    @foreach ($jugador->skills as $habilidad)
                                      <span class="badge badge-primary" title="{{ $habilidad->nombre }}">{{ $habilidad->nombre_corto }}</span>
                                    @endforeach
                                  </td>
                                  <td scope="col">{{ $jugador->niggling }}</td>
                                  <td scope="col">{{ $jugador->px }}</td>
                                  <td scope="col">{{ $jugador->precio }}</td>
                                  @php($accumulatedValue += $jugador->precio)
                                  <td scope="col" class="text-nowrap">
                                    @if ($permisoEdicion)
                                      <a href="{{ url('/equipos/despedir/'.$jugador->id) }}" title="despedir"><span class="fas fa-thumbs-down"></span></a>
                                      @if ($jugador->px >= $jugador->getPxToNextLevel())
                                        <a id="playerId_{{ $jugador->id }}" title="subir de nivel" class="levelup activo"><span class="fas fa-arrow-alt-circle-up"></span></a>
                                      @endif
                                    @endif
                                    @if (Auth::user()->rol != 3)
                                      <a href="{{ url('/jugadores/editar/'.$jugador->id) }}" title="editar"><span class="fas fa-tools"></span></a>
                                    @endif
                                  </td>
                                @else
                                  <td scope="col">{{ $playerSlot }}</td>
                                  <td scope="col"></td>
                                  <td scope="col"></td>
                                  <td scope="col"></td>
                                  <td scope="col"></td>
                                  <td scope="col"></td>
                                  <td scope="col"></td>
                                  <td scope="col"></td>
                                  <td scope="col"></td>
                                  <td scope="col"></td>
                                  <td scope="col"></td>
                                  <td scope="col"></td>
                                @endif
                              </tr>
                            @endfor
                            <tr>
                              <th scope="col" colspan="12"></th>
                            </tr>
                            <tr>
                              <th scope="col" colspan="2">EQUIPO</th>
                              <td scope="col" colspan="4">{{ $team->nombre }}</td>
                              <th scope="col" colspan="2">Segundas oportunidades</th>
                              <td scope="col">{{ $team->rerolls }}</td>
                              <th scope="col">x {{ $team->race->costerr }}</td>
                              <td scope="col">{{ ($team->rerolls * $team->race->costerr) }}</td>
                              <td scope="col">
                                @if ($permisoEdicion)
                                  @if ($team->rerolls < 8 && (($team->activo == 0)
                                    ? ($team->tesoreria + $team->banco >= $team->race->costerr)
                                    : ($team->tesoreria + $team->banco >= 2 * $team->race->costerr)))
                                    <a href="{{ url('/equipos/gestionaElementos/'.$team->id.'/reroll/1') }}"><span class="fas fa-plus-square"></span>
                                  @endif
                                  @if ($team->rerolls > 0)
                                    <a href="{{ url('/equipos/gestionaElementos/'.$team->id.'/reroll/0') }}"><span class="fas fa-minus-square"></span>
                                  @endif
                                @endif
                              </td>
                              @php($accumulatedValue += ($team->rerolls * $team->race->costerr))
                            </tr>
                            <tr>
                              <th scope="col" colspan="2">RAZA</th>
                              <td scope="col" colspan="4">{{ $team->race->nombre }}</td>
                              <th scope="col" colspan="2">Factor de hinchas</th>
                              <td scope="col">{{ $team->ff }}</td>
                              <th scope="col">x 10000</td>
                              <td scope="col">{{ ($team->ff * 10000) }}</td>
                              <td scope="col"></td>
                              @php($accumulatedValue += ($team->ff * 10000))
                            </tr>
                            <tr>
                              <th scope="col" colspan="2">VALORACION</th>
                              <td scope="col" colspan="4">{{ $team->valoracion }}</td>
                              <th scope="col" colspan="2">Ayudantes de entrenador</th>
                              <td scope="col">{{ $team->ayudantes }}</td>
                              <th scope="col">x 10000</td>
                              <td scope="col">{{ ($team->ayudantes * 10000) }}</td>
                              <td scope="col">
                                @if ($permisoEdicion)
                                  @if ($team->tesoreria + $team->banco >= 10000)
                                    <a href="{{ url('/equipos/gestionaElementos/'.$team->id.'/ayudante/1') }}"><span class="fas fa-plus-square"></span>
                                  @endif
                                  @if ($team->ayudantes > 0)
                                    <a href="{{ url('/equipos/gestionaElementos/'.$team->id.'/ayudante/0') }}"><span class="fas fa-minus-square"></span>
                                  @endif
                                @endif
                              </td>
                              @php($accumulatedValue += ($team->ayudantes * 10000))
                            </tr>
                            <tr>
                              <tr>
                                <th scope="col" colspan="2">TESORERIA / BANCO</th>
                                <td scope="col" colspan="4">{{ $team->tesoreria }} / {{ $team->banco }}</td>
                                <th scope="col" colspan="2">Animadoras</th>
                                <td scope="col">{{ $team->animadoras }}</td>
                                <th scope="col">x 10000</td>
                                <td scope="col">{{ ($team->animadoras * 10000) }}</td>
                                <td scope="col">
                                  @if ($permisoEdicion)
                                    @if ($team->tesoreria + $team->banco >= 10000)
                                      <a href="{{ url('/equipos/gestionaElementos/'.$team->id.'/animadora/1') }}"><span class="fas fa-plus-square"></span>
                                    @endif
                                    @if ($team->animadoras > 0)
                                      <a href="{{ url('/equipos/gestionaElementos/'.$team->id.'/animadora/0') }}"><span class="fas fa-minus-square"></span>
                                    @endif
                                  @endif
                                </td>
                                @php($accumulatedValue += ($team->animadoras * 10000))
                              </tr>
                            </tr>
                            <tr>
                              <tr>
                                <th scope="col" colspan="2">ENTRENADOR</th>
                                <td scope="col" colspan="4">{{ $team->user->name }}</td>
                                <th scope="col" colspan="2">Médico</th>
                                <td scope="col">{{ $team->apotecario }}</td>
                                <th scope="col">x 50000</td>
                                <td scope="col">{{ ($team->apotecario * 50000) }}</td>
                                <td scope="col">
                                  @if ($permisoEdicion && $team->race->apotecario)
                                    @if ($team->apotecario == 0 && $team->tesoreria + $team->banco >= 50000)
                                      <a href="{{ url('/equipos/gestionaElementos/'.$team->id.'/apotecario/1') }}"><span class="fas fa-plus-square"></span>
                                    @endif
                                    @if ($team->apotecario == 1)
                                      <a href="{{ url('/equipos/gestionaElementos/'.$team->id.'/apotecario/0') }}"><span class="fas fa-minus-square"></span>
                                    @endif
                                  @endif
                                </td>
                                @php($accumulatedValue += ($team->apotecario * 50000))
                              </tr>
                              <tr>
                                <th scope="col" colspan="10">Valor total del equipo</th>
                                <td scope="col">{{ $accumulatedValue }}</td>
                              </tr>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                      <!--ESTADISTICAS-->
                      <div class="tab-pane fade" id="stats" role="tabpanel" aria-labelledby="stats-tab">
                        <table id="stats-table" class="table table-bordered table-hover table-sm table-responsive">
                          <thead class="thead-dark">
                            <tr>
                              <th scope="col">Nº</th>
                              <th scope="col">Nombre</th>
                              <th scope="col">Posición</th>
                              <th scope="col" title="Heridos provocados">H</th>
                              <th scope="col" title="Muertos provocados">M</th>
                              <th scope="col" title="Veces que ha sido herido">VH</th>
                              <th scope="col" title="Veces que ha sido curado por el apotecario o ha regenerado"><span class="fa fa-plus-circle"></span></th>
                              <th scope="col" title="Pases realizados">P</th>
                              <th scope="col" title="Yardas de pase">Y</th>
                              <th scope="col" title="Intercepciones realizadas">I</th>
                              <th scope="col" title="Touchdowns anotados">TD</th>
                              <th scope="col" title="Partidos jugados">P</th>
                              <th scope="col" title="Veces galardonado con el MVP">MVP</th>
                            </tr>
                          </thead>
                          <tbody class="thead-dark">
                            @for ($playerSlot = 1; $playerSlot <= 16; $playerSlot++)
                              @php($jugador = $team->players->where('numero', $playerSlot)->where('activo', 1)->first())
                              <tr id="stats_playerSlot_{{ $playerSlot }}">
                                @if ($jugador != null)
                                  <td scope="col">{{ $jugador->numero }}</td>
                                  <td scope="col">{{ $jugador->nombre }}</td>
                                  <td scope="col">{{ $jugador->position->nombre }}</td>
                                  <td scope="col">{{ $jugador->hf }}</td>
                                  <td scope="col">{{ $jugador->mf }}</td>
                                  <td scope="col">{{ $jugador->hc }}</td>
                                  <td scope="col">{{ $jugador->curado }}</td>
                                  <td scope="col">{{ $jugador->pases }}</td>
                                  <td scope="col">{{ $jugador->yardaspase }}</td>
                                  <td scope="col">{{ $jugador->intercepciones }}</td>
                                  <td scope="col">{{ $jugador->td }}</td>
                                  <td scope="col">{{ $jugador->jugados }}</td>
                                  <td scope="col">{{ $jugador->mvp }}</td>
                                @else
                                  <td scope="col">{{ $playerSlot }}</td>
                                  <td scope="col"></td>
                                  <td scope="col"></td>
                                  <td scope="col"></td>
                                  <td scope="col"></td>
                                  <td scope="col"></td>
                                  <td scope="col"></td>
                                  <td scope="col"></td>
                                  <td scope="col"></td>
                                  <td scope="col"></td>
                                  <td scope="col"></td>
                                  <td scope="col"></td>
                                  <td scope="col"></td>
                                @endif
                              </tr>
                            @endfor
                            <tr>
                              <th scope="col" colspan="13">Estadísticas generales del equipo</th>
                            </tr>
                            <tr>
                              <th scope="col" colspan="2">Partidos jugados</th>
                              <td scope="col">{{ $team->jugados }}</td>
                              <th scope="col" colspan="5">Ganados</th>
                              <td scope="col" colspan="5">{{ $team->ganados }}</td>
                            </tr>
                            <tr>
                              <th scope="col" colspan="2">Perdidos</th>
                              <td scope="col">{{ $team->perdidos }}</td>
                              <th scope="col" colspan="5">Empatados</th>
                              <td scope="col" colspan="5">{{ $team->empatados }}</td>
                            </tr>
                            <tr>
                              <th scope="col" colspan="2">TD a favor</th>
                              <td scope="col">{{ $team->tdf }}</td>
                              <th scope="col" colspan="5">TD en contra</th>
                              <td scope="col" colspan="5">{{ $team->tdc }}</td>
                            </tr>
                            <tr>
                              <th scope="col" colspan="2">Heridos a favor</th>
                              <td scope="col">{{ $team->hf }}</td>
                              <th scope="col" colspan="5">Heridos en contra</th>
                              <td scope="col" colspan="5">{{ $team->hc }}</td>
                            </tr>
                            <tr>
                              <th scope="col" colspan="2">Muertos a favor</th>
                              <td scope="col">{{ $team->mf }}</td>
                              <th scope="col" colspan="5">Muertos en contra</th>
                              <td scope="col" colspan="5">{{ $team->mc }}</td>
                            </tr>
                            <tr>
                              <th scope="col" colspan="2">Pases</th>
                              <td scope="col">{{ $team->pases }}</td>
                              <th scope="col" colspan="5">Yardas de pase</th>
                              <td scope="col" colspan="5">{{ $team->yardaspase }}</td>
                            </tr>
                            <tr>
                              <th scope="col" colspan="2">Intercepciones a favor</th>
                              <td scope="col">{{ $team->intercepciones }}</td>
                              <th scope="col" colspan="5">Intercepciones en contra</th>
                              <td scope="col" colspan="5">{{ $team->intercepcionesc }}</td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                      <!-- SALON DE LA FAMA-->
                      <div class="tab-pane fade" id="hallofame" role="tabpanel" aria-labelledby="hallofame-tab">
                        <table id="hallofame-table" class="table table-bordered table-hover table-sm table-responsive">
                          <thead class="thead-dark">
                            <tr>
                              <th scope="col">Nombre</th>
                              <th scope="col">Posición</th>
                              <th scope="col" title="Heridos provocados">H</th>
                              <th scope="col" title="Muertos provocados">M</th>
                              <th scope="col" title="Veces que ha sido herido">VH</th>
                              <th scope="col" title="Veces que ha sido curado por el apotecario o ha regenerado"><span class="fa fa-plus-circle"></span></th>
                              <th scope="col" title="Pases realizados">P</th>
                              <th scope="col" title="Yardas de pase">Y</th>
                              <th scope="col" title="Intercepciones realizadas">I</th>
                              <th scope="col" title="Touchdowns anotados">TD</th>
                              <th scope="col" title="Partidos jugados">P</th>
                              <th scope="col" title="Veces galardonado con el MVP">MVP</th>
                            </tr>
                          </thead>
                          <tbody class="thead-dark">
                            <tr>
                              <th colspan="12">Jugadores fallecidos</th>
                            </tr>
                            @foreach ($team->players->where('muerto', 1) as $jugador)
                              <tr>
                                <td scope="col">{{ $jugador->nombre }}</td>
                                <td scope="col">{{ $jugador->position->nombre }}</td>
                                <td scope="col">{{ $jugador->hf }}</td>
                                <td scope="col">{{ $jugador->mf }}</td>
                                <td scope="col">{{ $jugador->hc }}</td>
                                <td scope="col">{{ $jugador->curado }}</td>
                                <td scope="col">{{ $jugador->pases }}</td>
                                <td scope="col">{{ $jugador->yardaspase }}</td>
                                <td scope="col">{{ $jugador->intercepciones }}</td>
                                <td scope="col">{{ $jugador->td }}</td>
                                <td scope="col">{{ $jugador->jugados }}</td>
                                <td scope="col">{{ $jugador->mvp }}</td>
                              </tr>
                            @endforeach
                            <tr>
                              <th colspan="12">Jugadores expulsados del equipo</th>
                            </tr>
                            @foreach ($team->players->where('muerto', 0)->where('activo', 0) as $jugador)
                              <tr>
                                <td scope="col">{{ $jugador->nombre }}</td>
                                <td scope="col">{{ $jugador->position->nombre }}</td>
                                <td scope="col">{{ $jugador->hf }}</td>
                                <td scope="col">{{ $jugador->mf }}</td>
                                <td scope="col">{{ $jugador->hc }}</td>
                                <td scope="col">{{ $jugador->curado }}</td>
                                <td scope="col">{{ $jugador->pases }}</td>
                                <td scope="col">{{ $jugador->yardaspase }}</td>
                                <td scope="col">{{ $jugador->intercepciones }}</td>
                                <td scope="col">{{ $jugador->td }}</td>
                                <td scope="col">{{ $jugador->jugados }}</td>
                                <td scope="col">{{ $jugador->mvp }}</td>
                              </tr>
                            @endforeach
                          </tbody>
                        </table>
                      </div>
                      <!--ASPIRANTES-->
                      <div class="tab-pane fade" id="nextToLevel" role="tabpanel" aria-labelledby="nextToLevel-tab">
                        <table id="aspirantes-table" class="table table-bordered table-hover table-sm table-responsive">
                          <thead class="thead-dark">
                            <tr>
                              <th scope="col">Nº</th>
                              <th scope="col">Nombre</th>
                              <th scope="col">Posición</th>
                              <th scope="col">MA</th>
                              <th scope="col">FUE</th>
                              <th scope="col">AGL</th>
                              <th scope="col">AV</th>
                              <th scope="col">Habilidades</th>
                              <th scope="col">PX</th>
                              <th scope="col">Sube a</th>
                            </tr>
                          </thead>
                          <tbody class="thead-dark">
                            @foreach ($aspirantes as $jugador)
                              <tr id="aspirantes_playerSlot_{{ $playerSlot }}">
                                @if ($jugador != null)
                                  <td scope="col">{{ $jugador->numero }}</td>
                                  <td scope="col">{{ $jugador->nombre }}</td>
                                  <td scope="col">{{ $jugador->position->nombre }}</td>
                                  <td scope="col">{{ $jugador->ma }}</td>
                                  <td scope="col">{{ $jugador->fue }}</td>
                                  <td scope="col">{{ $jugador->agl }}</td>
                                  <td scope="col">{{ $jugador->av }}</td>
                                  <td scope="col">
                                    @foreach ($jugador->skills as $habilidad)
                                      <span class="badge badge-primary" title="{{ $habilidad->nombre }}">{{ $habilidad->nombre_corto }}</span>
                                    @endforeach
                                  </td>
                                  <td scope="col">{{ $jugador->px }}</td>
                                  <td scope="col">{{ $jugador->getPxToNextLevel() }}</td>
                                @endif
                            @endforeach
                          </tbody>
                        </table>
                      </div>
                    </div>
                    <div class="float-right">
                      @if ($permisoEdicion)
                        <a href="{{ url('/equipos/editar/'.$team->id) }}"><button class="btn btn-primary">Editar</button></a>
                        @if ($canHirePlayers)
                          <button id="showPlayerModal" class="btn btn-primary" data-toggle="modal" data-target="#addPlayer">Comprar jugador</button>
                        @endif
                        @if ($team->activo == 0 && $team->players->count() >= 11)
                          <a href="{{ url('/equipos/activar/'.$team->id) }}"><button class="btn btn-warning">Activar</button></a>
                        @elseif ($team->activo == 1 && $team->preparado == 0)
                          <button id="showPrepareModal" class="btn btn-success" data-toggle="modal" data-target="#warnPreparation">Preparado para jugar</button></a>
                        @endif
                      @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ventana modal para añadir jugadores comprados-->
@if ($canHirePlayers)
  <div id="addPlayer" class="modal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Comprar jugador</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="addPlayerForm" method="post" action="{{ url('/equipos/comprarJugador/'.$team->id) }}">
          @csrf
          <input type="hidden" id="idEquipo" name="idEquipo" value="{{ $team->id }}" />
          <div class="modal-body">
            <div class="row">
              <span class="col-sm-2">
                <label for="numero">Nº</label>
              </span>
              <span class="col-sm-6">
                <label for="nombre">Nombre</label>
              </span>
              <span class="col-sm-4">
                <label class="form-group">Posición</label>
              </span>
            </div>
            <div class="row">
              <span class="col-sm-2">
                <select id="numero" name="numero" class="form-control">
                  @for ($numero = 1;$numero <= 16; $numero++)
                    @if ($team->players->where('numero', $numero)->count() == 0)
                      <option value="{{ $numero }}">{{ $numero }}</option>
                    @endif
                  @endfor
                </select>
              </span>
              <span class="col-sm-6">
                <input type="text" id="nombre" name="nombre" class="form-control nombreJugador" value="" />
              </span>
              <span class="col-sm-4">
                <select id="posicion" name="posicion" class="form-control">
                  @foreach ($team->race->positionals as $positional)
                    @if (($positional->precio <= $team->banco + $team->tesoreria)
                      && $team->players->where('activo', 1)->where('posicion', $positional->id)->count() < $positional->maximo)
                      <option value="{{ $positional->id }}">{{ $positional->nombre }}</option>
                    @endif
                  @endforeach
                </select>
              </span>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-primary">Enviar</button>
            <span class="btn btn-danger" data-dismiss="modal">Cancelar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endif

@if ($team->activo == 1 && $team->preparado == 0)
  <div id="warnPreparation" class="modal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Pasar a preparado</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          Al pasar a preparado se añadirán levas al equipo hasta completar 11 jugadores.
          Además, no podrán realizarse más operaciones sobre el equipo hasta que se juegue el siguiente partido.
        </div>
        <div class="modal-footer">
          <a href="{{ url('/equipos/preparar/'.$team->id) }}"><button class="btn btn-success">Confirmar</button></a>
          <span class="btn btn-danger" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </div>
  </div>
@endif

<div id="levelupPlayer" class="modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Indica la subida</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        ¿Cuál es la subida?
        <select id="tipoSubida">
          <option value=""></option>
          <option value="normal">Habilidad normal</option>
          <option value="doble">Habilidad doble</option>
          <option value="ma">+1 Movimiento</option>
          <option value="fue">+1 Fuerza</option>
          <option value="agl">+1 Agilidad</option>
          <option value="av">+1 Armadura</option>
        </select>
        <div id="habilidadSubida" class="hidden">
          ¿Qué habilidad vas a escoger?
          <select id="skillSet">
          </select>
        </div>
      </div>
    </div>
  </div>
</div>
<!------------------------------------------->
@endsection
