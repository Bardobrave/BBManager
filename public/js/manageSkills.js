$("document").ready(function() {

  /*Este fichero se encarga de gestionar la edición de las habilidades de los jugadores*/

  //Método que gestiona el evento click sobre una habilidad existente
  function activeBeans(obj) {
    var skillId = obj.attr("id").split('_')[1]; //Se obtiene el id de la skill
    var skillNombre = obj.attr("title"); //El nombre
    var skillNombreCorto = obj.text(); //Y el nombre corto

    /*Se recorre la lista de habilidades disponibles en el contenedor hasta encontrar
      una que sea posterior en orden a la que estamos eliminando*/
    $("#skillListContainer > span").each(function () {
      //Si el nombre de la habilidad en curso es posterior a la que estamos tratando
      if ($(this).text() > skillNombreCorto) {
        //Se añade la burbuja justo delante
        $(this).before(obj);
        //Se cambia su evento onclick
        obj.on('click', function () { activePossibleBeans(obj); });
        return false; //Y se abandona el bucle
      }
    });
  }

  //Método que gestiona el evento click sobre una habilidad posible
  //Cada vez que se hace click sobre una habilidad de la ventana de la lista, se añade a las del jugador
  function activePossibleBeans(obj) {
    var skillId = obj.attr("id").split('_')[1]; //Se obtiene el id de la habilidad
    var skillNombre = obj.attr("title"); //El nombre
    var skillNombreCorto = obj.text(); //Y el nombre corto

    //Se añade al contenedor la burbuja con la habilidad escogida
    $("#skillContainer").append(obj);
    //Se cambia su evento onclick
    obj.on('click', function() { activeBeans(obj); });

    //obj.remove(); //Se elimina la burbuja del contenedor de posibles
    $("#skillListModal").modal('hide'); //Se oculta la ventana modal
  }

  //Se asignan eventos por defecto a las burbujas cargadas inicialmente
  $(".skillBean").on('click', function() { activeBeans($(this)); });
  $(".skillPossibleBean").on('click', function() { activePossibleBeans($(this)); });

  //Al hacer click en el botón de grabar
  $("#grabar").on("click", function() {
    var result = [];
    //Se construye un array con los ids de las habilidades seleccionadas
    $("#skillContainer > span").each(function () {
      result.push($(this).attr("id").split("_")[1]);
    });
    //Se almacena la lista de habilidades en el input type hidden correspondiente
    $("#skills").val(JSON.stringify(result));
    $("#playerForm").submit(); //Se envía el formulario
  });

});
