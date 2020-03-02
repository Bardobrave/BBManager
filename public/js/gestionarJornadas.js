$("document").ready(function() {

  //Definimos un objeto para la transición de elementos
  var transferredElement;

  $(".dragContainer").on("dragover", function (ev) {
    ev.preventDefault();
  });

  $(".dragContainer").on("drop", function(ev) {
    $(this).append(transferredElement);
  });

  $(".equipoJornada").on("dragstart", function (ev) {
    transferredElement = $(this);
  });

  $("#enviar").on("click", function() {
    //Al pulsar el botón de enviar se construye un array donde almacenar los emparejamientos
    var jsonResponse = [];

    //Y se recorren todos los elementos emparejamiento, añadiéndolos al array
    $(".emparejamientoContainer").each(function() {
      var anfitrion = $(this).children(".anfitrion").children();
      var visitante = $(this).children(".visitante").children();
      if (anfitrion.length == 1 && visitante.length == 1)
        jsonResponse.push({
          'anfitrion': anfitrion.attr("id").split('_')[1],
          'visitante': visitante.attr("id").split('_')[1]
        });
    }).promise().done(function () {
      $("#emparejamientos").val(JSON.stringify(jsonResponse));
      //console.log($("#emparejamientos").val());
      $("#formJornada").submit();
    });

  });

});
