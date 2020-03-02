@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Borrado del usuario</div>

                <div class="card-body">
                  <form method="post" action="{{ url('/usuarios/eliminar/'.$user->id) }}">
                    @csrf
                    <div class="form-group">
                      <label for="confirm">Has solicitado eliminar al usuario <strong>{{ $user->name }}</strong>, esta operación no es reversible.
                        ¿Estás seguro de que quieres llevarla a cabo?</label>
                    </div>
                    <div class="form-group float-right">
                      <button type="submit" class="btn btn-danger">Confirmar</button>
                      <a href="{{ url(session('lastVisited')) }}" class="btn btn-success">Cancelar</a>
                    </div>
                  </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
