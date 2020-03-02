@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Acta del partido</div>

                <div class="card-body">
                  @php($actaHome = $match->teams->where("equipo", $match->local->id)->first())
                  @php($actaAway = $match->teams->where("equipo", $match->away->id)->first())
                    <!--EQUIPO LOCAL-->
                    <div class="acta home col-md-12 float-left">
                      <div class="actaHeader col-md-6 float-left">
                        <strong>{{ $match->local->nombre }}</strong>
                      </div>
                      <div class="actaHeader col-md-6 float-left">
                        <strong>{{ $match->away->nombre }}</strong>
                      </div>
                      <table class="table table-bordered table-hover table-sm table-responsive">
                        <thead class="thead-dark">
                          <tr>
                            <th scope="col" class="col-md-2">
                              TESO INI
                            </th>
                            <th scope="col" class="col-md-2">
                              RECAUDACION
                            </th>
                            <th scope="col" class="col-md-2">
                              TESO FINAL
                            </th>
                            <th scope="col" class="col-md-2">
                              TESO INI
                            </th>
                            <th scope="col" class="col-md-2">
                              RECAUDACION
                            </th>
                            <th scope="col" class="col-md-2">
                              TESO FINAL
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td scope="col">
                              {{ $actaHome->tesoinicial }}
                            </td>
                            <td scope="col">
                              {{ $actaHome->recaudacion }}
                            </td>
                            <td scope="col">
                              {{ $actaHome->tesofinal }}
                            </td>
                            <td scope="col">
                              {{ $actaAway->tesoinicial }}
                            </td>
                            <td scope="col">
                              {{ $actaAway->recaudacion }}
                            </td>
                            <td scope="col">
                              {{ $actaAway->tesofinal }}
                            </td>
                          </tr>
                        </tbody>
                      </table>
                      <table class="table table-bordered table-hover table-sm table-responsive">
                        <thead class="thead-dark">
                          <tr>
                            <th scope="col" class="col-md-2">
                              FF INI
                            </th>
                            <th scope="col" class="col-md-2">
                              ESPECTADORES
                            </th>
                            <th scope="col" class="col-md-2">
                              FF FINAL
                            </th>
                            <th scope="col" class="col-md-2">
                              FF INI
                            </th>
                            <th scope="col" class="col-md-2">
                              ESPECTADORES
                            </th>
                            <th scope="col" class="col-md-2">
                              FF FINAL
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td scope="col">
                              {{ $actaHome->ffinicial }}
                            </td>
                            <td scope="col">
                              {{ $actaHome->espectadores }}
                            </td>
                            <td scope="col">
                              {{ $actaHome->fffinal }}
                            </td>
                            <td scope="col">
                              {{ $actaAway->ffinicial }}
                            </td>
                            <td scope="col">
                              {{ $actaAway->espectadores }}
                            </td>
                            <td scope="col">
                              {{ $actaAway->fffinal }}
                            </td>
                          </tr>
                        </tbody>
                      </table>
                      <table class="annotationsTable table table-bordered table-hover table-sm table-responsive">
                        <thead class="thead-dark">
                          <tr>
                            <th scope="col" class="col-md-6">
                              HIGHLIGHTS DEL PARTIDO
                            </th>
                            <th scope="col" class="col-md-6">
                              HIGHLIGHTS DEL PARTIDO
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>
                              @foreach($actaHome->annotations->sortBy("active.numero") as $annotation)
                                @switch($annotation->type->tipo)

                                  @case("PASE")
                                    <div class="annotation">
                                      El nº {{ $annotation->active->numero }} ha realizado un pase de {{ $annotation->efecto }} yardas.
                                    </div>
                                  @break

                                  @case("HERIDO")
                                    <div class="annotation">
                                      @php($herida = explode('|', $annotation->efecto))
                                      El nº {{ $annotation->active->numero }} ha lesionado al jugador nº {{ $annotation->pasive->numero }} ({{ $herida[0] }})
                                      @if($herida[1] == 'true')
                                        haciendo una falta
                                      @endif
                                      @if($herida[2] == 'true')
                                        tirándolo por la banda
                                      @endif
                                    </div>
                                  @break

                                  @case("LESIONADO")
                                    <div class="annotation">
                                      @php($lesion = explode('|', $annotation->efecto))
                                      El nº {{ $annotation->active->numero }} ha sufrido una lesión ({{ $lesion[0] }}) {{ $lesion[1] }}.
                                      @if($lesion[2] == 'true')
                                        El jugador fue curado por el médico del equipo.
                                      @endif
                                      @if($lesion[3] == 'true')
                                        El jugador regeneró.
                                      @endif
                                    </div>
                                  @break

                                  @case("INTERCEPCION")
                                    <div class="annotation">
                                      El nº {{ $annotation->active->numero }} ha realizado una intercepción
                                    </div>
                                  @break

                                  @case("TD")
                                    <div class="annotation">
                                      El nº {{ $annotation->active->numero }} ha anotado un Touchdown!!
                                    </div>
                                  @break

                                  @case("MVP")
                                    <div class="annotation">
                                      El nº {{ $annotation->active->numero }} ha sido galardonado con un MVP!!
                                    </div>
                                  @break

                                @endswitch
                              @endforeach
                            </td>
                            <td>
                              @foreach($actaAway->annotations->sortBy("active.numero") as $annotation)
                                @switch($annotation->type->tipo)

                                  @case("PASE")
                                    <div class="annotation">
                                      El nº {{ $annotation->active->numero }} ha realizado un pase de {{ $annotation->efecto }} yardas.
                                    </div>
                                  @break

                                  @case("HERIDO")
                                    <div class="annotation">
                                      @php($herida = explode('|', $annotation->efecto))
                                      El nº {{ $annotation->active->numero }} ha lesionado al jugador nº {{ $annotation->pasive->numero }} ({{ $herida[0] }})
                                      @if($herida[1] == 'true')
                                        haciendo una falta
                                      @endif
                                      @if($herida[2] == 'true')
                                        tirándolo por la banda
                                      @endif
                                    </div>
                                  @break

                                  @case("LESIONADO")
                                    <div class="annotation">
                                      @php($lesion = explode('|', $annotation->efecto))
                                      El nº {{ $annotation->active->numero }} ha sufrido una lesión ({{ $lesion[0] }}) {{ $lesion[1] }}.
                                      @if($lesion[2] == 'true')
                                        El jugador fue curado por el médico del equipo.
                                      @endif
                                      @if($lesion[3] == 'true')
                                        El jugador regeneró.
                                      @endif
                                    </div>
                                  @break

                                  @case("INTERCEPCION")
                                    <div class="annotation">
                                      El nº {{ $annotation->active->numero }} ha realizado una intercepción
                                    </div>
                                  @break

                                  @case("TD")
                                    <div class="annotation">
                                      El nº {{ $annotation->active->numero }} ha anotado un Touchdown!!
                                    </div>
                                  @break

                                  @case("MVP")
                                    <div class="annotation">
                                      El nº {{ $annotation->active->numero }} ha sido galardonado con un MVP!!
                                    </div>
                                  @break

                                @endswitch
                              @endforeach
                            </td>
                          </tr>
                        </tbody>
                      </table>
                      <table class="table table-bordered table-hover table-sm table-responsive">
                        <thead class="thead-dark">
                          <tr>
                            <th scope="col" class="col-md-2">
                              GASTO INCEN
                            </th>
                            <th scope="col" class="col-md-4">
                              NOTAS E INCENTIVOS
                            </th>
                            <th scope="col" class="col-md-2">
                              GASTO INCEN
                            </th>
                            <th scope="col" class="col-md-4">
                              NOTAS E INCENTIVOS
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td scope="col">
                              {{ $actaHome->gastoinducements }}
                            </td>
                            <td scope="col">
                              {{ $actaHome->inducements }}
                            </td>
                            <td scope="col">
                              {{ $actaAway->gastoinducements }}
                            </td>
                            <td scope="col">
                              {{ $actaAway->inducements }}
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                    <div class="float-right">
                      @if (!$actaHome->actafinalizada && (Auth::user()->rol != 3 || $match->local->user->id == Auth::user()->id))
                        <a href="{{ url('/actas/editar/'.$actaHome->id) }}"><button class="btn btn-primary">Editar Local</button></a>
                      @elseif($actaHome->actafinalizada && Auth::user()->rol != 3)
                        <a href="{{ url('/actas/reabrir/'.$actaHome->id) }}"><button class="btn btn-primary">Reabrir local</button></a>
                      @endif
                      @if (!$actaAway->actafinalizada && (Auth::user()->rol != 3 || $match->away->user->id == Auth::user()->id))
                        <a href="{{ url('/actas/editar/'.$actaAway->id) }}"><button class="btn btn-primary">Editar Visitante</button></a>
                      @elseif($actaAway->actafinalizada && Auth::user()->rol != 3)
                        <a href="{{ url('/actas/reabrir/'.$actaAway->id) }}"><button class="btn btn-primary">Reabrir visitante</button></a>
                      @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
