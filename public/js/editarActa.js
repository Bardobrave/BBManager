$("document").ready(function() {

  var annotations = [];//($("#annotations").val() == "") ? [] : JSON.parse($("#annotations").val());
  console.log(annotations);

  //Función para borrar un highlight
  function eraseHighlight(highlight) {
    highlight.parent().parent().detach();
  }

  //Función que asigna el evento de click a los botones de borrado
  function attachEraseEvent() {
    $(".eraseAnnotation").on("click", function() {
      eraseHighlight($(this));
    });
  }

  //Se asocia el evento en la carga inicial
  attachEraseEvent();

  //Control de pases
  $(".passConfirm").on("click", function() {
    var aActivo = $(".passForm").children("#numeroJugador").val().split('|');
    var efecto = $(".passForm").children("#yardaspase").val();
    var newRowData = '<tr>'
      +'<td scope="col">'
        + '<span class="annotation">'
          + 'El nº ' + aActivo[1] + '  ha realizado un pase de ' + efecto + ' yardas.'
        + '</span>'
        + '<span class="eraseAnnotation fas fa-times-circle"></span>'
        + '<input type="hidden" class="annotationVal" value=\'{ \"id\": \"new\", '
          + '\"tipo\": \"PASE\", \"activo\": \"' + aActivo[0] + '\", \"pasivo\": \"\", '
          + '\"efecto\": \"' + efecto + '\" }\' />'
      + '</td>'
    + '</tr>';

    $(".annotationsTable > tbody").append(newRowData);
    attachEraseEvent();
  });

  //Control de heridos (H+)
  $(".casConfirm").on("click", function() {
    var aActivo = $(".casForm").children("#numeroJugador").val().split('|');
    var aPasivo = $(".casForm").children("#numeroJugadorPasivo").val().split('|');
    var efecto = $(".casForm").children("#herida").val();
    var falta = $(".casForm").children("#falta").is(":checked");
    var publico = $(".casForm").children("#publico").is(":checked");
    var newRowData = '<tr>'
      + '<td scope="col">'
        + '<span class="annotation">'
          +  'El nº ' + aActivo[1] + ' ha lesionado al jugador nº ' + aPasivo[1]
            + ' (' + efecto + ') '
            + ((falta) ? 'haciendo una falta' : '')
            + ((publico) ? 'tirándolo por la banda' : '')
        + '</span>'
        + '<span class="eraseAnnotation fas fa-times-circle"></span>'
        + '<input type="hidden" class="annotationVal" value=\'{ \"id\": \"new\", '
          + '\"tipo\": \"HERIDO\", \"activo\": ' + aActivo[0] + ', \"pasivo\": '
          + aPasivo[0] + ', \"efecto\": \"' + efecto + '|' + falta + '|' + publico
          + '\" }\' />'
      + '</td>'
    + '</tr>';
    $(".annotationsTable > tbody").append(newRowData);
    attachEraseEvent();
  });

  //Control de lesionados (H-)
  $(".lesionadoConfirm").on("click", function() {
    var aActivo = $(".lesionadoForm").children("#numeroJugador").val().split('|');
    var efecto = $(".lesionadoForm").children("#herida").val();
    var modo = $(".lesionadoForm").children("#modoHerida").val();
    var medico = $(".lesionadoForm").children("#medico").is(":checked");
    var regeneracion = $(".lesionadoForm").children("#regenera").is(":checked");
    var newRowData = '<tr>'
      + '<td scope="col">'
        + '<span class="annotation">'
          +  'El nº ' + aActivo[1] + ' sufre una lesión (' + efecto + ') ' + modo + '. '
          + ((medico) ? 'Fue curado por el médico del equipo' : '')
          + ((regeneracion) ? 'El jugador regeneró': '')
        + '</span>'
        + '<span class="eraseAnnotation fas fa-times-circle"></span>'
        + '<input type="hidden" class="annotationVal" value=\'{ \"id\": \"new\", '
          + '\"tipo\": \"LESIONADO\", \"activo\": ' + aActivo[0] + ', '
          + '\"pasivo\": \"\", \"efecto\": \"' + efecto + '|' + modo + '|' + medico
          + '|' + regeneracion + '\" }\' />'
      + '</td>'
    + '</tr>';

    $(".annotationsTable > tbody").append(newRowData);
    attachEraseEvent();
  });

  //Control de intercepciones
  $(".interceptionConfirm").on("click", function() {
    var aActivo = $(".interceptionForm").children("#numeroJugador").val().split('|');
    var newRowData = '<tr>'
      + '<td scope="col">'
        + '<span class="annotation">'
          + 'El nº ' + aActivo[1] + '  ha realizado una intercepción.'
        + '</span>'
        + '<span class="eraseAnnotation fas fa-times-circle"></span>'
        + '<input type="hidden" class="annotationVal" value=\'{ \"id\": \"new\", '
          + '\"tipo\": \"INTERCEPCION\", \"activo\": ' + aActivo[0]
          + ', \"pasivo\": \"\", \"efecto\": \"\" }\' />'
      + '</td>'
    + '</tr>';

    $(".annotationsTable > tbody").append(newRowData);
    attachEraseEvent();
  });

  //Control de touchdowns
  $(".tdConfirm").on("click", function() {
    var aActivo = $(".tdForm").children("#numeroJugador").val().split('|');
    var newRowData = '<tr>'
      + '<td scope="col">'
        + '<span class="annotation">'
          + 'El nº ' + aActivo[1] + '  ha anotado un touchdown.'
        + '</span>'
        + '<span class="eraseAnnotation fas fa-times-circle"></span>'
        + '<input type="hidden" class="annotationVal" value=\'{ \"id\": \"new\", '
          + '\"tipo\": \"TD\", \"activo\": ' + aActivo[0] + ', \"pasivo\": \"\", '
          + '\"efecto\": \"\" }\' />'
      + '</td>'
    + '</tr>';

    $(".annotationsTable > tbody").append(newRowData);
    attachEraseEvent();
  });

  //Control de mvps
  $(".mvpConfirm").on("click", function() {
    var aActivo = $(".mvpForm").children("#numeroJugador").val().split('|');
    var newRowData = '<tr>'
      + '<td scope="col">'
        + '<span class="annotation">'
          + 'El nº ' + aActivo[1] + '  ha sido galardonado con un MVP.'
        + '</span>'
        + '<span class="eraseAnnotation fas fa-times-circle"></span>'
        + '<input type="hidden" class="annotationVal" value=\'{ \"id\": \"new\", '
          + '\"tipo\": \"MVP\", \"activo\": ' + aActivo[0] + ', \"pasivo\": \"\", '
          + '\"efecto\": \"\" }\' />'
      + '</td>'
    + '</tr>';

    $(".annotationsTable > tbody").append(newRowData);
    attachEraseEvent();
  });

  //Botón Finalizar
  $("#finalizar").on("click", function() {
    //Antes que nada, comprobar que todos los campos clave están rellenos
    $("#recaudacion").blur();
    $("#tesofinal").blur();
    $("#espectadores").blur();
    $("#fffinal").blur();
    $("#gastoinducements").blur();
    if ($(".is-invalid").length != 0)
      alert("Hay errores en campos clave, corrígelos antes de finalizar el acta");
    else
      $("#finalizacionForm").modal();
  });

  //Finalización del acta
  $("#finalizacionConfirm").on("click", function() {
    if ($("#tdcontraModal").val() != "" && $.isNumeric($("#tdcontraModal").val())) {
      $("#actafinalizada").val(1);
      $("#tdcontra").val($("#tdcontraModal").val());
      $("#enviar").click();
    } else {
      alert("Debes indicar el número de TD que has encajado para poder cerrar el acta");
      $("#tdcontraModal").val("");
      $("#tdcontra").val("");
    }
  });

  //Evento click sobre el botón enviar
  $("#enviar").on("click", function() {
    //Se recorren todos los elementos de la tabla de highlights y se construye un elemento json con ellos
    $(".annotationVal").each(function () {
      console.log($(this).val());
      annotations.push(JSON.parse($(this).val()));
    }).promise().done(function () {
      $("#annotations").val(JSON.stringify(annotations));
      $("#actaForm").submit();
    });
  });

});
