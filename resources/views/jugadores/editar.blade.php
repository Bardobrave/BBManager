@extends('layouts.app')

@push('scripts')
  <script src="{{ asset('js/plugins/validatorPlugin.js') }}" type="text/javascript" defer></script>
  <script src="{{ asset('js/validators/jugadores.js') }}" type="text/javascript" defer></script>
  <script src="{{ asset('js/manageSkills.js') }}" type="text/javascript" defer></script>
@endpush

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Editar jugador</div>

                <div class="card-body">
                  @if ($errors->any())
                    <div class="alert alert-danger">
                      Hay errores en los datos enviados a través del formulario
                    </div>
                  @endif
                  <form id="playerForm" method="post" action="{{ url('/jugadores/editar/'.$player->id) }}">
                    @csrf
                    <input type="hidden" value="{{ $player->equipo }}" id="idEquipo" name="idEquipo" />
                    <input type="hidden" value="{{ $player->id }}" id="idJugador" name="idJugador" />
                    <div class="form-group col-md-4 float-left">
                      <label for="numero">Número</label>
                      <select class="form-control @error('numero') is-invalid @enderror" id="numero" name="numero">
                        @for ($x = 1; $x <= 16; $x++)
                          @if ($player->numero == $x || $player->team->players->where('numero', $x)->where('activo', 1)->count() == 0)
                            <option value="{{ $x }}" @if ($player->numero == $x) selected="selected" @endif>{{ $x }}</option>
                          @endif
                        @endfor
                      </select>
                      @error('numero')
                        <small class="field-validation-error form-text alert-danger" id="numeroErrorMsg">{{ $message }}</small>
                      @enderror
                    </div>
                    <div class="form-group col-md-4 float-left">
                      <label for="nombre">Nombre</label>
                      <input class="form-control @error('nombre') is-invalid @enderror" type="text" value="{{ $player->nombre }}" id="nombre" name="nombre" placeholder="Nombre del jugador"/>
                      @error('nombre')
                        <small class="field-validation-error form-text alert-danger" id="nombreErrorMsg">{{ $message }}</small>
                      @enderror
                    </div>
                    <div class="form-group col-md-4 float-left">
                      <label for="posicion">Posicion</label>
                      <select class="form-control @error('posicion') is-invalid @enderror" id="posicion" name="posicion">
                        @foreach ($player->team->race->positionals as $positional)
                          @if ($positional->id == $player->posicion
                            || $player->team->players->where('posicion', $positional->id)->where('activo', 1)->count() < $positional->maximo)
                            <option value="{{ $positional->id }}" @if ($positional->id == $player->posicion) selected="selected" @endif>{{ $positional->nombre }}</option>
                          @endif
                        @endforeach
                      </select>
                      @error('posicion')
                        <small class="field-validation-error form-text alert-danger" id="posicionErrorMsg">{{ $message }}</small>
                      @enderror
                    </div>
                    <div class="form-group col-md-3 float-left">
                      <label for="ma">Movimiento </label>
                      <input class="form-control @error('ma') is-invalid @enderror" type="number" value="{{ $player->ma }}" id="ma" name="ma"/>
                      @error('ma')
                        <small class="field-validation-error form-text alert-danger" id="maErrorMsg">{{ $message }}</small>
                      @enderror
                    </div>
                    <div class="form-group col-md-3 float-left">
                      <label for="fue">Fuerza</label>
                      <input class="form-control @error('fue') is-invalid @enderror" type="number" value="{{ $player->fue }}" id="fue" name="fue"/>
                      @error('fue')
                        <small class="field-validation-error form-text alert-danger" id="fueErrorMsg">{{ $message }}</small>
                      @enderror
                    </div>
                    <div class="form-group col-md-3 float-left">
                      <label for="agl">Agilidad</label>
                      <input class="form-control @error('agl') is-invalid @enderror" type="number" value="{{ $player->agl }}" id="agl" name="agl"/>
                      @error('agl')
                        <small class="field-validation-error form-text alert-danger" id="aglErrorMsg">{{ $message }}</small>
                      @enderror
                    </div>
                    <div class="form-group col-md-3 float-left">
                      <label for="av">Armadura</label>
                      <input class="form-control @error('av') is-invalid @enderror" type="number" value="{{ $player->av }}" id="av" name="av"/>
                      @error('av')
                        <small class="field-validation-error form-text alert-danger" id="avErrorMsg">{{ $message }}</small>
                      @enderror
                    </div>
                    <div class="form-group col-md-12 float-left">
                      <label for="skills">Habilidades <span class="fa fa-plus activo" id="addSkill" data-toggle="modal" data-target="#skillListModal"> </span></label>
                      <input type="hidden" value="{{ $player->skills->toJson() }}" id="skills" name="skills" />
                      <div class="" id="skillContainer">
                        @foreach ($player->skills as $skill)
                          <span class="badge badge-primary skillBean activo" id="skill_{{ $skill->id }}" title="{{ $skill->nombre }}">{{ $skill->nombre_corto }}</span>&nbsp;
                        @endforeach
                      </div>
                    </div>
                    <div class="form-group col-md-6 float-left">
                      <label for="px">Experiencia</label>
                      <input class="form-control @error('px') is-invalid @enderror" type="number" value="{{ $player->px }}" id="px" name="px"/>
                      @error('px')
                        <small class="field-validation-error form-text alert-danger" id="pxErrorMsg">{{ $message }}</small>
                      @enderror
                    </div>
                    <div class="form-group col-md-6 float-left">
                      <label for="precio">Precio</label>
                      <input class="form-control @error('precio') is-invalid @enderror" type="number" value="{{ $player->precio }}" id="precio" name="precio"/>
                      @error('precio')
                        <small class="field-validation-error form-text alert-danger" id="precioErrorMsg">{{ $message }}</small>
                      @enderror
                    </div>
                    <div class="form-group col-md-4 float-left">
                      <label for="hf">Heridos causados</label>
                      <input class="form-control @error('hf') is-invalid @enderror" type="number" value="{{ $player->hf }}" id="hf" name="hf"/>
                      @error('hf')
                        <small class="field-validation-error form-text alert-danger" id="hfErrorMsg">{{ $message }}</small>
                      @enderror
                    </div>
                    <div class="form-group col-md-4 float-left">
                      <label for="mf">Muertos causados</label>
                      <input class="form-control @error('mf') is-invalid @enderror" type="number" value="{{ $player->mf }}" id="mf" name="mf"/>
                      @error('mf')
                        <small class="field-validation-error form-text alert-danger" id="mfErrorMsg">{{ $message }}</small>
                      @enderror
                    </div>
                    <div class="form-group col-md-4 float-left">
                      <label for="hc">Heridas recibidas en contra</label>
                      <input class="form-control @error('hc') is-invalid @enderror" type="number" value="{{ $player->hc }}" id="hc" name="hc"/>
                      @error('av')
                        <small class="field-validation-error form-text alert-danger" id="hcErrorMsg">{{ $message }}</small>
                      @enderror
                    </div>
                    <div class="form-group col-md-6 float-left">
                      <label for="curado">Veces que ha sido curado por el apotecario</label>
                      <input class="form-control @error('curado') is-invalid @enderror" type="number" value="{{ $player->curado }}" id="curado" name="curado"/>
                      @error('curado')
                        <small class="field-validation-error form-text alert-danger" id="curadoErrorMsg">{{ $message }}</small>
                      @enderror
                    </div>
                    <div class="form-group col-md-6 float-left">
                      <label for="niggling">Heridas incapacitantes</label>
                      <input class="form-control @error('niggling') is-invalid @enderror" type="number" value="{{ $player->niggling }}" id="niggling" name="niggling"/>
                      @error('niggling')
                        <small class="field-validation-error form-text alert-danger" id="nigglingErrorMsg">{{ $message }}</small>
                      @enderror
                    </div>
                    <div class="form-group col-md-6 float-left">
                      <label for="pases">Pases realizados</label>
                      <input class="form-control @error('pases') is-invalid @enderror" type="number" value="{{ $player->pases }}" id="pases" name="pases"/>
                      @error('pases')
                        <small class="field-validation-error form-text alert-danger" id="pasesErrorMsg">{{ $message }}</small>
                      @enderror
                    </div>
                    <div class="form-group col-md-6 float-left">
                      <label for="yardaspase">Yardas de pase</label>
                      <input class="form-control @error('yardaspase') is-invalid @enderror" type="number" value="{{ $player->yardaspase }}" id="yardaspase" name="yardaspase"/>
                      @error('yardaspase')
                        <small class="field-validation-error form-text alert-danger" id="yardaspaseErrorMsg">{{ $message }}</small>
                      @enderror
                    </div>
                    <div class="form-group col-md-6 float-left">
                      <label for="intercepciones">Intercepciones realizadas</label>
                      <input class="form-control @error('intercepciones') is-invalid @enderror" type="number" value="{{ $player->intercepciones }}" id="intercepciones" name="intercepciones"/>
                      @error('intercepciones')
                        <small class="field-validation-error form-text alert-danger" id="intercepcionesErrorMsg">{{ $message }}</small>
                      @enderror
                    </div>
                    <div class="form-group col-md-6 float-left">
                      <label for="td">Touchdowns anotados</label>
                      <input class="form-control @error('td') is-invalid @enderror" type="number" value="{{ $player->td }}" id="td" name="td"/>
                      @error('td')
                        <small class="field-validation-error form-text alert-danger" id="tdErrorMsg">{{ $message }}</small>
                      @enderror
                    </div>
                    <div class="form-group col-md-6 float-left">
                      <label for="jugados">Partidos jugados</label>
                      <input class="form-control @error('jugados') is-invalid @enderror" type="number" value="{{ $player->jugados }}" id="jugados" name="jugados"/>
                      @error('jugados')
                        <small class="field-validation-error form-text alert-danger" id="jugadosErrorMsg">{{ $message }}</small>
                      @enderror
                    </div>
                    <div class="form-group col-md-6 float-left">
                      <label for="mvp">Veces galardonado con el MVP</label>
                      <input class="form-control @error('mvp') is-invalid @enderror" type="number" value="{{ $player->mvp }}" id="mvp" name="mvp"/>
                      @error('mvp')
                        <small class="field-validation-error form-text alert-danger" id="mvpErrorMsg">{{ $message }}</small>
                      @enderror
                    </div>
                    <div class="form-group col-md-4 float-left">
                      <label for="lesionado">Lesionado</label>
                      <input class="form-control @error('av') is-invalid @enderror" type="checkbox" @if($player->lesionado) checked="checked" @endif value="1" id="lesionado" name="lesionado"/>
                      @error('lesionado')
                        <small class="field-validation-error form-text alert-danger" id="lesionadoErrorMsg">{{ $message }}</small>
                      @enderror
                    </div>
                    <div class="form-group col-md-4 float-left">
                      <label for="activo">Jugador en activo</label>
                      <input class="form-control @error('activo') is-invalid @enderror" type="checkbox" @if($player->activo) checked="checked" @endif value="1" id="activo" name="activo"/>
                      @error('activo')
                        <small class="field-validation-error form-text alert-danger" id="activoErrorMsg">{{ $message }}</small>
                      @enderror
                    </div>
                    <div class="form-group col-md-4 float-left">
                      <label for="muerto">Jugador fallecido</label>
                      <input class="form-control @error('muerto') is-invalid @enderror" type="checkbox" @if($player->muerto) checked="checked" @endif value="1" id="muerto" name="muerto"/>
                      @error('muerto')
                        <small class="field-validation-error form-text alert-danger" id="muertoErrorMsg">{{ $message }}</small>
                      @enderror
                    </div>
                    <div class="form-group float-right">
                      <span id="grabar" class="btn btn-primary">Enviar</span>
                      <a href="{{ url(session('lastVisited')) }}" class="btn btn-danger">Cancelar</a>
                    </div>
                  </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="skillListModal" class="modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Añadir habilidad</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div id="skillListContainer" class="modal-body">
        @foreach ($skills as $skill)
          @if ($player->skills->where('id', $skill->id)->count() == 0)
            <span class="badge badge-primary skillPossibleBean activo" id="skill_{{ $skill->id }}" title="{{ $skill->nombre }}">{{ $skill->nombre_corto }}</span>&nbsp;
          @endif
        @endforeach
      </div>
    </div>
  </div>
</div>
@endsection
