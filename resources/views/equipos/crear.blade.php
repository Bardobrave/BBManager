@extends('layouts.app')

@push('scripts')
  <script src="{{ asset('js/plugins/validatorPlugin.js') }}" type="text/javascript" defer></script>
  <script src="{{ asset('js/validators/equipos.js') }}" type="text/javascript" defer></script>
@endpush

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Nuevo equipo</div>

                <div class="card-body">
                  @if ($errors->any())
                    <div class="alert alert-danger">
                      Hay errores en los datos enviados a través del formulario
                    </div>
                  @endif
                  <form method="post" action="{{ url('/equipos/crear') }}">
                    @csrf
                    <div class="form-group">
                      <label for="nombre">Nombre</label>
                      <input class="form-control @error('nombre') is-invalid @enderror" type="text" value="{{ old('nombre') }}" id="nombre" name="nombre" placeholder="Nombre del equipo"/>
                      @error('nombre')
                        <small class="field-validation-error form-text alert-danger" id="nombreErrorMsg">{{ $message }}</small>
                      @enderror
                    </div>
                    <div class="form-group">
                      <label for="raza">Raza</label>
                      <select class="form-control col-md-3 @error('raza') is-invalid @enderror" id="raza" name="raza">
                        @foreach ($razas as $raza)
                          <option value="{{ $raza->id }}">{{ $raza->nombre }}</option>
                        @endforeach
                      </select>
                      @error('raza')
                        <small class="field-validation-error form-text alert-danger" id="razaErrorMsg">{{ $message }}</small>
                      @enderror
                    </div>
                    <div class="form-group">
                      <label for="presupuesto">Presupuesto inicial del equipo </label>
                      <input class="form-control @error('presupuesto') is-invalid @enderror" type="number" value="" id="presupuesto" name="presupuesto" placeholder="Indica de cuánto dinero dispone el equipo para empezar"/>
                      @error('presupuesto')
                        <small class="field-validation-error form-text alert-danger" id="presupuestoErrorMsg">{{ $message }}</small>
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
