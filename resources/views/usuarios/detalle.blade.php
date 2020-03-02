@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Detalle del usuario</div>

                <div class="card-body">
                    <h3>{{ $user->name }}</h3>
                    <div class="form-group">
                      <strong>Email: </strong>{{ $user->email }}
                    </div>
                    <div class="form-group">
                      <strong>Rol: </strong>{{ $user->roleName->nombre }}
                    </div>
                    <div class="form-group">
                      <span class="badge badge-dark">{{ ($user->activo == 1) ? 'ACTIVO' : 'INACTIVO' }}</span>
                    </div>
                    <div class="form-group">
                      <h4>Lista de equipos</h4>
                      <table class="table table-bordered table-hover">
                        <thead class="thead-dark">
                          <tr>
                            <th scope="col">Nombre</th>
                            <th scope="col">Raza</th>
                            <th scope="col">Valoración</th>
                            <th scope="col"></th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach ($user->teams as $team)
                            <tr>
                              <td scope="col"><a href="{{ url('/equipos/detalle/'.$team->id) }}">{{ $team->nombre }}</a></td>
                              <td scope="col">{{ $team->race->nombre }}</td>
                              <td scope="col">{{ $team->valoracion }}</td>
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
                    </div>
                    <div class="float-right">
                      <a href="{{ url('/usuarios/editar/'.$user->id) }}"><button class="btn btn-primary">Editar</button></a>
                      <a href="{{ url('/usuarios/cambiarPass/'.$user-> id) }}"><button class="btn btn-primary">Cambiar Contraseña</button></a>
                      @if (Auth::user()->id == $user->id)
                        <a href="{{ url('/equipos/crear') }}"><button class="btn btn-primary">Crear equipo</button></a>
                      @endif
                      @if (Auth::user()->rol == 1)
                        @if ($user->activo == 1)
                          <a href="{{ url('/usuarios/desactivar/'.$user->id) }}"><button class="btn btn-secondary">Desactivar</button></a>
                        @else
                          <a href="{{ url('/usuarios/activar/'.$user->id) }}"><button class="btn btn-success">Activar</button></a>
                        @endif
                        <a href="{{ url('/usuarios/eliminar/'.$user->id) }}"><button class="btn btn-danger">Borrar</button></a>
                      @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
