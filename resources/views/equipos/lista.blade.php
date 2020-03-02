@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Listado de equipos</div>

                <div class="card-body">
                    <h3>Lista de equipos</h3>
                    <table class="table table-bordered table-hover">
                      <thead class="thead-dark">
                        <tr>
                          @sortedTableHeader(/equipos/lista, nombre, Nombre, {$page})
                          @sortedTableHeader(/equipos/lista, raza, Raza, {$page})
                          @sortedTableHeader(/equipos/lista, valoracion, Valoracion, {$page})
                          @if (Auth::user()->rol != 3)
                            @sortedTableHeader(/equipos/lista, usuario, Dueño, {$page})
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
                    {{ $teams->appends(['sort' => $sort, 'ascdesc' => $ascdesc])->links() }}

                    <div class="float-right">
                      <a href="{{ url('/equipos/crear') }}"><button class="btn btn-primary">Crear equipo</button></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
