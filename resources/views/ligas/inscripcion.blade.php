@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Inscripción a la liga</div>

                <div class="card-body">
                  <form method="post" action="{{ url('/ligas/aplicar/'.$league->id) }}">
                    @csrf
                    @if (count($listaTeams) == 0)
                      <div class="form-group">
                        <label for="nombre">No tienes ningún equipo válido para apuntarte a esta liga. Crea un equipo válido para las condiciones de la liga y vuelve a apuntarte</label>
                      </div>
                      <div class="form-group float-right">
                        <a href="{{ url('/equipos/crear') }}" class="btn btn-primary">Crear equipo</a>
                      </div>
                    @else
                      <div class="form-group">
                        <label for="nombre">Escoge el equipo con el que vas a inscribirte a la liga</label>
                        <select class="form-control col-md-12" id="equipo" name="equipo">
                          @foreach ($listaTeams as $team)
                            <option value="{{ $team->id }}">{{ $team->nombre }}</option>
                          @endforeach
                        </select>
                      </div>
                      <div class="form-group float-right">
                        <button type="submit" class="btn btn-primary">Enviar</button>
                        <a href="{{ url(session('lastVisited')) }}" class="btn btn-danger">Cancelar</a>
                      </div>
                    @endif
                  </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
