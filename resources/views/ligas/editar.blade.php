@extends('layouts.app')

@push('scripts')
  <script src="{{ asset('js/plugins/validatorPlugin.js') }}" type="text/javascript" defer></script>
  <script src="{{ asset('js/validators/ligas.js') }}" type="text/javascript" defer></script>
@endpush

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Nueva liga</div>

                <div class="card-body">
                  @if ($errors->any())
                    <div class="alert alert-danger">
                      Hay errores en los datos enviados a través del formulario
                    </div>
                  @endif
                  <form method="post" action="{{ url('/ligas/editar/'.$league->id) }}">
                    @csrf
                    <input type="hidden" value="{{ $league->id }}" id="idLiga" name="idLiga" />
                    <div class="form-group">
                      <label for="nombre">Nombre</label>
                      <input class="form-control @error('nombre') is-invalid @enderror" type="text" value="{{ $league->nombre }}" id="nombre" name="nombre" placeholder="Nombre de la liga"/>
                      @error('nombre')
                        <small class="field-validation-error form-text alert-danger" id="nombreErrorMsg">{{ $message }}</small>
                      @enderror
                    </div>
                    <div class="form-group">
                      <label for="descripcion">Descripción de la liga </label>
                      <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion" placeholder="Explica a los jugadores las reglas a aplicar durante la liga">{{ $league->descripcion }}</textarea>
                      @error('descripcion')
                        <small class="field-validation-error form-text alert-danger" id="descripcionErrorMsg">{{ $message }}</small>
                      @enderror
                    </div>
                    <div class="form-group">
                      <label for="numgrupos">Número de grupos</label>
                      <input class="form-control @error('numgrupos') is-invalid @enderror" type="number" value="{{ $league->numgrupos }}" id="numgrupos" name="numgrupos" placeholder="Grupos que conformarán la liga"/>
                      @error('numgrupos')
                        <small class="field-validation-error form-text alert-danger" id="numgruposErrorMsg">{{ $message }}</small>
                      @enderror
                    </div>
                    @if($league->leagueTeams->count() == 0)
                    <div class="form-group">
                      <label for="maximopresupuesto">Máximo presupuesto equipos nuevos</label>
                      <input class="form-control @error('maximopresupuesto') is-invalid @enderror" type="number" value="{{ $league->maximopresupuesto }}" id="maximopresupuesto" name="maximopresupuesto" placeholder="Máximo presupuesto inicial de los equipos que se apuntan"/>
                      @error('maximopresupuesto')
                        <small class="field-validation-error form-text alert-danger" id="maximopresupuestoErrorMsg">{{ $message }}</small>
                      @enderror
                    </div>
                      <div class="form-group col-md-12 float-left">
                        <label for="activo">Liga de novatos</label>
                        <input class="form-control @error('liganovatos') is-invalid @enderror" type="checkbox" @if($league->liganovatos) checked="checked" @endif value="1" id="liganovatos" name="liganovatos"/>
                        @error('liganovatos')
                          <small class="field-validation-error form-text alert-danger" id="liganovatosErrorMsg">{{ $message }}</small>
                        @enderror
                      </div>
                    @else
                      <input type="hidden" id="maximopresupuesto" name="maximopresupuesto" value="{{ $league->maximopresupuesto }}" />
                    @endif
                    @if(!$league->iniciada)
                      <div class="form-group col-md-12 float-left">
                        <label for="cruzargrupos">Se van a cruzar los grupos</label>
                        <input class="form-control @error('cruzargrupos') is-invalid @enderror" type="checkbox" @if($league->cruzargrupos) checked="checked" @endif value="1" id="cruzargrupos" name="cruzargrupos"/>
                        @error('cruzargrupos')
                          <small class="field-validation-error form-text alert-danger" id="cruzargruposErrorMsg">{{ $message }}</small>
                        @enderror
                      </div>
                      <div class="form-group col-md-12 float-left">
                        <label for="idavueltagrupo">Partidos de ida y vuelta en el grupo</label>
                        <input class="form-control @error('idavueltagrupo') is-invalid @enderror" type="checkbox" @if($league->idavueltagrupo) checked="checked" @endif value="1" id="idavueltagrupo" name="idavueltagrupo"/>
                        @error('idavueltagrupo')
                          <small class="field-validation-error form-text alert-danger" id="idavueltagrupoErrorMsg">{{ $message }}</small>
                        @enderror
                      </div>
                      <div class="form-group col-md-12 float-left">
                        <label for="idavueltatodos">Partidos de ida y vuelta fuera del grupo</label>
                        <input class="form-control @error('idavueltatodos') is-invalid @enderror" type="checkbox" @if($league->idavueltatodos) checked="checked" @endif value="1" id="idavueltatodos" name="idavueltatodos"/>
                        @error('idavueltatodos')
                          <small class="field-validation-error form-text alert-danger" id="idavueltatodosErrorMsg">{{ $message }}</small>
                        @enderror
                      </div>
                      <div class="form-group col-md-12 float-left">
                        <label for="jornadadescanso">Jornada de descanso</label>
                        <input class="form-control @error('jornadadescanso') is-invalid @enderror" type="checkbox" @if($league->jornadadescanso) checked="checked" @endif value="1" id="jornadadescanso" name="jornadadescanso"/>
                        @error('jornadadescanso')
                          <small class="field-validation-error form-text alert-danger" id="jornadadescansoErrorMsg">{{ $message }}</small>
                        @enderror
                      </div>
                      <div class="form-group col-md-4 float-left">
                        <label for="puntosvictoria">Puntos por victoria</label>
                        <input class="form-control @error('puntosvictoria') is-invalid @enderror" type="number" value="{{ $league->puntosvictoria }}" id="puntosvictoria" name="puntosvictoria" />
                        @error('puntosvictoria')
                          <small class="field-validation-error form-text alert-danger" id="puntosvictoriaErrorMsg">{{ $message }}</small>
                        @enderror
                      </div>
                      <div class="form-group col-md-4 float-left">
                        <label for="puntosempate">Puntos por empate</label>
                        <input class="form-control @error('puntosempate') is-invalid @enderror" type="number" value="{{ $league->puntosempate }}" id="puntosempate" name="puntosempate" />
                        @error('puntosempate')
                          <small class="field-validation-error form-text alert-danger" id="puntosempateErrorMsg">{{ $message }}</small>
                        @enderror
                      </div>
                      <div class="form-group col-md-4 float-left">
                        <label for="puntosderrota">Puntos por derrota</label>
                        <input class="form-control @error('puntosderrota') is-invalid @enderror" type="number" value="{{ $league->puntosderrota }}" id="puntosderrota" name="puntosderrota" />
                        @error('puntosderrota')
                          <small class="field-validation-error form-text alert-danger" id="puntosderrotaErrorMsg">{{ $message }}</small>
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
