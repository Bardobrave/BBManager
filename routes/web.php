<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

//Rutas para la gestión de USUARIOS
Route::get('/usuarios', function (){
  $user = Auth::user();
  if ($user->rol == 1) {
    return redirect('/usuarios/lista');
  } else {
    return redirect('/usuarios/detalle/' . $user->id);
  }
})->name('usuarios');

Route::get('/usuarios/crear', 'UsuariosController@create');
Route::post('/usuarios/crear', 'UsuariosController@saveCreate');
Route::get('/usuarios/lista', 'UsuariosController@list');
Route::post('/usuarios/lista', 'UsuariosController@list');
Route::get('/usuarios/detalle/{id}', 'UsuariosController@detail');
Route::get('/usuarios/editar/{id}', 'UsuariosController@edit');
Route::post('/usuarios/editar/{id}', 'UsuariosController@saveEdit');
Route::get('/usuarios/cambiarPass/{id}', 'UsuariosController@changePass');
Route::post('/usuarios/cambiarPass/{id}', 'UsuariosController@saveChangePass');
Route::get('/usuarios/activar/{id}', 'UsuariosController@activate');
Route::get('/usuarios/desactivar/{id}', 'UsuariosController@deactivate');
Route::get('/usuarios/eliminar/{id}', 'UsuariosController@delete');
Route::post('/usuarios/eliminar/{id}', 'UsuariosController@saveDelete');
Route::get('/usuarios/checkNombreRepetido/{nombre}/{id?}', 'UsuariosController@checkSingleName');
Route::get('/usuarios/checkEmailRepetido/{email}/{id?}', 'UsuariosController@checkSingleEmail');

//Rutas para la gestión de EQUIPOS
Route::get('/equipos', function (){
  $user = Auth::user();
  if ($user->rol != 3) {
    return redirect('/equipos/lista');
  } else {
    return redirect('/equipos/listaPorUsuario/' . $user->id);
  }
})->name('equipos');

Route::get('/equipos/checkNombreRepetido/{nombre}/{id?}', 'EquiposController@checkSingleName');
Route::get('/equipos/checkNombreJugadorRepetido/{nombre}/{idEquipo}/{idJugador?}', 'EquiposController@checkSinglePlayer');
Route::get('/equipos/crear', 'EquiposController@create');
Route::post('/equipos/crear', 'EquiposController@saveCreate');
Route::get('/equipos/lista', 'EquiposController@list');
Route::post('/equipos/lista', 'EquiposController@list');
Route::get('/equipos/listaPorUsuario/{id}', 'EquiposController@listByUser');
Route::get('/equipos/detalle/{id}', 'EquiposController@detail');
Route::get('/equipos/editar/{id}', 'EquiposController@edit');
Route::post('/equipos/editar/{id}', 'EquiposController@saveEdit');
Route::get('/equipos/eliminar/{id}', 'EquiposController@delete');
Route::post('/equipos/eliminar/{id}', 'EquiposController@saveDelete');
Route::post('/equipos/comprarJugador/{id}', 'EquiposController@addPlayer');
Route::get('/equipos/despedir/{id}', 'EquiposController@firePlayer');
Route::get('/equipos/gestionaElementos/{id}/{element}/{add}', 'EquiposController@manageElements');
Route::get('/equipos/activar/{id}', 'EquiposController@activate');
Route::get('/equipos/preparar/{id}', 'EquiposController@prepare');

//Rutas para la gestión de JUGADORES
Route::get('/jugadores/editar/{id}', 'JugadoresController@edit');
Route::post('/jugadores/editar/{id}', 'JugadoresController@saveEdit');
Route::get('/jugadores/subida/{id}/{subida}', 'JugadoresController@uplevel');
Route::get('/jugadores/getSkills/{id}/{subida}', 'JugadoresController@getSkills');

//Rutas para la gestión de LIGAS
Route::get('/ligas/checkNombreRepetido/{nombre}/{id?}', 'LigasController@checkSingleName');
Route::get('/ligas', 'LigasController@list')->name('ligas');
Route::get('/ligas/lista', 'LigasController@list');
Route::get('/ligas/detalle/{id}', 'LigasController@detail');
Route::get('/ligas/crear', 'LigasController@create');
Route::post('/ligas/crear', 'LigasController@saveCreate');
Route::get('/ligas/editar/{id}', 'LigasController@edit');
Route::post('/ligas/editar/{id}', 'LigasController@saveEdit');
Route::get('/ligas/eliminar/{id}', 'LigasController@delete');
Route::post('/ligas/eliminar/{id}', 'LigasController@saveDelete');
Route::get('/ligas/aplicar/{id}', 'LigasController@apply');
Route::post('/ligas/aplicar/{id}', 'LigasController@saveApply');
Route::get('/ligas/gestionarInscripcion/{result}/{id}', 'LigasController@manageInscription');
Route::get('/ligas/periodoInscripciones/{operation}/{id}', 'LigasController@inscriptionPhase');
Route::get('/ligas/asignarGrupos/{id}', 'LigasController@manageGroups');
Route::post('/ligas/asignarGrupos/{id}', 'LigasController@manageGroupsSave');
Route::get('/ligas/iniciar/{id}', 'LigasController@start');
Route::get('/ligas/finalizar/{id}', 'LigasController@finish');

//Rutas para la gestión de jornadas
Route::get('/jornadas/crear/{id}', 'JornadasController@create');
Route::post('/jornadas/crear/{id}', 'JornadasController@saveCreate');
Route::get('/jornadas/editar/{id}', 'JornadasController@edit');
Route::post('/jornadas/editar/{id}', 'JornadasController@saveEdit');
Route::get('/jornadas/eliminar/{id}', 'JornadasController@delete');
Route::post('/jornadas/eliminar/{id}', 'JornadasController@saveDelete');

//Rutas para la gestión de actas
Route::get('/actas/crear/{id}', 'ActasController@create');
Route::get('/actas/editar/{id}', 'ActasController@edit');
Route::post('/actas/editar/{id}', 'ActasController@saveEdit');
Route::get('/actas/detalle/{id}', 'ActasController@detail');
Route::get('/actas/reabrir/{id}', 'ActasController@reopen');
