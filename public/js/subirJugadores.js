$(document).ready(function() {

  var idPlayer;

  //Evento que gestiona el click de subida de nivel en un jugador
  $(".levelup").on("click", function() {
  //Se obtiene el id del jugador
  idPlayer = $(this).attr("id").split('_')[1];

  //Se muestra la ventana modal de subida de nivel
  $("#levelupPlayer").modal();

  });

  //Evento para controlar la selección del tipo de subida
  $("#tipoSubida").on("change", function() {
    var subida = $(this).val();

    //Si el valor elegido es una subida de característica, podemos llamar al controlador
    if (subida != "normal" && subida != "doble")
      location.href = '/jugadores/subida/' + idPlayer + '/' + subida;
    else {
      //Si fuera otro valor, tenemos que hacer una llamada AJAX para obtener las habilidades
      $.ajax({
        async: false,
        url: '/jugadores/getSkills/' + idPlayer + '/' + subida,
        success: function (response) {
          $("#skillSet").html(response);
          $("#habilidadSubida").removeClass("hidden");
          $("#skillSet").on("change", function() {
            $("#habilidadesSubida").addClass("hidden");
            location.href = '/jugadores/subida/' + idPlayer + '/' + $(this).val();
          });
        }
      });
    }
  });


});
