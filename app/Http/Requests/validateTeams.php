<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\URL;
use Illuminate\Foundation\Http\FormRequest;
use App\Team;

class validateTeams extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        /*Para modificar cualquier equipo se debe ser administrador, comisionado
          o el dueño del equipo. Esto significa que la creación de equipos es libre,
          pero la edición no */
        if (strpos(URL::current(), 'crear') != false)
          return true;

        if ($this->user()->rol != 3 || $this->user()->id == Team::find($this->idEquipo)->user->id)
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

      //Se marcan unas reglas u otras dependiendo de la url de la petición
      //Si se trata de una petición de creación de equipo
      if (strpos(URL::current(), 'crear') != false) {
        return [
            'nombre' => 'required|unique:EQUIPOS|max:255',
            'raza' => 'required|numeric|min:1|max:24',
            'presupuesto' => 'required|numeric|min:1000000|max:9999999'
        ];
        //Si se trata de una petición de modificación de equipo
      } else if (strpos(URL::current(), 'editar') != false) {
        if ($this->user()->rol != 3) {
          return [
                'nombre' => 'required|unique:EQUIPOS,nombre,'.$this->id.'|max:255',
                'raza' => 'required|numeric|min:1|max:24',
                'presupuesto' => 'required|numeric|min:1000000|max:9999999',
                'ff' => 'required|numeric|min:0|max:18',
                'tesoreria' => 'required|numeric|min:0',
                'banco' => 'required|numeric|min:0|max:200000',
                'jugados' => 'required|numeric|min:0|max:100',
                'ganados' => 'required|numeric|min:0|max:100',
                'perdidos' => 'required|numeric|min:0|max:100',
                'empatados' => 'required|numeric|min:0|max:100',
                'tdf' => 'required|numeric|min:0|max:300',
                'tdc' => 'required|numeric|min:0|max:300',
                'hf' => 'required|numeric|min:0|max:1000',
                'hc' => 'required|numeric|min:0|max:1000',
                'mf' => 'required|numeric|min:0|max:1000',
                'mc' => 'required|numeric|min:0|max:1000',
                'pases' => 'required|numeric|min:0|max:1000',
                'yardaspase' => 'required|numeric|min:0|max:10000',
                'intercepciones' => 'required|numeric|min:0|max:100',
                'intercepcionesc' => 'required|numeric|min:0|max:100'
          ];
        } else {
          return [
                'nombre' => 'required|unique:EQUIPOS,nombre,'.$this->id.'|max:255',
                'raza' => 'required|numeric|min:1|max:24',
                'presupuesto' => 'required|numeric|min:1000000|max:9999999'
          ];
        }
      } else {
        //En este caso se está añadiendo un jugador al equipo
        return [
              'numero' => 'required|numeric|min:1|max:16',
              'nombre' => 'required|max:255',
              'posicion' => 'required|numeric|min:1|max:107'
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
            'nombre.required' => 'El nombre del equipo es obligatorio',
            'nombre.unique' => 'El nombre del equipo ya existe en la base de datos',
            'nombre.max' => 'El nombre no puede superar los 255 caracteres',
            'raza.required'  => 'Debe indicarse la raza del equipo',
            'raza.numeric' => 'Debe indicarse una raza válida para el equipo',
            'raza.min' => 'Debe indicarse una raza válida para el equipo',
            'raza.max' => 'Debe indicarse una raza válida para el equipo',
            'presupuesto.required' => 'Debe indicarse el presupuesto inicial del equipo',
            'presupuesto.numeric' => 'Debe indicarse un presupuesto válido',
            'presupuesto.min' => 'El presupuesto debe estar entre uno y diez millones',
            'presupuesto.max' => 'El presupuesto debe estar entre uno y diez millones',
            'ff.required' => 'Debe indicarse un valor para el factor de hinchas',
            'ff.numeric' => 'El factor de hinchas debe ser un valor numérico válido',
            'ff.min' => 'El factor de hinchas debe ser positivo y no puede superar 18',
            'ff.max' => 'El factor de hinchas debe ser positivo y no puede superar 18',
            'tesoreria.required' => 'Debe indicarse un valor para la tesorería',
            'tesoreria.numeric' => 'La tesorería debe ser un valor numérico válido',
            'tesoreria.min' => 'La tesorería debe ser positiva',
            'banco.required' => 'Debe indicarse un valor para el banco',
            'banco.numeric' => 'El banco debe ser un valor numérico válido',
            'banco.min' => 'El banco debe tener un valor positivo',
            'banco.max' => 'El banco no puede almacenar más de 200000',
            'jugados.required' => 'Debe indicarse el número de partidos jugados por el equipo',
            'jugados.numeric' => 'El número de partidos jugados debe ser un valor numérico válido',
            'jugados.min' => 'El número de partidos jugados debe ser un valor numérico entre 0 y 100',
            'jugados.max' => 'El número de partidos jugados debe ser un valor numérico entre 0 y 100',
            'ganados.required' => 'Debe indicarse el número de partidos ganados por el equipo',
            'ganados.numeric' => 'El número de partidos ganados debe ser un valor numérico válido',
            'ganados.min' => 'El número de partidos ganados debe ser un valor numérico entre 0 y 100',
            'ganados.max' => 'El número de partidos ganados debe ser un valor numérico entre 0 y 100',
            'perdidos.required' => 'Debe indicarse el número de partidos perdidos por el equipo',
            'perdidos.numeric' => 'El número de partidos perdidos debe ser un valor numérico válido',
            'perdidos.min' => 'El número de partidos perdidos debe ser un valor numérico entre 0 y 100',
            'perdidos.max' => 'El número de partidos perdidos debe ser un valor numérico entre 0 y 100',
            'empatados.required' => 'Debe indicarse el número de partidos empatados por el equipo',
            'empatados.numeric' => 'El número de partidos empatados debe ser un valor numérico válido',
            'empatados.min' => 'El número de partidos empatados debe ser un valor numérico entre 0 y 100',
            'empatados.max' => 'El número de partidos empatados debe ser un valor numérico entre 0 y 100',
            'tdf.required' => 'Debe indicarse el número de td anotados por el equipo',
            'tdf.numeric' => 'El número de td anotados debe ser un valor numérico válido',
            'tdf.min' => 'El número de td anotados debe ser un valor numérico entre 0 y 300',
            'tdf.max' => 'El número de td anotados debe ser un valor numérico entre 0 y 300',
            'tdc.required' => 'Debe indicarse el número de td recibidos por el equipo',
            'tdc.numeric' => 'El número de td recibidos debe ser un valor numérico válido',
            'tdc.min' => 'El número de td recibidos debe ser un valor numérico entre 0 y 300',
            'tdc.max' => 'El número de td recibidos debe ser un valor numérico entre 0 y 300',
            'hf.required' => 'Debe indicarse el número de heridos provocados por el equipo',
            'hf.numeric' => 'El número de heridos provocados debe ser un valor numérico válido',
            'hf.min' => 'El número de heridos provocados debe ser un valor numérico entre 0 y 1000',
            'hf.max' => 'El número de heridos provocados debe ser un valor numérico entre 0 y 1000',
            'hc.required' => 'Debe indicarse el número de heridos sufridos por el equipo',
            'hc.numeric' => 'El número de heridos sufridos debe ser un valor numérico válido',
            'hc.min' => 'El número de heridos sufridos debe ser un valor numérico entre 0 y 1000',
            'hc.max' => 'El número de heridos sufridos debe ser un valor numérico entre 0 y 1000',
            'mf.required' => 'Debe indicarse el número de muertos provocados por el equipo',
            'mf.numeric' => 'El número de muertos provocados debe ser un valor numérico válido',
            'mf.min' => 'El número de muertos provocados debe ser un valor numérico entre 0 y 1000',
            'mf.max' => 'El número de muertos provocados debe ser un valor numérico entre 0 y 1000',
            'mc.required' => 'Debe indicarse el número de muertos sufridos por el equipo',
            'mc.numeric' => 'El número de muertos sufridos debe ser un valor numérico válido',
            'mc.min' => 'El número de muertos sufridos debe ser un valor numérico entre 0 y 1000',
            'mc.max' => 'El número de muertos sufridos debe ser un valor numérico entre 0 y 1000',
            'pases.required' => 'Debe indicarse el número de pases realizados por el equipo',
            'pases.numeric' => 'El número de pases realizados debe ser un valor numérico válido',
            'pases.min' => 'El número de pases realizados debe ser un valor numérico entre 1 y 1000',
            'pases.max' => 'El número de pases realizados debe ser un valor numérico entre 1 y 1000',
            'yardaspase.required' => 'Debe indicarse el número de yardas de pase conseguidas por el equipo',
            'yardaspase.numeric' => 'El número de yardas de pase conseguidas debe ser un valor numérico válido',
            'yardaspase.min' => 'El número de yardas de pase conseguidas debe ser un valor numérico entre 1 y 10000',
            'yardaspase.max' => 'El número de yardas de pase conseguidas debe ser un valor numérico entre 1 y 10000',
            'intercepciones.required' => 'Debe indicarse el número de intercepciones realizadas por el equipo',
            'intercepciones.numeric' => 'El número de intercepciones realizadas debe ser un valor numérico válido',
            'intercepciones.min' => 'El número de intercepciones realizadas debe ser un valor numérico entre 0 y 100',
            'intercepciones.max' => 'El número de intercepciones realizadas debe ser un valor numérico entre 0 y 100',
            'intercepcionesc.required' => 'Debe indicarse el número de intercepciones sufridas por el equipo',
            'intercepcionesc.numeric' => 'El número de intercepciones sufridas debe ser un valor numérico válido',
            'intercepcionesc.min' => 'El número de intercepciones sufridas debe ser un valor numérico entre 0 y 100',
            'intercepcionesc.max' => 'El número de intercepciones sufridas debe ser un valor numérico entre 0 y 100'
        ];
    }
}
