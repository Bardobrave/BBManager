@extends('layouts.app')

@push('scripts')
  <script src="{{ asset('js/plugins/validatorPlugin.js') }}" type="text/javascript" defer></script>
  <script src="{{ asset('js/validators/users.js') }}" type="text/javascript" defer></script>
@endpush

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Edición del usuario</div>

                <div class="card-body">
                  @if ($errors->any())
                    <div class="alert alert-danger">
                      Hay errores en los datos enviados a través del formulario
                    </div>
                  @endif
                  <form method="post" action="{{ url('/usuarios/editar/'.$user->id) }}">
                    @csrf
                    <input type="hidden" value="{{ $user->id }}" id="idUsuario" name="idUsuario" />
                    <div class="form-group">
                      <label for="name">Nombre </label>
                      <input class="form-control @error('name') is-invalid @enderror" type="text" value="{{ $user->name }}" id="name" name="name"/>
                      @error('name')
                        <small class="field-validation-error form-text alert-danger" id="nameErrorMsg">{{ $message }}</small>
                      @enderror
                    </div>
                    <div class="form-group">
                      <label for="email">Email </label>
                      <input class="form-control @error('email') is-invalid @enderror" type="email" value="{{ $user->email }}" id="email" name="email"/>
                      @error('email')
                        <small class="field-validation-error form-text alert-danger" id="emailErrorMsg">{{ $message }}</small>
                      @enderror
                    </div>
                    @if (Auth::user()->rol == 1)
                      <div class="form-group">
                        <label for="rol">Rol </label>
                        <select class="form-control col-md-3 @error('rol') is-invalid @enderror" id="rol" name="rol">
                          @foreach ($roles as $rol)
                            <option value="{{ $rol->id }}" {{ ($user->roleName->nombre == $rol->nombre) ? 'selected="selected"' : '' }}>{{ $rol->nombre }}</option>
                          @endforeach
                        </select>
                      </div>
                    @else
                      <div class="form-group">
                        <label for="rol">Rol</label>
                        <input class="form-control @error('rol') is-invalid @enderror" type="text" value="{{ $user->roleName->nombre }}" readonly/>
                      </div>
                    @endif
                    @error('rol')
                      <small class="field-validation-error form-text alert-danger" id="rolErrorMsg">{{ $message }}</small>
                    @enderror
                    <div class="form-group float-right">
                      <button type="submit" class="btn btn-primary">Enviar</button>
                      <a href="{{ url(session('lastVisited')) }}" class="btn btn-danger">Cancelar</a>
                    </div>
                  </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
