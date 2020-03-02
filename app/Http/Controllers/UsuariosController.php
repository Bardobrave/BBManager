<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\validateUsers;
use App\User;
use App\Rol;

class UsuariosController extends Controller
{

    //Variables con el rol y el id del usuario en sesión
    public $userRol;
    public $userId;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->middleware('auth');
        $this->middleware('auth.check');
    }

//Métodos de gestión AJAX------------------------------------------------------

    /**
     * Gestión de la llamada AJAX que controla si un email está repetido en bd
     *
     * @param email que se está comprobando
     * @param id del usuario que, de existir, se excluye de la búsqueda
     * @return boolean
     */
    public function checkSingleEmail($email, $id = null) {
      if ($id != null)
        $user = User::where('email', $email)->where('id', '<>', $id)->first();
      else
        $user = User::where('email', $email)->first();
      return (($user != null) ? 'true' : 'false');
    }

    /**
     * Gestión de la llamada AJAX para controlar si un nombre está repetido en base de datos
     *
     * @param nombre del usuario que hay que comprobar
     * @param id del usuario que, de existir, se excluye de la búsqueda
     * @return boolean
     */
    public function checkSingleName($nombre, $id = null) {
      if ($id != null)
        $user = User::where('name', $nombre)->where('id', '<>', $id)->first();
      else
        $user = User::where('name', $nombre)->first();
      return (($user != null) ? 'true' : 'false');
    }

//Fin de gestión de métodos AJAX-----------------------------------------------

//Gestión de métodos CRUD------------------------------------------------------

    /**
     * Mostrar una lista de usuarios.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function list(Request $request)
    {
        //Sólo los administradores pueden ver listados de usuarios
        if ($this->userRol == 1) {

          /*Dado que el controlador de listas es un posible punto de retorno,
            se almacena en sesión como última página visitada */
          session(['lastVisited' => '/usuarios/lista']);

          return view('usuarios/lista', User::listAll($request));
        } else {
          return redirect('usuarios/detail/'.$this->userId);
        }
    }

    /**
     * Mostrar el detalle de un usuario.
     *
     * @param id del usuario cuyo detalle se va a mostrar
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function detail($id)
    {
        //Primero se carga el usuario
        $user = User::find($id);

        /* Si el usuario que solicita el detalle es administrador o el propio
           usuario en sesión, y el usuario se ha encontrado se le lleva al
           detalle, si no se le redirige al home*/
        if (($this->userRol == 1 || $this->userId == $id) && $user !== null) {

          /*Se actualiza el valor de sesión de última página visitada*/
          session(['lastVisited' => '/usuarios/detalle/'.$id]);

          return view('usuarios/detalle', ["user" => $user]);
        } else {
          return redirect('home');
        }
    }

    /**
     * Dirigir al formulario de edición de un usuario
     *
     * @param id del usuario que se va a editar
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function edit($id) {

        //Se carga el usuario
        $user = User::find($id);

        /*Si solicita la edición el administrador o el propio usuario que se
          intenta editar, y dicho usuario existe se redirige al formulario
          si no se le saca al home */
        if (($this->userRol == 1 || $this->userId == $id) && $user != null) {
          return view('usuarios/editar', ["user" => $user, "roles" => Rol::all()]);
        } else {
          return redirect('home');
        }
    }

    /**
     * Grabar los cambios realizados sobre un usuario
     *
     * @param id del usuario que se va a editar
     * @param request con los datos enviados por el formulario
     * @return \Illuminate\Contracts\Support\Renderable
    */
    public function saveEdit($id, validateUsers $request) {

        //El Request se encarga de comprobar las autorizaciones
        //Aquí tan sólo se obtiene el usuario que se está editando
        $user = User::find($id);

        //Si el usuario no existiera se redirigiría al home
        if ($user == null)
          return redirect('home');

        //Se llama al método que actualiza sus datos
        $user->edit($this->userRol, $request);

        //Y se redirige a la página de detalle del usuario actualizado
        return redirect('usuarios/detalle/'.$user->id);
    }

    /**
     * Crear un usuario nuevo
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function create() {

       //Sólo el administrador puede crear nuevos usuarios
       if ($this->userRol == 1) {
         return view('usuarios/crear', [ "roles" => Rol::all()]);
       } else {
         return redirect('home');
       }
    }

    /**
     * Grabar un usuario nuevo
     *
     * @param request con los datos del formulario
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function saveCreate(validateUsers $request) {

        $user = new User;
        $id = $user->crear($request);

        //Se redirige al detalle del nuevo usuario
        return redirect('usuarios/detalle/'.$id);
    }

    /**
     * Mostrar la páina de confirmación de borrado de un usuario
     *
     * @param id del usuario que se quiere eliminar
     * @return \Illuminate\Contracts\Support\Renderable
    */
    public function delete($id){

      //Se comprueba que el usuario existe
      $user = User::find($id);

      //Sólo el administrador puede eliminar usuaios, que deben existir
      if ($this->userRol == 1 && $user != null) {
        return view('usuarios/borrar', [ "user" => $user ]);
      } else {
        return redirect('home');
      }
    }

    /**
     * Eliminar un usuario
     *
     * @param id del usuario que se va a eliminar
     * @return \Illuminate\Contracts\Support\Renderable
    */
    public function saveDelete($id){

      //Se comprueba que el usuario existe
      $user = User::find($id);

      //Sólo el administrador puede eliminar usuarios
      if ($this->userRol == 1 && $user != null) {
        $user->delete();

        //Se redirige al listado de usuarios
        return redirect('usuarios/lista');
      } else {
        return redirect('home');
      }
    }

//Fin de métodos de gestión CRUD-----------------------------------------------

//Resto de métodos de gestión--------------------------------------------------

    /**
     * Activar un usuario
     *
     * @param id del usuario que se va a activar
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function activate($id) {

        //Se obtiene el usuario
        $user = User::find($id);

       /*Si el usuario en sesión es administrador y el usuario que se pretende
         activar existe se activa */
       if ($this->userRol == 1 && $user != null)
           $user->activar();

       return redirect()->back();
    }

    /**
     * Dirigir al formulario de cambio de contraseña de un usuario
     *
     * @param id del usuario que se va a editar
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function changePass($id) {

        //Se carga el usuario
        $user = User::find($id);

        /*Para cambiar la clave de un usuario debe ser administrador o el
          propio usuario que se intenta editar, y el usuario debe exiistir */
        if (($this->userRol == 1 || $this->userId == $id) && $user != null) {
          return view('usuarios/cambiarPass', ["user" => $user]);
        } else {
          return redirect('home');
        }
    }

    /**
     * Desactivar un usuario
     *
     * @param id del usuario que se va a desactivar
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function deactivate($id) {

        //Se obtiene el usuario
        $user = User::find($id);

        /*Si el usuario en sesión es administrador y el usuario que se pretende
          desactivar existe, se desactiva */
        if ($this->userRol == 1 && $user != null)
            $user->desactivar();

        return redirect()->back();
    }

    /**
     * Grabar el cambio de contraseña del usuario
     *
     * @param id del usuario cuya contraseña se cambia
     * @param request con los datos enviados por el formulario
     * @return \Illuminate\Contracts\Support\Renderable
    */
    public function saveChangePass($id, validateUsers $request) {

        $user = User::find($id);

        //Si el usuario no existe se redirige al home
        if ($user == null)
          return redirect('home');

        $user->cambiarPass($request);

        //Redirigir a la página de detalle del usuario actualizado
        return redirect('usuarios/detalle/'.$user->id);
    }
}
