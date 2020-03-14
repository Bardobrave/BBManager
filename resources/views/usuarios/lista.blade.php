@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Listado de usuarios</div>

                <div class="card-body">
                    <h3>Lista de usuarios</h3>
                    <div class="card card-body buscador form-group">
                      <form id="buscador" method="post" action="{{ url('/usuarios/lista') }}" />
                        @csrf
                        <div class="col-md-6 float-left form-group">
                          <label for="name">Nombre </label>
                          <input class="form-control" type="text" value="{{ $name }}" id="name" name="name" placeholder="Nombre del usuario"/>
                          <label for="estado">Estado</label>
                          <select class="form-control" name="estado">
                            <option value="Todos" @if($estado == "Todos") selected="selected" @endif>Todos</option>
                            <option value="Activos" @if($estado == "Activos") selected="selected" @endif>Activos</option>
                            <option value="Inactivos" @if($estado == "Inactivos") selected="selected" @endif>Inactivos</option>
                          </select>
                        </div>
                        <div class="col-md-6 float-left form-group">
                          <label for="email">Email</label>
                          <input class="form-control" type="text" value="{{ $email }}" id="email" name="email" placeholder="Dirección de correo" />
                          <label for="rol">Rol</label>
                          <select class="form-control" name="rol">
                            <option value="0" @if($rol == "0")selected="selected"@endif></option>
                            @foreach($roles as $currentRol)
                              <option value="{{ $currentRol->id }}" @if($rol == $currentRol->id)selected="selected"@endif>
                                {{ $currentRol->nombre }}
                              </option>
                            @endforeach
                          </select>
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
                      <a href="crear"><span class="btn btn-primary">Nuevo usuario</span></a>
                    </div>
                    <br/>
                    <table class="table table-bordered table-hover">
                      <thead class="thead-dark">
                        <tr>
                          @sortedTableHeader(/usuarios/lista, name, Nombre, {$page}, name|email|estado|rol, {$name}|{$email}|{$estado}|{$rol})
                          @sortedTableHeader(/usuarios/lista, email, Email, {$page}, name|email|estado|rol, {$name}|{$email}|{$estado}|{$rol})
                          @sortedTableHeader(/usuarios/lista, activo, Activo, {$page}, name|email|estado|rol, {$name}|{$email}|{$estado}|{$rol})
                          @sortedTableHeader(/usuarios/lista, rol, Rol, {$page}, name|email|estado|rol, {$name}|{$email}|{$estado}|{$rol})
                          <th scope="col"></th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($users as $user)
                          <tr>
                            <td scope="col"><a href="{{ url('/usuarios/detalle/'.$user->id) }}">{{ $user->name }}</a></td>
                            <td scope="col">{{ $user->email }}</td>
                            <td scope="col">
                                @if ($user->activo == 1)
                                  <span class="badge badge-success">ACTIVO</span>
                                @else
                                  <span class="badge badge-danger">INACTIVO</span>
                                @endif
                            </td>
                            <td scope="col">{{ $user->roleName->nombre }}</td>
                            <td scope="col" class="text-nowrap">
                              <a href="{{ url('/usuarios/editar/'.$user->id) }}"><span class="far fa-edit" title="Editar"> </span></a>
                              @if ($user->activo == 1)
                                <a href="{{ url('/usuarios/desactivar/'.$user->id) }}"><span class="fas fa-toggle-off" title="Desactivar"> </span></a>
                              @else
                                <a href="{{ url('/usuarios/activar/'.$user->id) }}"><span class="fas fa-toggle-on" title="Activar"> </span></a>
                              @endif
                              <a href="{{ url('/usuarios/eliminar/'.$user->id) }}"><span class="fas fa-trash-alt" title="Eliminar"> </span></a>
                            </td>
                          </tr>
                        @endforeach
                      <tbody>
                    </table>
                    {{ $users->appends(['sort' => $sort, 'ascdesc' => $ascdesc, 'name' => $name,
                      'email' => $email, 'estado' => $estado, 'rol' => $rol])->links() }}
                    <div style="text-align:right;">
                      <a href="crear"><span class="btn btn-primary">Nuevo usuario</span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
