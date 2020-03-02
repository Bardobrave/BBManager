@extends('layouts.app')

@push('scripts')
  <script src="{{ asset('js/gestionarJornadas.js') }}" type="text/javascript" defer></script>
@endpush

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Crear nueva jornada</div>

                <div class="card-body">
                  <form id="formJornada" method="post" action="{{ url('/jornadas/crear/'.$league->id) }}">
                    @csrf
                    <input type="hidden" value="" id="emparejamientos" name="emparejamientos" />
                    <div class="form-group">
                      <div id="dragDropContainer" class="col-md-12 float-left">
                        <!--Se hace un contenedor para todos los equipos, y otro para los emparejamientos-->
                        <div id="equipos" class="dragContainer col-md-4 float-left">
                          <h4>EQUIPOS</h4>
                          @for($x = 1; $x <= $league->numgrupos; $x++)
                            <h5>GRUPO {{ $x }}</h5>
                            @foreach($league->leagueTeams->where('grupo', $x) as $leagueTeam)
                              <div id="equipo_{{ $leagueTeam->team->id }}" class="equipoJornada" draggable="true">
                                {{ $leagueTeam->team->nombre }}
                              </div>
                            @endforeach
                          @endfor
                          @if ($league->jornadaDescanso)
                            <div id="descanso_0" class="equipoGrupo" draggable="true">
                              DESCANSA
                            </div>
                          @endif
                        </div>
                        <div id="emparejamientosContainer" class="equiposContainer col-md-8 float-left">
                          <h4>EMPAREJAMIENTOS</h4>
                          @for($x = 0; $x < $league->teams->count() / 2; $x++)
                            <div id="emparejamiento_{{ $x }}" class="emparejamientoContainer col-md-12 float-left">
                              <div id="anfitrion_{{ $x }}" class="dragContainer anfitrion col-md-5 float-left">
                              </div>
                              <strong class="vs">- VS -</strong>
                              <div id="visitante_{{ $x }}" class="dragContainer visitante col-md-5 float-right">
                              </div>
                            </div>
                          @endfor
                        </div>
                      </div>
                    </div>
                    <div class="form-group col-md-12 float-left">
                      <label for="eliminatoria">Jornada eliminatoria</label>
                      <input type="checkbox" id="eliminatoria" name="eliminatoria" value="1" />
                    </div>
                    <div class="form-group col-md-12 float-left">
                      <label for="raza">Observaciones</label>
                      <textarea class="form-control" id="observaciones" name="observaciones"></textarea>
                    </div>
                    <div class="form-group float-right">
                      <span id="enviar" class="btn btn-primary">Enviar</span>
                      <a href="{{ url(session('lastVisited')) }}" class="btn btn-danger">Cancelar</a>
                    </div>
                  </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
