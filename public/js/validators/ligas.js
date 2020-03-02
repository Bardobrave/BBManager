$("document").ready(function () {
  /*Fichero que gestiona las validaciones cliente de los formularios de ligas*/

  //Validación de nombres de liga
  $('#nombre').on('blur', function() {
    var sError = '';
    sError = $(this).isValidStringNotNull('El nombre no puede superar los 255 caracteres',
      'Es obligatorio dar un nombre a la liga', 255);

    if (sError == '')
      $.ajax({
        async: false,
        url: '/ligas/checkNombreRepetido/' + encodeURI($(this).val()) + '/'
          + $("#idLiga").val(),
        success: function (repetido) {
          if (repetido == "true")
            sError = 'El nombre de la liga ya existe en la base de datos';
        }
      });

    $(this).validator(sError != '', sError);
  });

  //Validación del presupuesto máximo para los equipos que comienzan la liga
  $('#maximopresupuesto').on('blur', function() {
    var sError = '';
    sError = $(this).isValidNumberNotNull('El máximo presupuesto debe ser un valor numérico válido',
      'Debe indicarse el máximo presupuesto con el que los equipos pueden concurrir');

    if (sError == '' && (parseInt($(this).val()) < 1000000 || parseInt($(this).val()) > 10000000))
      sError = 'El máximo presupuesto debe estar entre uno y diez millones';

    $(this).validator(sError != '', sError);
  });

  //Validación del número de grupos de la liga
  $('#numgrupos').on('blur', function() {
    var sError = '';
    sError = $(this).isValidNumberNotNull('El número de grupos debe ser un valor numérico válido',
      'Debe indicarse el número de grupos de la liga');

    if (sError == '' && (parseInt($(this).val()) < 1 || parseInt($(this).val()) > 10))
      sError = 'El número de grupos debe ser un valor entre uno y diez';

    $(this).validator(sError != '', sError);
  });

  //Validación del número de puntos por victoria
  $('#puntosvictoria').on('blur', function() {
    var sError = '';
    sError = $(this).isValidNumberNotNull('La cantidad de puntos por victoria debe ser un valor numérico válido',
      'Debe indicarse la cantidad de puntos de liga que concede una victoria');

    if (sError == '' && (parseInt($(this).val()) < 1 || parseInt($(this).val()) > 1000))
      sError = 'La cantidad de puntos por victoria debe ser un valor entre uno y mil';

    $(this).validator(sError != '', sError);
  });

  //Validación del número de puntos por empate
  $('#puntosempate').on('blur', function() {
    var sError = '';
    sError = $(this).isValidNumberNotNull('La cantidad de puntos por empate debe ser un valor numérico válido',
      'Debe indicarse la cantidad de puntos de liga que concede un empate');

      if (sError == '' && (parseInt($(this).val()) < 0 || parseInt($(this).val()) > 1000))
        sError = 'La cantidad de puntos por empate debe ser un valor entre cero y mil';

    $(this).validator(sError != '', sError);
  });

  //Validación del número de puntos por derrota
  $('#puntosderrota').on('blur', function() {
    var sError = '';
    sError = $(this).isValidNumberNotNull('La cantidad de puntos por derrota debe ser un valor numérico válido',
      'Debe indicarse la cantidad de puntos de liga que concede una derrota');

    if (sError == '' && (parseInt($(this).val()) < 0 || parseInt($(this).val()) > 1000))
      sError = 'La cantidad de puntos por derrota debe ser un valor entre cero y mil';

    $(this).validator(sError != '', sError);
  });
});
