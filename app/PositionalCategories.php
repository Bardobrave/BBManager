<?php

namespace App;

use Illuminate\database\Eloquent\Model;

class PositionalCategories extends Model
{
    //Tabla del modelo
    protected $table = "POSICIONES_CATEGORIAS";

    //Relación de pertenencia a una categoria
    public function category() {
      return $this->belongsTo('App\Category', 'categoria');
    }

}
