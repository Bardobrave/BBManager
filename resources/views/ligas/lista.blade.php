@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Listado de ligas</div>

                <div class="card-body">
                    <h3>Lista de ligas</h3>
                    <table class="table table-bordered table-hover">
                      <thead class="thead-dark">
                        <tr>
                          @sortedTableHeader(/ligas/lista, nombre, Nombre, {$page})
                          @sortedTableHeader(/ligas/lista, jornadas, Jornadas, {$page})
                          @sortedTableHeader(/ligas/lista, campeon, Campeon, {$page})
                          <th scope="col"></th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($leagues as $league)
                          <tr>
                            <td scope="col"><a href="{{ url('/ligas/detalle/'.$league->id) }}">{{ $league->nombre }}</a></td>
                            <td scope="col">{{ $league->weeks->count() }}</td>
                            <td scope="col">
                              @if($league->finalizada)
                                {{ $league->teams->sortBy('puntos')->first()->nombre }}
                              @else
                                En juego
                              @endif
                            </td>
                            <td scope="col" class="text-nowrap">
                              @if (Auth::user()->rol != 3)
                                <a href="{{ url('/ligas/editar/'.$league->id) }}"><span class="far fa-edit" title="Editar"> </span></a>
                                @if ($league->leagueTeams->count() == 0)
                                  <a href="{{ url('/ligas/eliminar/'.$league->id) }}"><span class="fas fa-trash-alt" title="Eliminar"> </span></a>
                                @endif
                              @endif
                              @if ($league->abierta && $league->teams->where("usuario", Auth::user()->id)->count() == 0)
                                <a href="{{ url('/ligas/aplicar/'.$league->id) }}"><span class="fas fa-hand-paper" title="Liga abierta:click aquí apuntarse a la liga"> </span></a>
                              @else
                                @if ($league->abierta)
                                  <span class="fas fa-hand-paper" title="Liga abierta"> </span>
                                @else
                                  <span class="fas fa-lock" title="Liga cerrada"> </span>
                                @endif
                              @endif
                            </td>
                          </tr>
                        @endforeach
                      <tbody>
                    </table>
                    {{ $leagues->appends(['sort' => $sort, 'ascdesc' => $ascdesc])->links() }}
                    @if(Auth::user()->rol != 3)
                      <div style="text-align:right;">
                        <a href="{{ url('/ligas/crear') }}"><button class="btn btn-primary">Crear liga</button></a>
                      </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
