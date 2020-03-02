@extends('layouts.app')

@push('scripts')
  <script src="{{ asset('js/editarActa.js') }}" type="text/javascript" defer></script>
  <script src="{{ asset('js/plugins/validatorPlugin.js') }}" type="text/javascript" defer></script>
  <script src="{{ asset('js/validators/actas.js') }}" type="text/javascript" defer></script>
@endpush

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Editar acta</div>

                <div class="card-body">
                  <form id="actaForm" method="post" action="{{ url('/actas/editar/'.$sheet->id) }}">
                    @csrf
                    <input type="hidden" id="actafinalizada" name="actafinalizada" value="{{ $sheet->actafinalizada }}" />
                    <input type="hidden" id="tdcontra" name="tdcontra" />
                    <input type="hidden" id="annotations" name="annotations" value="" />
                    <div class="acta">
                      <div class="actaHeader">
                        <strong>{{ $sheet->team->nombre }}</strong>
                      </div>
                      <table class="table table-bordered table-hover table-sm table-responsive">
                        <thead class="thead-dark">
                          <tr>
                            <th scope="col" class="col-md-3">
                              TESO INI
                            </th>
                            <th scope="col" class="col-md-3">
                              RECAUDACION
                            </th>
                            <th scope="col" class="col-md-3">
                              TESO FINAL
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td scope="col">
                              {{ $sheet->tesoinicial }}
                            </td>
                            <td scope="col">
                              <input type="number" id="recaudacion" name="recaudacion" value="{{ $sheet->recaudacion }}" />
                            </td>
                            <td scope="col">
                              <input type="number" id="tesofinal" name="tesofinal" value="{{ $sheet->tesofinal }}" />
                            </td>
                          </tr>
                        </tbody>
                      </table>
                      <table class="table table-bordered table-hover table-sm table-responsive">
                        <thead class="thead-dark">
                          <tr>
                            <th scope="col" class="col-md-3">
                              FF INI
                            </th>
                            <th scope="col" class="col-md-3">
                              ESPECTADORES
                            </th>
                            <th scope="col" class="col-md-3">
                              FF FINAL
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td scope="col">
                              {{ $sheet->ffinicial }}
                            </td>
                            <td scope="col">
                              <input type="number" id="espectadores" name="espectadores" value="{{ $sheet->espectadores }}" />
                            </td>
                            <td scope="col">
                              <input type="number" id="fffinal" name="fffinal" value="{{ $sheet->fffinal }}" />
                            </td>
                          </tr>
                        </tbody>
                      </table>
                      <table class="annotationsTable table table-bordered table-hover table-sm table-responsive">
                        <thead class="thead-dark">
                          <tr>
                            <th scope="col" class="col-md-3">
                              HIGHLIGHTS DEL PARTIDO
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($sheet->annotations->sortBy("active.numero") as $annotation)
                            @switch($annotation->type->tipo)

                              @case("PASE")
                                <tr>
                                  <td scope="col">
                                    <span id="{{ $annotation->id }}" class="annotation">
                                      El nº {{ $annotation->active->numero }} ha realizado un pase de {{ $annotation->efecto }} yardas.
                                    </span>
                                    <span class="eraseAnnotation fas fa-times-circle"></span>
                                    <input type="hidden" class="annotationVal" value='{ "id": {{ $annotation->id }}, "tipo": "PASE", "activo": {{ $annotation->active->id }}, "pasivo": "", "efecto": {{ $annotation->efecto }} }' />
                                  </td>
                                <tr>
                              @break

                              @case("HERIDO")
                                <tr>
                                  <td scope="col">
                                    <span id="{{ $annotation->id }}" class="annotation">
                                      @php($herida = explode('|', $annotation->efecto))
                                      El nº {{ $annotation->active->numero }} ha lesionado al jugador nº {{ $annotation->pasive->numero }} ({{ $herida[0] }})
                                      @if($herida[1] == 'true')
                                        haciendo una falta
                                      @endif
                                      @if($herida[2] == 'true')
                                        tirándolo por la banda
                                      @endif
                                    </span>
                                    <span class="eraseAnnotation fas fa-times-circle"></span>
                                    <input type="hidden" class="annotationVal" value='{ "id": {{ $annotation->id }}, "tipo": "HERIDO", "activo": {{ $annotation->active->id }}, "pasivo": {{ $annotation->pasive->id }}, "efecto": "{{ $annotation->efecto }}" }' />
                                  </td>
                                </tr>
                              @break

                              @case("LESIONADO")
                                <tr>
                                  <td scope="col">
                                    <span id="{{ $annotation->id }}" class="annotation">
                                      @php($lesion = explode('|', $annotation->efecto))
                                      El nº {{ $annotation->active->numero }} ha sufrido una lesión ({{ $lesion[0] }}) {{ $lesion[1] }}.
                                      @if($lesion[2] == 'true')
                                        El jugador fue curado por el médico del equipo.
                                      @endif
                                      @if($lesion[3] == 'true')
                                        El jugador regeneró.
                                      @endif
                                    </span>
                                    <span class="eraseAnnotation fas fa-times-circle"></span>
                                    <input type="hidden" class="annotationVal" value='{ "id": {{ $annotation->id }}, "tipo": "LESIONADO", "activo": {{ $annotation->active->id }}, "pasivo": "", "efecto": "{{ $annotation->efecto }}" }' />
                                  </td>
                                </tr>
                              @break

                              @case("INTERCEPCION")
                                <tr>
                                  <td scope="col">
                                    <span id="{{ $annotation->id }}" class="annotation">
                                      El nº {{ $annotation->active->numero }} ha realizado una intercepción
                                    </span>
                                    <span class="eraseAnnotation fas fa-times-circle"></span>
                                    <input type="hidden" class="annotationVal" value='{ "id": {{ $annotation->id }}, "tipo": "INTERCEPCION", "activo": {{ $annotation->active->id }}, "pasivo": "", "efecto": "" }' />
                                  </td>
                                </tr>
                              @break

                              @case("TD")
                                <tr>
                                  <td scope="col">
                                    <span id="{{ $annotation->id }}" class="annotation">
                                      El nº {{ $annotation->active->numero }} ha anotado un Touchdown!!
                                    </span>
                                    <span class="eraseAnnotation fas fa-times-circle"></span>
                                    <input type="hidden" class="annotationVal" value='{ "id": {{ $annotation->id }}, "tipo": "TD", "activo": {{ $annotation->active->id }}, "pasivo": "", "efecto": "" }' />
                                  </td>
                                </tr>
                              @break

                              @case("MVP")
                                <tr>
                                  <td scope="col">
                                    <span id="{{ $annotation->id }}" class="annotation">
                                      El nº {{ $annotation->active->numero }} ha sido galardonado con un MVP!!
                                    </span>
                                    <span class="eraseAnnotation fas fa-times-circle"></span>
                                    <input type="hidden" class="annotationVal" value='{ "id": {{ $annotation->id }}, "tipo": "MVP", "activo": {{ $annotation->active->id }}, "pasivo": "", "efecto": "" }' />
                                  </td>
                                </tr>
                              @break

                            @endswitch
                          @endforeach
                        </tbody>
                      </table>
                      <table class="table table-bordered table-hover table-sm table-responsive">
                        <thead class="thead-dark">
                          <tr>
                            <th scope="col" class="col-md-3">
                              GASTO INCENTIVOS
                            </th>
                            <th scope="col" class="col-md-6">
                              NOTAS E INCENTIVOS
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td scope="col">
                              <input type="number" id="gastoinducements" name="gastoinducements" value="{{ $sheet->gastoinducements }}" />
                            </td>
                            <td scope="col">
                              <textarea name="inducements" id="inducements">
                                {{ $sheet->inducements }}
                              </textarea>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                    <div class="float-right">
                      <span class="pase btn btn-primary" data-toggle="modal" data-target="#passForm">PASE</span>
                      <span class="herido btn btn-primary" data-toggle="modal" data-target="#casForm">H+</span>
                      <span class="lesion btn btn-primary" data-toggle="modal" data-target="#lesionadoForm">H-</span>
                      <span class="intercepcion btn btn-primary" data-toggle="modal" data-target="#interceptionForm">INTERCEPCION</span>
                      <span class="td btn btn-primary" data-toggle="modal" data-target="#tdForm">TD</span>
                      <span class="mvp btn btn-primary" data-toggle="modal" data-target="#mvpForm">MVP</span>
                      <span id="finalizar" class="btn btn-primary">FINALIZAR</span>
                      <span id="enviar" class="btn btn-primary">Enviar</span>
                      <a href="{{ url(session('lastVisited')) }}" class="btn btn-danger">Cancelar</a>
                    </div>
                  </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SECCION CON HTML DE LOS FORMULARIOS ESPECIFICOS PARA CADA TIPO DE ANOTACION -->
