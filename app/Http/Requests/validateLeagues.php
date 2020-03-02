<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\URL;
use Illuminate\Foundation\Http\FormRequest;
use App\League;

class validateLeagues extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        //Sólo los administradores y comisionados pueden crear o editar ligas
        if ($this->user()->rol != 3) {

          //Una liga puede crearse directamente
          if (strpos(URL::current(), 'crear') != false)
            return true;

          //Para otras operaciones necesitaremos conocer el estado de la liga  
          $league = League::find($this->idLiga);

          //Para editar una liga, debe estar abierta
          if (strpos(URL::current(), 'editar') != false && $league->abierta)
            return true;

          //Para manipular los grupos, la liga debe estar cerrada y no iniciada
          if (strpos(URL::current(), 'asignarGrupos') != false
            && !$league->abierta && !$league->iniciada)
          return true;
        }

        return redirect('home');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
      //Crear o Editar una liga tiene una serie de campos obligatorios
      if (strpos(URL::current(), 'editar') != false
        || strpos(URL::current(), 'crear') != false)
        return [
            'nombre' => 'required|unique:LIGAS,nombre,'.$this->id.'|max:255',
            'maximopresupuesto' => 'required|numeric|min:1000000|max:9999999',
            'numgrupos' => 'required|numeric|min:1|max:10',
            'puntosvictoria' => 'required|numeric|min:1|max:1000',
            'puntosempate' => 'required|numeric|min:0|max:1000',
            'puntosderrota' => 'required|numeric|min:0|max:1000'
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
            'nombre.required' => 'Es obligatorio dar un nombre a la liga',
            'nombre.unique' => 'El nombre de la liga ya existe en la base de datos',
            'nombre.max' => 'El nombre no puede superar los 255 caracteres',
            'maximopresupuesto.required' => 'Debe indicarse el máximo presupuesto con el que los equipos pueden concurrir',
            'maximopresupuesto.numeric' => 'El máximo presupuesto debe ser un valor numérico válido',
            'maximopresupuesto.min' => 'El máximo presupuesto debe estar entre uno y diez millones',
            'maximopresupuesto.max' => 'El máximo presupuesto debe estar entre uno y diez millones',
            'numgrupos.required' => 'Debe indicarse el número de grupos de la liga',
            'numgrupos.numeric' => 'El número de grupos debe ser un valor numérico válido',
            'numgrupos.min' => 'El número de grupos debe ser un valor entre uno y diez',
            'numgrupos.max' => 'El número de grupos debe ser un valor entre uno y diez',
            'puntosvictoria.required' => 'Debe indicarse la cantidad de puntos de liga que concede una victoria',
            'puntosvictoria.numeric' => 'La cantidad de puntos por victoria debe ser un valor numérico válido',
            'puntosvictoria.min' => 'La cantidad de puntos por victoria debe ser un valor entre uno y mil',
            'puntosvictoria.max' => 'La cantidad de puntos por victoria debe ser un valor entre uno y mil',
            'puntosempate.required' => 'Debe indicarse la cantidad de puntos de liga que concede un empate',
            'puntosempate.numeric' => 'La cantidad de puntos por empate debe ser un valor numérico válido',
            'puntosempate.min' => 'La cantidad de puntos por empate debe ser un valor entre cero y mil',
            'puntosempate.max' => 'La cantidad de puntos por empate debe ser un valor entre cero y mil',
            'puntosderrota.required' => 'Debe indicarse la cantidad de puntos de liga que concede una derrota',
            'puntosderrota.numeric' => 'La cantidad de puntos por derrota debe ser un valor numérico válido',
            'puntosderrota.min' => 'La cantidad de puntos por derrota debe ser un valor entre cero y mil',
            'puntosderrota.max' => 'La cantidad de puntos por derrota debe ser un valor entre cero y mil'
        ];
    }
}
