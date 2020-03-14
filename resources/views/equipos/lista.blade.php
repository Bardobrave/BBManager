@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Listado de equipos</div>

                <div class="card-body">
                    <h3>Lista de equipos</h3>
                    <div class="card card-body buscador form-group">
                      <form id="buscador" method="post" action="{{ url('/equipos/lista') }}" />
                        @csrf
                        <div class="col-md-6 float-left form-group">
                          <label for="nombre">Nombre </label>
                          <input class="form-control" type="text" value="{{ $nombre }}" id="nombre" name="nombre" placeholder="Nombre del equipo"/>
                          <label for="valoracionDesde">Valoración desde</label>
                          <input class="form-control" type="text" value="{{ $valoracionDesde }}" id="valoracionDesde" name="valoracionDesde" />
                        </div>
                        <div class="col-md-6 float-left form-group">
                          <label for="raza">Raza</label>
                          <select class="form-control" name="raza">
                            <option value="0" @if($raza == "0")selected="selected"@endif></option>
                            @foreach($razas as $currentRace)
                              <option value="{{ $currentRace->id }}" @if($raza == $currentRace->id)selected="selected"@endif>
                                {{ $currentRace->nombre }}
                              </option>
                            @endforeach
                          </select>
                          <label for="valoracionHasta">Valoración hasta</label>
                          <input class="form-control" type="text" value="{{ $valoracionHasta }}" id="valoracionHasta" name="valoracionHasta" />
                        </div>
                        <input type="hidden" name="sort" value="{{ $sort }}" />
                        <input type="hidden" name="ascdesc" value="{{ $ascdesc }}" />
                        <input type="hidden" name="page" value="{{ $page }}" />
                        <div class="form-group float-right">
                          <span class="reset btn btn-primary">Limpiar</span>
                          <button class="submit btn btn-primary">Buscar</button>
                        </div>
                      </form>
                    </div>
                    <div style="text-align:right;">
                      <a href="{{ url('/equipos/crear') }}"><button class="btn btn-primary">Crear equipo</button></a>
                    </div>
                    <br/>
                    <table class="table table-bordered table-hover">
                      <thead class="thead-dark">
                        <tr>
                          @sortedTableHeader(/equipos/lista, nombre, Nombre, {$page}, nombre|raza|valoracionDesde|valoracionHasta, {$nombre}|{$raza}|{$valoracionDesde}|{$valoracionHasta})
                          @sortedTableHeader(/equipos/lista, raza, Raza, {$page}, nombre|raza|valoracionDesde|valoracionHasta, {$nombre}|{$raza}|{$valoracionDesde}|{$valoracionHasta})
                          @sortedTableHeader(/equipos/lista, valoracion, Valoracion, {$page}, nombre|raza|valoracionDesde|valoracionHasta, {$nombre}|{$raza}|{$valoracionDesde}|{$valoracionHasta})
                          @if (Auth::user()->rol != 3)
                            @sortedTableHeader(/equipos/lista, usuario, Dueño, {$page}, nombre|raza|valoracionDesde|valoracionHasta, {$nombre}|{$raza}|{$valoracionDesde}|{$valoracionHasta})
                          @endif
                          <th scope="col"></th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($teams as $team)
                          <tr>
                            <td scope="col"><a href="{{ url('/equipos/detalle/'.$team->id) }}">{{ $team->nombre }}</a></td>
                            <td scope="col">{{ $team->race->nombre }}</td>
                            <td scope="col">{{ $team->valoracion }}</td>
                            @if (Auth::user()->rol != 3)
                              <td scope="col">{{ $team->user->name }}</td>
                            @endif
                            <td scope="col" class="text-nowrap">
                              @if (Auth::user()->rol != 3 || Auth::user()->id == $team->user->id)
                                <a href="{{ url('/equipos/editar/'.$team->id) }}"><span class="far fa-edit" title="Editar"> </span></a>
                                @if ($team->leagues->count() == 0)
                                  <a href="{{ url('/equipos/eliminar/'.$team->id) }}"><span class="fas fa-trash-alt" title="Eliminar"> </span></a>
                                @endif
                              @endif
                              @if (!$team->activo)
                                <span class="fas fa-hammer" title="Inactivo: en proceso de creación"> </span>
                              @endif
                              @if ($team->preparado)
                                <span class="fas fa-football-ball" title="Preparado para jugar"> </span>
                              @endif
                            </td>
                          </tr>
                        @endforeach
                      <tbody>
                    </table>
                    {{ $teams->appends(['nombre' => $nombre, 'raza' => $raza, 'valoracionDesde' => $valoracionDesde,
                      'valoracionHasta' => $valoracionHasta, 'sort' => $sort, 'ascdesc' => $ascdesc])->links() }}

                    <div class="float-right">
                      <a href="{{ url('/equipos/crear') }}"><button class="btn btn-primary">Crear equipo</button></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