<!-- Formulario para definir un pase-->
<div id="passForm" class="modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Anotar pase</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="passForm modal-body">
        Indica qué jugador ha hecho el pase y de cuántas yardas ha sido éste:
        Jugador que hace el pase:
        <select id="numeroJugador">
          @foreach($sheet->team->players->where("activo", 1)->where("lesionado", 0) as $player)
            <option value="{{ $player->id }}|{{ $player->numero }}">{{ $player->numero }} - {{ $player->position->nombre }} - {{ $player->nombre }}</option>
          @endforeach
        </select><br/>
        Yardas (casillas) de pase:
        <input type="number" id="yardaspase" name="yardaspase" value="0" />
      </div>
      <div class="modal-footer">
        <button class="passConfirm btn btn-success" data-dismiss="modal">Confirmar</button>
        <span class="btn btn-danger" data-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>
<!-- Fin del formulario para definir un pase-->

<!-- Formulario para definir un herido-->
<div id="casForm" class="modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Anotar Herido+</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="casForm modal-body">
        Indica qué jugador ha hecho el herido, el número del jugador herido y la herida producida antes de aplicar médico o tirar regeneración:
        Jugador que produce el herido:
        <select id="numeroJugador">
          @foreach($sheet->team->players->where("activo", 1)->where("lesionado", 0) as $player)
            <option value="{{ $player->id }}|{{ $player->numero }}">{{ $player->numero }} - {{ $player->position->nombre }} - {{ $player->nombre }}</option>
          @endforeach
        </select><br/>
        Jugador herido:
        <select id="numeroJugadorPasivo">
          @foreach($sheet->match->teams->where("equipo", "<>", $sheet->team->id)->first()->team->players->where("activo", 1)->where("lesionado", 0) as $pplayer)
            <option value="{{ $pplayer->id }}|{{ $pplayer->numero }}">{{ $pplayer->numero }} - {{ $pplayer->position->nombre }} - {{ $pplayer->nombre }}</option>
          @endforeach
        </select><br/>
        Herida producida:
        <select id="herida">
          <option value="38">11-38 Lesionado, sin efecto a largo plazo</option>
          <option value="41">41 Mandíbula rota, se pierde el próximo partido</option>
          <option value="42">42 Costillas rotas, se pierde el próximo partido</option>
          <option value="43">43 Brazo fracturado, se pierde el próximo partido</option>
          <option value="44">44 Pierna fracturada, se pierde el próximo partido</option>
          <option value="45">45 Mano rota, se pierde el próximo partido</option>
          <option value="46">46 Derrame ocular, se pierde el próximo partido</option>
          <option value="47">47 Rotura de ligamentos, se pierde el próximo partido</option>
          <option value="48">48 Pinzamiento nervioso, se pierde el próximo partido</option>
          <option value="51">51 Lesión de espalda, lesión permanente</option>
          <option value="52">52 Rodilla rota, lesión permanente</option>
          <option value="53">53 Tobillo roto, -1 MOV</option>
          <option value="54">54 Cadera rota, -1 MOV</option>
          <option value="55">55 Cráneo fracturado, -1 AV</option>
          <option value="56">56 Contusión grave, -1 AV</option>
          <option value="57">57 Cuello roto, -1 AGI</option>
          <option value="58">58 Clavícula rota, -1 FUE</option>
          <option value="68">61-68 Muerto</option>
        </select><br/>
        Haciendo falta
        <input type="checkbox" id="falta" value="falta" /><br/>
        Con el apoyo del público
        <input type="checkbox" id="publico" value="publico" /><br/>
      </div>
      <div class="modal-footer">
        <button class="casConfirm btn btn-success" data-dismiss="modal">Confirmar</button>
        <span class="btn btn-danger" data-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>
