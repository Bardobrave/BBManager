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
                <div class="card-header">Nuevo usuario</div>

                <div class="card-body">
                  @if ($errors->any())
                    <div class="alert alert-danger">
                      Hay errores en los datos enviados a través del formulario
                    </div>
                  @endif
                  <form method="post" action="{{ url('/usuarios/crear') }}">
                    @csrf
                    <div class="form-group">
                      <label for="name">Nombre </label>
                      <input class="form-control @error('name') is-invalid @enderror" type="text" value="{{ old('name') }}" id="name" name="name" placeholder="Nombre del usuario"/>
                      @error('name')
                        <small class="field-validation-error form-text alert-danger" id="nameErrorMsg">{{ $message }}</small>
                      @enderror
                    </div>
                    <div class="form-group">
                      <label for="email">Email </label>
                      <input class="form-control @error('email') is-invalid @enderror" type="email" value="{{ old('email') }}" id="email" name="email" placeholder="Email"/>
                      @error('email')
                        <small class="field-validation-error form-text alert-danger" id="emailErrorMsg">{{ $message }}</small>
                      @enderror
                    </div>
                    <div class="form-group">
                      <label for="password">Contraseña </label>
                      <input class="form-control @error('password') is-invalid @enderror" type="password" value="" id="password" name="password"/>
                      @error('password')
                        <small class="field-validation-error form-text alert-danger" id="passwordErrorMsg">{{ $message }}</small>
                      @enderror
                      <small class="form-text text-muted" id="passwordHelper">La contraseña debe tener al menos 8 caracteres</small>
                    </div>
                    <div class="form-group">
                      <label for="confirmPassword">Confirmar contraseña </label>
                      <input class="form-control @error('confirmPassword') is-invalid @enderror" type="password" value="" id="confirmPassword" name="confirmPassword"/>
                      <small class="form-text text-muted" id="confirmPasswordHelper">La contraseña y la confirmación deben coincidir</small>
                      @error('confirmPassword')
                        <small class="field-validation-error form-text alert-danger" id="confirmPasswordErrorMsg">{{ $message }}</small>
                      @enderror
                    </div>
                    <div class="form-group">
                      <label for="rol">Rol </label>
                      <select class="form-control col-md-3 @error('rol') is-invalid @enderror" id="rol" name="rol">
                        @foreach ($roles as $rol)
                          <option value="{{ $rol->id }}">{{ $rol->nombre }}</option>
                        @endforeach
                      </select>
                      @error('rol')
                        <small class="field-validation-error form-text alert-danger" id="rolErrorMsg">{{ $message }}</small>
                      @enderror
                    </div>
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
