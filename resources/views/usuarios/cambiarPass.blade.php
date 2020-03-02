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
                <div class="card-header">Cambiar contraseña de usuario</div>

                <div class="card-body">
                  @if ($errors->any())
                    <div class="alert alert-danger">
                      Hay errores en los datos enviados a través del formulario
                    </div>
                  @endif
                  <form method="post" action="{{ url('/usuarios/cambiarPass/'.$user->id) }}">
                    @csrf
                    <div class="form-group">
                      Vas a proceder a cambiar la contraseña del usuario {{ $user->name }}
                    </div>
                    <div class="form-group">
                      <label for="email">Contraseña </label>
                      <input class="form-control @error('password') is-invalid @enderror" type="password" value="" id="password" name="password"/>
                      @error('password')
                        <small class="field-validation-error form-text alert-danger" id="passwordErrorMsg">{{ $message }}</small>
                      @enderror
                      <small class="form-text text-muted" id="passwordHelper">La contraseña debe tener al menos 8 caracteres</small>
                    </div>
                    <div class="form-group">
                      <label for="email">Confirmar contraseña </label>
                      <input class="form-control @error('confirmPassword') is-invalid @enderror" type="password" value="" id="confirmPassword" name="confirmPassword"/>
                      @error('confirmPassword')
                        <small class="field-validation-error form-text alert-danger" id="confirmPasswordErrorMsg">{{ $message }}</small>
                      @enderror
                      <small class="form-text text-muted" id="confirmPasswordHelper">La contraseña y la confirmación deben coincidir</small>
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