<!-- Fin del formulario para definir un herido-->

<!-- Formulario para definir una lesión de un jugador propio-->
<div id="lesionadoForm" class="modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Anotar Lesión (H-)</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="lesionadoForm modal-body">
        Indica qué jugador ha sufrido la herida, si ha sido curado por el médico o ha regenerado, indica la herida final:<br/>
        Jugador lesionado:
        <select id="numeroJugador">
          @foreach($sheet->team->players->where("activo", 1)->where("lesionado", 0) as $player)
            <option value="{{ $player->id }}|{{ $player->numero }}">{{ $player->numero }} - {{ $player->position->nombre }} - {{ $player->nombre }}</option>
          @endforeach
        </select><br/>
        Lesión sufrida:
        <select id="herida">
          <option value="38">11-38 Lesionado, sin efecto a largo plazo</option>
          <option value="41">41 Mandíbula rota, se pierde el próximo partido</option>
          <option value="42">42 Costillas rotas, se pierde el próximo partido</option>
          <option value="43">43 Brazo fracturado, se pierde el próximo partido</option>
          <option value="44">44 Pierna fracturada, se pierde el próximo partido</option>
          <option value="45">45 Mano rota, se pierde el próximo partido</option>
          <option value="46">46 Derrame ocular, se pierde el próximo partido</option>
          <option value="47">47 Rotura de ligamentos, se pierde el próximo partido</option>
          <option value="48">48 Pinzamiento nervioso, se pierde el próximo partido</option>
          <option value="51">51 Lesión de espalda, lesión permanente</option>
          <option value="52">52 Rodilla rota, lesión permanente</option>
          <option value="53">53 Tobillo roto, -1 MOV</option>
          <option value="54">54 Cadera rota, -1 MOV</option>
          <option value="55">55 Cráneo fracturado, -1 AV</option>
          <option value="56">56 Contusión grave, -1 AV</option>
          <option value="57">57 Cuello roto, -1 AGI</option>
          <option value="58">58 Clavícula rota, -1 FUE</option>
          <option value="68">61-68 Muerto</option>
        </select><br/>
        Modo en que se produce
        <select id="modoHerida">
          <option value="a consecuencia de un placaje">A consecuencia de un placaje</option>
          <option value="a consecuencia de una falta">A consecuencia de una falta</option>
          <option value="esquivando">Esquivando</option>
          <option value="forzando">Forzando</option>
          <option value="herido por el público">Herido por el público</option>
          <option value="herido por el hechicero">Hechicero</option>
          <option value="por circunstancias extraordinarias">Otra circunstancia</option>
        </select><br/>
        Se usa al médico del equipo
        <input type="checkbox" id="medico" value="medico" /><br/>
        Regenera
        <input type="checkbox" id="regenera" value="regenera" /><br/>
      </div>
      <div class="modal-footer">
        <button class="lesionadoConfirm btn btn-success" data-dismiss="modal">Confirmar</button>
        <span class="btn btn-danger" data-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>
