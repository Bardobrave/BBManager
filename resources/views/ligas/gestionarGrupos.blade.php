@extends('layouts.app')

@push('scripts')
  <script src="{{ asset('js/gestionarGruposLigas.js') }}" type="text/javascript" defer></script>
@endpush

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Gestionar Grupos de la Liga</div>

                <div class="card-body">
                  <form id="formGrupos" method="post" action="{{ url('/ligas/asignarGrupos/'.$league->id) }}">
                    @csrf
                    <input type="hidden" value="{{ $league->id }}" id="idLiga" name="idLiga" />
                    <input type="hidden" value="" id="grupos" name="grupos" />
                    <div id="dragDropContainer" class="col-md-12 float-left">
                      <!--Habrá un contenedor para cada grupo en la liga, con los equipos que tiene actualmente asignados-->
                      @for($grupo = 1; $grupo <= $league->numgrupos; $grupo++)
                        <div id="grupo_{{ $grupo }}" class="grupoContainer col-md-6 float-left">
                          <h4>GRUPO {{ $grupo }}</h4>
                          @foreach($league->leagueTeams->where("grupo", $grupo) as $groupTeam)
                            <div id="equipo_{{ $groupTeam->team->id }}" class="equipoGrupo" draggable="true">
                              {{ $groupTeam->team->nombre }}
                            </div>
                          @endforeach
                        </div>
                      @endfor
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
