@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Listado de usuarios</div>

                <div class="card-body">
                    <h3>Lista de usuarios</h3>
                    <table class="table table-bordered table-hover">
                      <thead class="thead-dark">
                        <tr>
                          @sortedTableHeader(/usuarios/lista, name, Nombre, {$page})
                          @sortedTableHeader(/usuarios/lista, email, Email, {$page})
                          @sortedTableHeader(/usuarios/lista, activo, Activo, {$page})
                          @sortedTableHeader(/usuarios/lista, rol, Rol, {$page})
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
                    {{ $users->appends(['sort' => $sort, 'ascdesc' => $ascdesc])->links() }}
                    <div style="text-align:right;">
                      <a href="crear"><span class="btn btn-primary">Nuevo usuario</span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
