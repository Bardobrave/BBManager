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
                <div class="card-header">Editar equipo</div>

                <div class="card-body">
                  @if ($errors->any())
                    <div class="alert alert-danger">
                      Hay errores en los datos enviados a través del formulario 
                    </div>
                  @endif
                  <form method="post" action="{{ url('/equipos/editar/'.$team->id) }}">
                    @csrf
                    <input type="hidden" value="{{ $team->id }}" id="idEquipo" name="idEquipo" />
                    <div class="form-group col-md-12">
                      <label for="nombre">Nombre</label>
                      <input class="form-control @error('nombre') is-invalid @enderror" type="text" value="{{ $team->nombre }}" id="nombre" name="nombre" placeholder="Nombre del equipo"/>
                      @error('nombre')
                        <small class="field-validation-error form-text alert-danger" id="nombreErrorMsg">{{ $message }}</small>
                      @enderror
                    </div>
                    <div class="form-group col-md-12">
                      <label for="raza">Raza</label>
                      @if ($team->players->count() == 0 && $team->rerolls == 0 && $team->apotecario == 0)
                        <select class="form-control col-md-3 @error('raza') is-invalid @enderror" id="raza" name="raza">
                          @foreach ($razas as $raza)
                            <option value="{{ $raza->id }}" @if ($raza->id == $team->raza) selected="selected" @endif>{{ $raza->nombre }}</option>
                          @endforeach
                        </select>
                      @else
                        <span><strong>{{ $team->race->nombre }}</strong></span>
                        <input type="hidden" value="{{ $team->raza }}" id="raza" name="raza"/>
                      @endif
                      @error('raza')
                        <small class="field-validation-error form-text alert-danger" id="razaErrorMsg">{{ $message }}</small>
                      @enderror
                    </div>
                    <div class="form-group col-md-12">
                      <label for="presupuesto">Presupuesto inicial del equipo </label>
                      <input class="form-control @error('presupuesto') is-invalid @enderror @if ($team->activo == 1) readonly @endif" type="number" value="{{ $team->presupuesto }}" id="presupuesto" name="presupuesto" placeholder="Indica de cuánto dinero dispone el equipo para empezar"/>
                      @error('presupuesto')
                        <small class="field-validation-error form-text alert-danger" id="presupuestoErrorMsg">{{ $message }}</small>
                      @enderror
                    </div>
                    @if (Auth::user()->rol != 3)
                      <div class="form-group col-md-12">
                        <label for="tesoreria">Tesorería</label>
                        <input class="form-control @error('tesoreria') is-invalid @enderror" type="number" value="{{ $team->tesoreria }}" id="tesoreria" name="tesoreria"/>
                        @error('tesoreria')
                          <small class="field-validation-error form-text alert-danger" id="tesoreriaErrorMsg">{{ $message }}</small>
                        @enderror
                      </div>
                      <div class="form-group col-md-12">
                        <label for="banco">Banco</label>
                        <input class="form-control @error('banco') is-invalid @enderror" type="number" value="{{ $team->banco }}" id="banco" name="banco"/>
                        @error('banco')
                          <small class="field-validation-error form-text alert-danger" id="bancoErrorMsg">{{ $message }}</small>
                        @enderror
                      </div>
                      <div class="form-group col-md-12">
                        <label for="ff">Factor de hinchas</label>
                        <input class="form-control @error('ff') is-invalid @enderror" type="number" value="{{ $team->ff }}" id="ff" name="ff"/>
                        @error('ff')
                          <small class="field-validation-error form-text alert-danger" id="ffErrorMsg">{{ $message }}</small>
                        @enderror
                      </div>
                      <div class="form-group col-md-3 float-left">
                        <label for="jugados">Partidos jugados</label>
                        <input class="form-control @error('jugados') is-invalid @enderror" type="number" value="{{ $team->jugados }}" id="jugados" name="jugados"/>
                        @error('jugados')
                          <small class="field-validation-error form-text alert-danger" id="jugadosErrorMsg">{{ $message }}</small>
                        @enderror
                      </div>
                      <div class="form-group col-md-3 float-left">
                        <label for="ganados">Partidos ganados</label>
                        <input class="form-control @error('ganados') is-invalid @enderror" type="number" value="{{ $team->ganados }}" id="ganados" name="ganados"/>
                        @error('ganados')
                          <small class="field-validation-error form-text alert-danger" id="ganadosErrorMsg">{{ $message }}</small>
                        @enderror
                      </div>
                      <div class="form-group col-md-3 float-left">
                        <label for="perdidos">Partidos perdidos</label>
                        <input class="form-control @error('perdidos') is-invalid @enderror" type="number" value="{{ $team->perdidos }}" id="perdidos" name="perdidos"/>
                        @error('perdidos')
                          <small class="field-validation-error form-text alert-danger" id="perdidosErrorMsg">{{ $message }}</small>
                        @enderror
                      </div>
                      <div class="form-group col-md-3 float-left">
                        <label for="empatados">Partidos empatados</label>
                        <input class="form-control @error('empatados') is-invalid @enderror" type="number" value="{{ $team->empatados }}" id="empatados" name="empatados"/>
                        @error('empatados')
                          <small class="field-validation-error form-text alert-danger" id="empatadosErrorMsg">{{ $message }}</small>
                        @enderror
                      </div>
                      <div class="form-group col-md-6 float-left">
                        <label for="tdf">Touchdowns a favor</label>
                        <input class="form-control @error('tdf') is-invalid @enderror" type="number" value="{{ $team->tdf }}" id="tdf" name="tdf"/>
                        @error('tdf')
                          <small class="field-validation-error form-text alert-danger" id="tdfErrorMsg">{{ $message }}</small>
                        @enderror
                      </div>
                      <div class="form-group col-md-6 float-left">
                        <label for="tdc">Touchdowns en contra</label>
                        <input class="form-control @error('tdc') is-invalid @enderror" type="number" value="{{ $team->tdc }}" id="tdc" name="tdc"/>
                        @error('tdc')
                          <small class="field-validation-error form-text alert-danger" id="tdcErrorMsg">{{ $message }}</small>
                        @enderror
                      </div>
                      <div class="form-group col-md-3 float-left">
                        <label for="hf">Heridos a favor</label>
                        <input class="form-control @error('hf') is-invalid @enderror" type="number" value="{{ $team->hf }}" id="hf" name="hf"/>
                        @error('hf')
                          <small class="field-validation-error form-text alert-danger" id="hfErrorMsg">{{ $message }}</small>
                        @enderror
                      </div>
                      <div class="form-group col-md-3 float-left">
                        <label for="hc">Heridos en contra</label>
                        <input class="form-control @error('hc') is-invalid @enderror" type="number" value="{{ $team->hc }}" id="hc" name="hc"/>
                        @error('hc')
                          <small class="field-validation-error form-text alert-danger" id="hcErrorMsg">{{ $message }}</small>
                        @enderror
                      </div>
                      <div class="form-group col-md-3 float-left">
                        <label for="mf">Muertos a favor</label>
                        <input class="form-control @error('mf') is-invalid @enderror" type="number" value="{{ $team->mf }}" id="mf" name="mf"/>
                        @error('mf')
                          <small class="field-validation-error form-text alert-danger" id="mfErrorMsg">{{ $message }}</small>
                        @enderror
                      </div>
                      <div class="form-group col-md-3 float-left">
                        <label for="mc">Muertos en contra</label>
                        <input class="form-control @error('mc') is-invalid @enderror" type="number" value="{{ $team->mc }}" id="mc" name="mc"/>
                        @error('mc')
                          <small class="field-validation-error form-text alert-danger" id="mcErrorMsg">{{ $message }}</small>
                        @enderror
                      </div>
                      <div class="form-group col-md-3 float-left">
                        <label for="pases">Pases realizados</label>
                        <input class="form-control @error('pases') is-invalid @enderror" type="number" value="{{ $team->pases }}" id="pases" name="pases"/>
                        @error('pases')
                          <small class="field-validation-error form-text alert-danger" id="pasesErrorMsg">{{ $message }}</small>
                        @enderror
                      </div>
                      <div class="form-group col-md-3 float-left">
                        <label for="yardaspase">Yardas de pase</label>
                        <input class="form-control @error('yardaspase') is-invalid @enderror" type="number" value="{{ $team->yardaspase }}" id="yardaspase" name="yardaspase"/>
                        @error('yardaspase')
                          <small class="field-validation-error form-text alert-danger" id="yardaspaseErrorMsg">{{ $message }}</small>
                        @enderror
                      </div>
                      <div class="form-group col-md-3 float-left">
                        <label for="intercepciones">Interc. realizadas</label>
                        <input class="form-control @error('intercepciones') is-invalid @enderror" type="number" value="{{ $team->intercepciones }}" id="intercepciones" name="intercepciones"/>
                        @error('intercepciones')
                          <small class="field-validation-error form-text alert-danger" id="intercepcionesErrorMsg">{{ $message }}</small>
                        @enderror
                      </div>
                      <div class="form-group col-md-3 float-left">
                        <label for="intercepcionesc">Interc. sufridas</label>
                        <input class="form-control @error('intercepcionesc') is-invalid @enderror" type="number" value="{{ $team->intercepcionesc }}" id="intercepcionesc" name="intercepcionesc"/>
                        @error('intercepcionesc')
                          <small class="field-validation-error form-text alert-danger" id="intercepcionescErrorMsg">{{ $message }}</small>
                        @enderror
                      </div>
                    @endif
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
