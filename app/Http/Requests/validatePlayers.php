<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\URL;
use Illuminate\Foundation\Http\FormRequest;

class validatePlayers extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        //Para editar cualquier jugador se debe ser administrador o comisionado
        if ($this->user()->rol != 3)
          return true;

        return redirect('home');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

      return [
          'numero' => 'required|numeric|min:1|max:16',
          'nombre' => 'required|max:255',
          'posicion' => 'required|numeric|min:1|max:106',
          'ma' => 'required|numeric|min:1|max:11',
          'fue' => 'required|numeric|min:1|max:10',
          'agl' => 'required|numeric|min:1|max:10',
          'av' => 'required|numeric|min:1|max:12',
          'px' => 'required|numeric|min:0|max:275',
          'precio' => 'required|numeric|min:40000|max:1000000',
          'hf' => 'required|numeric|min:0|max:300',
          'mf' => 'required|numeric|min:0|max:300',
          'hc' => 'required|numeric|min:0|max:300',
          'curado' => 'required|numeric|min:0|max:300',
          'pases' => 'required|numeric|min:0|max:1000',
          'yardaspase' => 'required|numeric|min:0|max:10000',
          'intercepciones' => 'required|numeric|min:0|max:300',
          'td' => 'required|numeric|min:0|max:300',
          'jugados' => 'required|numeric|min:0|max:300',
          'mvp' => 'required|numeric|min:0|max:300',
          'niggling' => 'required|numeric|min:0|max:10'
      ];
    }

   /**
   * Get the error messages for the defined validation rules.
   *
   * @return array
   */
    public function messages()
    {
        return [
          'numero.required' => 'Debe indicarse el número del jugador',
          'numero.numeric' => 'Debe indicarse un número válido',
          'numero.min' => 'Debe indicarse un número válido',
          'numero.max' => 'Debe indicarse un número válido',
          'nombre.required' => 'El nombre del jugador es obligatorio',
          'nombre.max' => 'El nombre del jugador no puede superar los 256 caracteres',
          'posicion.required' => 'Debe indicarse la posición del jugador',
          'posicion.numeric' => 'Debe indicarse una posición válida',
          'posicion.min' => 'Debe indicarse una posición válida',
          'posicion.max' => 'Debe indicarse una posición válida',
          'ma.required' => 'Debe indicarse el movimiento del jugador',
          'ma.numeric' => 'El movimiento debe ser un valor numérico válido',
          'ma.min' => 'El movimiento debe ser un valor numérico entre 1 y 11',
          'ma.max' => 'El movimiento debe ser un valor numérico entre 1 y 11',
          'fue.required' => 'Debe indicarse la fuerza del jugador',
          'fue.numeric' => 'La fuerza debe ser un valor numérico válido',
          'fue.min' => 'La fuerza debe ser un valor numérico entre 1 y 10',
          'fue.max' => 'La fuerza debe ser un valor numérico entre 1 y 10',
          'agl.required' => 'Debe indicarse la agilidad del jugador',
          'agl.numeric' => 'La agilidad debe ser un valor numérico válido',
          'agl.min' => 'La agilidad debe ser un valor numérico entre 1 y 10',
          'agl.max' => 'La agilidad debe ser un valor numérico entre 1 y 10',
          'av.required' => 'Debe indicarse la armadura del jugador',
          'av.numeric' => 'La armadura debe ser un valor numérico válido',
          'av.min' => 'La armadura debe ser un valor numérico entre 1 y 12',
          'av.max' => 'La armadura debe ser un valor numérico entre 1 y 12',
          'px.required' => 'Debe indicarse la experiencia del jugador',
          'px.numeric' => 'La experiencia debe ser un valor numérico válido',
          'px.min' => 'La experiencia debe ser un valor numérico entre 0 y 275',
          'px.max' => 'La experiencia debe ser un valor numérico entre 0 y 275',
          'precio.required' => 'Debe indicarse el precio del jugador',
          'precio.numeric' => 'El precio debe ser un valor numérico válido',
          'precio.min' => 'El precio debe ser un valor numérico entre 40000 y 1000000',
          'precio.max' => 'El precio debe ser un valor numérico entre 40000 y 1000000',
          'hf.required' => 'Debe indicarse cuantos heridos ha provocado el jugador',
          'hf.numeric' => 'Los heridos provocados deben ser un valor numérico válido',
          'hf.min' => 'Los heridos provocados deben ser un valor numérico entre 0 y 300',
          'hf.max' => 'Los heridos provocados deben ser un valor numérico entre 0 y 300',
          'mf.required' => 'Debe indicarse cuantos muertos ha provocado el jugador',
          'mf.numeric' => 'Los muertos provocados deben ser un valor numérico válido',
          'mf.min' => 'Los muertos provocados deben ser un valor numérico entre 0 y 300',
          'mf.max' => 'Los muertos provocados deben ser un valor numérico entre 0 y 300',
          'hc.required' => 'Debe indicarse cuantas veces ha sido herido el jugador',
          'hc.numeric' => 'Las heridas sufridas deben ser un valor numérico válido',
          'hc.min' => 'Las heridas sufridas deben ser un valor numérico entre 0 y 300',
          'hc.max' => 'Las heridas sufridas deben ser un valor numérico entre 0 y 300',
          'curado.required' => 'Debe indicarse cuantas veces ha sido atendido por el apotecario del equipo el jugador',
          'curado.numeric' => 'Las atenciones del apotecario deben ser un valor numérico válido',
          'curado.min' => 'Las atenciones del apotecario deben ser un valor numérico entre 0 y 300',
          'curado.max' => 'Las atenciones del apotecario deben ser un valor numérico entre 0 y 300',
          'pases.required' => 'Debe indicarse cuantos pases ha realizado el jugador',
          'pases.numeric' => 'Los pases deben ser un valor numérico válido',
          'pases.min' => 'Los pases deben ser un valor numérico entre 0 y 1000',
          'pases.max' => 'Los pases deben ser un valor numérico entre 0 y 1000',
          'yardaspase.required' => 'Debe indicarse cuantas yardas de pase ha conseguido el jugador',
          'yardaspase.numeric' => 'Las yardas de pase deben ser un valor numérico válido',
          'yardaspase.min' => 'Las yardas de pase deben ser un valor numérico entre 0 y 10000',
          'yardaspase.max' => 'Las yardas de pase deben ser un valor numérico entre 0 y 10000',
          'intercepciones.required' => 'Debe indicarse cuantas intercepciones ha realizado el jugador',
          'intercepciones.numeric' => 'Las intercepciones realizadas deben ser un valor numérico válido',
          'intercepciones.min' => 'Las intercepciones realizadas deben ser un valor numérico entre 0 y 300',
          'intercepciones.max' => 'Las intercepciones realizadas deben ser un valor numérico entre 0 y 300',
          'td.required' => 'Debe indicarse cuantos touchdowns ha anotado el jugador',
          'td.numeric' => 'Los touchdowns anotados deben ser un valor numérico válido',
          'td.min' => 'Los touchdowns anotados deben ser un valor numérico entre 0 y 300',
          'td.max' => 'Los touchdowns anotados deben ser un valor numérico entre 0 y 300',
          'jugados.required' => 'Debe indicarse cuantos partidos ha jugado el jugador',
          'jugados.numeric' => 'Los partidos jugados deben ser un valor numérico válido',
          'jugados.min' => 'Los partidos jugados deben ser un valor numérico entre 0 y 300',
          'jugados.max' => 'Los partidos jugados deben ser un valor numérico entre 0 y 300',
          'mvp.required' => 'Debe indicarse cuantas veces ha sido galardonado con el mvp el jugador',
          'mvp.numeric' => 'Los mvp deben ser un valor numérico válido',
          'mvp.min' => 'Los mvp deben ser un valor numérico entre 0 y 300',
          'mvp.max' => 'Los mvp deben ser un valor numérico entre 0 y 300',
          'niggling.required' => 'Debe indicarse cuantas heridas incapacitantes ha sufrido el jugador',
          'niggling.numeric' => 'Las heridas incapacitantes deben ser un valor numérico válido',
          'niggling.min' => 'Las heridas incapacitantes deben ser un valor numérico entre 0 y 10',
          'niggling.max' => 'Las heridas incapacitantes deben ser un valor numérico entre 0 y 10'
        ];
    }
}