<!-- Fin del formulario para definir una lesión de un jugador propio-->

<!-- Formulario para definir una intercepción-->
<div id="interceptionForm" class="modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Anotar intercepción</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="interceptionForm modal-body">
        Indica qué jugador ha hecho la intercepción:
        <select id="numeroJugador">
          @foreach($sheet->team->players->where("activo", 1)->where("lesionado", 0) as $player)
            <option value="{{ $player->id }}|{{ $player->numero }}">{{ $player->numero }} - {{ $player->position->nombre }} - {{ $player->nombre }}</option>
          @endforeach
        </select><br/>
      </div>
      <div class="modal-footer">
        <button class="interceptionConfirm btn btn-success" data-dismiss="modal">Confirmar</button>
        <span class="btn btn-danger" data-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>
<!-- Fin del formulario para definir una intercepción-->

<!-- Formulario para definir un td-->
<div id="tdForm" class="modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Anotar touchdown</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="tdForm modal-body">
        Indica qué jugador ha anotado el touchdown:
        <select id="numeroJugador">
          @foreach($sheet->team->players->where("activo", 1)->where("lesionado", 0) as $player)
            <option value="{{ $player->id }}|{{ $player->numero }}">{{ $player->numero }} - {{ $player->position->nombre }} - {{ $player->nombre }}</option>
          @endforeach
        </select><br/>
      </div>
      <div class="modal-footer">
        <button class="tdConfirm btn btn-success" data-dismiss="modal">Confirmar</button>
        <span class="btn btn-danger" data-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>
<!-- Fin del formulario para definir un td-->

<!-- Formulario para definir un mvp-->
<div id="mvpForm" class="modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Anotar MVP</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="mvpForm modal-body">
        Indica qué jugador ha sido galardonado con el MVP:
        <select id="numeroJugador">
          @foreach($sheet->team->players->where("activo", 1)->where("lesionado", 0) as $player)
            <option value="{{ $player->id }}|{{ $player->numero }}">{{ $player->numero }} - {{ $player->position->nombre }} - {{ $player->nombre }}</option>
          @endforeach
        </select><br/>
      </div>
      <div class="modal-footer">
        <button class="mvpConfirm btn btn-success" data-dismiss="modal">Confirmar</button>
        <span class="btn btn-danger" data-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>
<!-- Fin del formulario para definir un td-->

<!-- Mensaje de finalización del acta-->
<div id="finalizacionForm" class="modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Finalización del acta</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="finalizacionForm modal-body">
        @if(!$sheet->match->teams->where("equipo", "<>", $sheet->team->id)->first()->actafinalizada)
          Tu rival aún no ha finalizado su acta, indícanos el número de TD que has encajado:
          <input type="number" id="tdcontraModal" name="tdcontraModal" />
        @else
          <input type="hidden" id="tdcontraModal" name="tdcontraModal" value="{{ $sheet->match->teams->where('equipo', '<>', $sheet->team->id)->first()->tdf }}" />
        @endif
        Vas a finalizar el acta, esta acción pasará todos los datos a tu equipo e impedirá
        que puedas acceder de nuevo a gestionar el acta. Cualquier error o modificación que
        haya que llevar a cabo sobre ella deberá hacerla un comisionado. ¿Estás seguro de finalizar?
      </div>
      <div class="modal-footer">
        <button id="finalizacionConfirm" class="btn btn-success" data-dismiss="modal">Finalizar</button>
        <span class="btn btn-danger" data-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>
<!-- Fin del mensaje de finalización del acta -->

<!-- FIN DE LA SECCION DE FORMULARIOS ESPECIFICOS POR ANOTACION-->
@endsection
