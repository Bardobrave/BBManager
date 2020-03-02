<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\URL;
use Illuminate\Foundation\Http\FormRequest;

class validateUsers extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        //Para modificar cualquier usuario se debe ser administrador o el propio usuario
        if ($this->userRol == 1 || $this->userId == $this->idUsuario)
          return true;
        else
          return redirect('home');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

      //Se marcan unas reglas u otras dependiendo de la url de la petición
      //Si se trata de una petición de creación de usuario
      if (strpos(URL::current(), 'crear') != false) {
        return [
            'name' => 'required|unique:USUARIOS|max:255',
            'email' => 'required|email:rfc|unique:USUARIOS|max:255',
            'password' => 'required|min:8',
            'confirmPassword' => 'required|same:password',
            'rol' => 'required|numeric|min:1|max:3'
        ];
        //Si se trata de una petición de modificación de usuario
      } else if (strpos(URL::current(), 'editar') != false) {
        if ($this->userRol == 1) {
          return [
                'name' => 'required|unique:USUARIOS,name,'.$this->id.'|max:255',
                'email' => 'required|email:rfc|unique:USUARIOS,email,'.$this->id.'|max:255',
                'rol' => 'required|numeric|min:1|max:3'
          ];
        } else {
          return [
                'name' => 'required|unique:USUARIOS,name,'.$this->id.'|max:255',
                'email' => 'required|email:rfc|unique:USUARIOS,email,'.$this->id.'|max:255',
          ];
        }
        //En otro caso se trata de una modificación de contraseñas
      } else {
          return [
              'password' => 'required|min:8',
              'confirmPassword' => 'required|same:password'
          ];
      }
    }

   /**
   * Get the error messages for the defined validation rules.
   *
   * @return array
   */
    public function messages()
    {
        return [
            'name.required' => 'El nombre del usuario es obligatorio',
            'name.unique' => 'El nombre de usuario elegido ya existe en la base de datos',
            'name.max' => 'El nombre no puede superar los 255 caracteres',
            'email.required'  => 'El correo es obligatorio',
            'email.email' => 'Debe indicarse una dirección de correo válida',
            'email.unique' => 'El correo indicado ya existe en la base de datos',
            'email.max' => 'El correo no puede superar los 255 caracteres',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'confirmPassword.required' => 'Debe confirmarse la contraseña',
            'confirmPassword.same' => 'La confirmación no coincide con la contraseña',
            'rol.required' => 'Debe indicarse un rol para el usuario',
            'rol.numeric' => 'Código de rol incorrecto',
            'rol.min' => 'Código de rol incorrecto',
            'rol.max' => 'Código de rol incorrecto'
        ];
    }
}
