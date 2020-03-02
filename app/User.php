<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request as listaRequest;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $table = 'USUARIOS';

    /**
     * Definición de la relación con el rol de un usuario
     */
    public function roleName() {
      return $this->belongsTo('App\Rol', 'rol');
    }

    /**
     * Definición de la relación con los equipos del usuario
     */
    public function teams() {
      return $this->hasMany('App\Team', 'usuario');
    }

    /**
     * Método para listar todos los usuarios
     *
     * @param request con las características de la búsqueda (ordenacion, paginado)
     * @return array con los datos que requerirá la vista para constuir el listado
     */
    public static function listAll(listaRequest $request) {
      $sort = $request->input('sort');
      $ascdesc = $request->input('ascdesc');
      $page = $request->input('page');
      if ($sort != null)
        $users = User::orderBy($sort, $ascdesc)->paginate(10);
      else
        $users = User::paginate(10);
      return ["users" => $users, "sort" => $sort,
        "ascdesc" => $ascdesc, "page" => ($page == null) ? 1 : $page];
    }

    /**
     * Método para modificar los datos de un usuarios
     *
     * @param userRol con el rol del usuario en sesión
     * @param request con los datos pasados por el formulario
     */
    public function edit($userRol, $request) {
      $this->name = $request->input('name');
      $this->email = $request->input('email');
      //Si el usuario en sesión es administrador puede cambiar el rol
      if ($userRol == 1)
        $this->rol = $request->input('rol');

      //Grabar los cambios
      $this->save();
    }

    /**
     * Método para actualizar la contraseña del usuario
     *
     * @param request con los datos pasados por el formulario
     */
    public function cambiarPass($request) {

      $this->password = Hash::make($request->input('password'));

      //Grabar los cambios
      $this->save();
    }

    /**
     * Método para rellenar la información de un usuario nuevo
     *
     * @param request con los datos pasados por el formulario
     * @return numeric
     */
     public function crear($request) {
       $this->name = $request->input('name');
       $this->email = $request->input('email');
       $this->password = Hash::make($request->input('password'));
       $this->rol = $request->input('rol');

       //Se persiste el usuario nuevo en base de datos
       $this->save();

       //Se retorna el id del usuario recién creado
       return User::where('email', $request->input('email'))->first()->id;
     }

     /**
      * Método para activar un usuario
      *
      */
    function activar() {

      if ($this->activo == 0) {
          $this->activo = 1;
          $this->save();
      }
    }

    /**
     * Método para desactivar un usuario
     *
     */
   function desactivar() {

     if ($this->activo == 1) {
         $this->activo = 0;
         $this->save();
     }
   }

}
