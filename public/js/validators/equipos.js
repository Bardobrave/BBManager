$("document").ready(function () {
  /*Fichero que gestiona las validaciones cliente de los formularios de equipo*/

  //Validación de nombres de jugadores
  $('.nombreJugador').on('blur', function() {
    var sError = '';
    sError = $(this).isValidStringNotNull('El nombre del jugador no puede superar los 255 caracteres',
      'El nombre del jugador es obligatorio', 255);

    if (sError == '')
      $.ajax({
        async: false,
        url: '/equipos/checkNombreJugadorRepetido/' + encodeURI($(this).val()) + '/'
          + $("#idEquipo").val(),
        success: function (repetido) {
          if (repetido == "true")
            sError = 'Tu equipo ya tiene (o ha tenido) un jugador con ese nombre';
        }
      });

    $(this).validator(sError != '', sError);
  });

  //Validación de nombres de equipos
  $('#nombre').on('blur', function() {
    var sError = '';
    sError = $(this).isValidStringNotNull('El nombre no puede superar los 255 caracteres',
      'El nombre del equipo es obligatorio', 255);

    if (sError == '')
      $.ajax({
        async:false,
        url: '/equipos/checkNombreRepetido/' + encodeURI($(this).val()) + '/'
          + $("#idEquipo").val(),
        success: function(repetido) {
          if (repetido == 'true')
            sError = 'El nombre del equipo ya existe en la base de datos';
        }
      });

    $(this).validator(sError != '', sError);
  });

  //Validación de presupuestos
  $('#presupuesto').on('blur', function() {
    var sError = '';
    sError = $(this).isValidNumberNotNull('Debe indicarse un presupuesto válido',
      'Debe indicarse el presupuesto inicial del equipo');

    if (sError == '' && (parseInt($(this).val()) < 1000000 || parseInt($(this).val()) > 9999999))
      sError = 'El presupuesto debe estar entre uno y diez millones';

    $(this).validator(sError != '', sError);
  });

  //Validación de factor de hinchas
  $('#ff').on('blur', function() {
    var sError = '';
    sError = $(this).isValidNumberNotNull('El factor de hinchas debe ser un valor numérico válido',
      'Debe indicarse un valor para el factor de hinchas');

    if (sError == '' && (parseInt($(this).val()) < 0 || parseInt($(this).val()) > 18))
      sError = 'El factor de hinchas debe ser positivo y no puede superar 18';

    $(this).validator(sError != '', sError);
  });

  //Validación de la tesoreria
  $('#tesoreria').on('blur', function() {
    var sError = '';
    sError = $(this).isValidNumberNotNull('La tesorería debe ser un valor numérico válido',
      'Debe indicarse un valor para la tesorería');

    if (sError == '' && parseInt($(this).val()) < 0)
      sError = 'La tesorería debe ser positiva';

    $(this).validator(sError != '', sError);
  });

  //Validación del banco
  $('#banco').on('blur', function() {
    var sError = '';
    sError = $(this).isValidNumberNotNull('El banco debe ser un valor numérico válido',
      'Debe indicarse un valor para el banco');

    if (sError == '') {
      if (parseInt($(this).val()) < 0)
        sError = 'El banco debe tener un valor positivo';

      if (parseInt($(this).val()) > 200000)
        sError = 'El banco no puede almacenar más de 200000';
    }

    $(this).validator(sError != '', sError);
  });

  //Validación de partidos jugados
  $('#jugados').on('blur', function() {
    var sError = '';
    sError = $(this).isValidNumberNotNull('El número de partidos jugados debe ser un valor numérico válido',
      'Debe indicarse el número de partidos jugados por el equipo');

    if (sError == '' && (parseInt($(this).val()) < 0 || parseInt($(this).val()) > 100))
      sError = 'El número de partidos jugados debe ser un valor numérico entre 0 y 100';

    $(this).validator(sError != '', sError);
  });

  //Validación de partidos ganados
  $('#ganados').on('blur', function() {
    var sError = '';
    sError = $(this).isValidNumberNotNull('El número de partidos ganados debe ser un valor numérico válido',
      'Debe indicarse el número de partidos ganados por el equipo');

    if (sError == '' && (parseInt($(this).val()) < 0 || parseInt($(this).val()) > 100))
      sError = 'El número de partidos ganados debe ser un valor numérico entre 0 y 100';

    $(this).validator(sError != '', sError);
  });

  //Validación de partidos perdidos
  $('#perdidos').on('blur', function() {
    var sError = '';
    sError = $(this).isValidNumberNotNull('El número de partidos perdidos debe ser un valor numérico válido',
      'Debe indicarse el número de partidos perdidos por el equipo');

    if (sError == '' && (parseInt($(this).val()) < 0 || parseInt($(this).val()) > 100))
      sError = 'El número de partidos perdidos debe ser un valor numérico entre 0 y 100';

    $(this).validator(sError != '', sError);
  });

  //Validación de partidos empatados
  $('#empatados').on('blur', function() {
    var sError = '';
    sError = $(this).isValidNumberNotNull('El número de partidos empatados debe ser un valor numérico válido',
      'Debe indicarse el número de partidos empatados por el equipo');

    if (sError == '' && (parseInt($(this).val()) < 0 || parseInt($(this).val()) > 100))
      sError = 'El número de partidos empatados debe ser un valor numérico entre 0 y 100';

    $(this).validator(sError != '', sError);
  });

  //Validación de touchdowns a favor
  $('#tdf').on('blur', function() {
    var sError = '';
    sError = $(this).isValidNumberNotNull('El número de td anotados debe ser un valor numérico válido',
      'Debe indicarse el número de td anotados por el equipo');

    if (sError == '' && (parseInt($(this).val()) < 0 || parseInt($(this).val()) > 300))
      sError = 'El número de td anotados debe ser un valor numérico entre 0 y 300';

    $(this).validator(sError != '', sError);
  });

  //Validación de touchdowns en contra
  $('#tdc').on('blur', function() {
    var sError = '';
    sError = $(this).isValidNumberNotNull('El número de td recibidos debe ser un valor numérico válido',
      'Debe indicarse el número de td recibidos por el equipo');

    if (sError == '' && (parseInt($(this).val()) < 0 || parseInt($(this).val()) > 300))
      sError = 'El número de td recibidos debe ser un valor numérico entre 0 y 300';

    $(this).validator(sError != '', sError);
  });

  //Validación de heridos a favor
  $('#hf').on('blur', function() {
    var sError = '';
    sError = $(this).isValidNumberNotNull('El número de heridos provocados debe ser un valor numérico válido',
      'Debe indicarse el número de heridos provocados por el equipo');

    if (sError == '' && (parseInt($(this).val()) < 0 || parseInt($(this).val()) > 1000))
      sError = 'El número de heridos provocados debe ser un valor numérico entre 0 y 1000';

    $(this).validator(sError != '', sError);
  });

  //Validación de heridos en contra
  $('#hc').on('blur', function() {
    var sError = '';
    sError = $(this).isValidNumberNotNull('El número de heridos sufridos debe ser un valor numérico válido',
      'Debe indicarse el número de heridos sufridos por el equipo');

    if (sError == '' && (parseInt($(this).val()) < 0 || parseInt($(this).val()) > 1000))
      sError = 'El número de heridos sufridos debe ser un valor numérico entre 0 y 1000';

    $(this).validator(sError != '', sError);
  });

  //Validación de muertos a favor
  $('#mf').on('blur', function() {
    var sError = '';
    sError = $(this).isValidNumberNotNull('El número de muertos provocados debe ser un valor numérico válido',
      'Debe indicarse el número de muertos provocados por el equipo');

    if (sError == '' && (parseInt($(this).val()) < 0 || parseInt($(this).val()) > 1000))
      sError = 'El número de muertos provocados debe ser un valor numérico entre 0 y 1000';

    $(this).validator(sError != '', sError);
  });

  //Validación de muertos a favor
  $('#mc').on('blur', function() {
    var sError = '';
    sError = $(this).isValidNumberNotNull('El número de muertos sufridos debe ser un valor numérico válido',
      'Debe indicarse el número de muertos sufridos por el equipo');

    if (sError == '' && (parseInt($(this).val()) < 0 || parseInt($(this).val()) > 1000))
      sError = 'El número de muertos sufridos debe ser un valor numérico entre 0 y 1000';

    $(this).validator(sError != '', sError);
  });

  //Validación de pases
  $('#pases').on('blur', function() {
    var sError = '';
    sError = $(this).isValidNumberNotNull('El número de pases realizados debe ser un valor numérico válido',
      'Debe indicarse el número de pases realizados por el equipo');

    if (sError == '' && (parseInt($(this).val()) < 0 || parseInt($(this).val()) > 1000))
      sError = 'El número de pases realizados debe ser un valor numérico entre 1 y 1000';

    $(this).validator(sError != '', sError);
  });

  //Validación de yardas de pase
  $('#yardaspase').on('blur', function() {
    var sError = '';
    sError = $(this).isValidNumberNotNull('El número de yardas de pase conseguidas debe ser un valor numérico válido',
      'Debe indicarse el número de yardas de pase conseguidas por el equipo');

    if (sError == '' && (parseInt($(this).val()) < 0 || parseInt($(this).val()) > 10000))
      sError = 'El número de yardas de pase conseguidas debe ser un valor numérico entre 1 y 10000';

    $(this).validator(sError != '', sError);
  });

  //Validación de intercepciones realizadas
  $('#intercepciones').on('blur', function() {
    var sError = '';
    sError = $(this).isValidNumberNotNull('El número de intercepciones realizadas debe ser un valor numérico válido',
      'Debe indicarse el número de intercepciones realizadas por el equipo');

    if (sError == '' && (parseInt($(this).val()) < 0 || parseInt($(this).val()) > 100))
      sError = 'El número de intercepciones realizadas debe ser un valor numérico entre 0 y 100';

    $(this).validator(sError != '', sError);
  });

  //Validación de intercepciones sufridas
  $('#intercepcionesc').on('blur', function() {
    var sError = '';
    sError = $(this).isValidNumberNotNull('El número de intercepciones sufridas debe ser un valor numérico válido',
      'Debe indicarse el número de intercepciones sufridas por el equipo');

    if (sError == '' && (parseInt($(this).val()) < 0 || parseInt($(this).val()) > 100))
      sError = 'El número de intercepciones sufridas debe ser un valor numérico entre 0 y 100';

    $(this).validator(sError != '', sError);
  });
});
