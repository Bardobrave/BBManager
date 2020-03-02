$("document").ready(function() {

  //Definimos un objeto para la transición de elementos
  var transferredElement;

  $(".grupoContainer").on("dragover", function (ev) {
    ev.preventDefault();
  });

  $(".grupoContainer").on("drop", function(ev) {
    $(this).append(transferredElement);
  });

  $(".equipoGrupo").on("dragstart", function (ev) {
    transferredElement = $(this);
  });

  $("#enviar").on("click", function() {
    //Al pulsar el botón de enviar se construye un array donde almacenar los grupos
    var jsonResponse = [];

    //Y se recorren todos los elementos de grupo, añadiéndolos al array
    $(".equipoGrupo").each(function() {
      var parentGroup = $(this).parent().attr("id").split('_')[1];
      if (jsonResponse[parentGroup] == undefined)
        jsonResponse[parentGroup] = [];

      jsonResponse[parentGroup].push($(this).attr("id").split('_')[1]);
    }).promise().done(function () {
      $("#grupos").val(JSON.stringify(jsonResponse));
      $("#formGrupos").submit();
    });

  });

});
