$("document").ready(function () {
  /*Fichero que gestiona las validaciones cliente de los formularios de jugadores*/

  //Validación de nombres de jugadores
  $('#nombre').on('blur', function() {
    var sError = '';
    sError = $(this).isValidStringNotNull('El nombre del jugador no puede superar los 255 caracteres',
      'El nombre del jugador es obligatorio', 255);

    if (sError == '')
      $.ajax({
        async: false,
        url: '/equipos/checkNombreJugadorRepetido/' + encodeURI($(this).val()) + '/'
          + $("#idEquipo").val() + '/' + $("#idJugador").val(),
        success: function (repetido) {
          if (repetido == "true")
            sError = 'Tu equipo ya tiene (o ha tenido) un jugador con ese nombre';
        }
      });

    $(this).validator(sError != '', sError);
  });

  //Validación de característica de movimiento
  $('#ma').on('blur', function() {
    var sError = '';
    sError = $(this).isValidNumberNotNull('El movimiento debe ser un valor numérico válido',
      'Debe indicarse el movimiento del jugador');

    if (sError == '' && (parseInt($(this).val()) < 1 || parseInt($(this).val()) > 11))
      sError = 'El movimiento debe ser un valor numérico entre 1 y 11'

    $(this).validator(sError != '', sError);
  });

  //Validación de característica de fuerza
  $('#fue').on('blur', function() {
    var sError = '';
    sError = $(this).isValidNumberNotNull('La fuerza debe ser un valor numérico válido',
      'Debe indicarse la fuerza del jugador');

    if (sError == '' && (parseInt($(this).val()) < 1 || parseInt($(this).val()) > 10))
      sError = 'La fuerza debe ser un valor numérico entre 1 y 10';

    $(this).validator(sError != '', sError);
  });

  //Validación de característica de agilidad
  $('#agl').on('blur', function() {
    var sError = '';
    sError = $(this).isValidNumberNotNull('La agilidad debe ser un valor numérico válido',
      'Debe indicarse la agilidad del jugador');

    if (sError == '' && (parseInt($(this).val()) < 1 || parseInt($(this).val()) > 10))
      sError = 'La agilidad debe ser un valor numérico entre 1 y 10';

    $(this).validator(sError != '', sError);
  });

  //Validación de característica de armadura
  $('#av').on('blur', function() {
    var sError = '';
    sError = $(this).isValidNumberNotNull('La armadura debe ser un valor numérico válido',
      'Debe indicarse la armadura del jugador');

      if (sError == '' && (parseInt($(this).val()) < 1 || parseInt($(this).val()) > 12))
        sError = 'La armadura debe ser un valor numérico entre 1 y 12';

    $(this).validator(sError != '', sError);
  });

  //Validación de la experiencia
  $('#px').on('blur', function() {
    var sError = '';
    sError = $(this).isValidNumberNotNull('La experiencia debe ser un valor numérico válido',
      'Debe indicarse la experiencia del jugador');

    if (sError == '' && (parseInt($(this).val()) < 0 || parseInt($(this).val()) > 275))
      sError = 'La experiencia debe ser un valor numérico entre 0 y 275';

    $(this).validator(sError != '', sError);
  });

  //Validación del precio del jugador
  $('#precio').on('blur', function() {
    var sError = '';
    sError = $(this).isValidNumberNotNull('El precio debe ser un valor numérico válido',
      'Debe indicarse el precio del jugador');

    if (sError == '' && (parseInt($(this).val()) < 40000 || parseInt($(this).val()) > 1000000))
      sError = 'El precio debe ser un valor numérico entre 40000 y 1000000';

    $(this).validator(sError != '', sError);
  });

  //Validación del número de heridos provocados
  $('#hf').on('blur', function() {
    var sError = '';
    sError = $(this).isValidNumberNotNull('Los heridos provocados deben ser un valor numérico válido',
      'Debe indicarse cuantos heridos ha provocado el jugador');

    if (sError == '' && (parseInt($(this).val()) < 0 || parseInt($(this).val()) > 300))
      sError = 'Los heridos provocados deben ser un valor numérico entre 0 y 300';

    $(this).validator(sError != '', sError);
  });

  //Validación del número de muertos provocados
  $('#mf').on('blur', function() {
    var sError = '';
    sError = $(this).isValidNumberNotNull('Los muertos provocados deben ser un valor numérico válido',
      'Debe indicarse cuantos muertos ha provocado el jugador');

    if (sError == '' && (parseInt($(this).val()) < 0 || parseInt($(this).val()) > 300))
      sError = 'Los muertos provocados deben ser un valor numérico entre 0 y 300';

    $(this).validator(sError != '', sError);
  });

  //Validación del número de heridas sufridas
  $('#hc').on('blur', function() {
    var sError = '';
    sError = $(this).isValidNumberNotNull('Las heridas sufridas deben ser un valor numérico válido',
      'Debe indicarse cuantas veces ha sido herido el jugador');

    if (sError == '' && (parseInt($(this).val()) < 0 || parseInt($(this).val()) > 300))
      sError = 'Las heridas sufridas deben ser un valor numérico entre 0 y 300';

    $(this).validator(sError != '', sError);
  });

  //Validación del número de veces que el jugador ha sido sanado por el apotecario
  $('#curado').on('blur', function() {
    var sError = '';
    sError = $(this).isValidNumberNotNull('Las atenciones del apotecario deben ser un valor numérico válido',
      'Debe indicarse cuantas veces ha sido atendido por el apotecario del equipo el jugador');

    if (sError == '' && (parseInt($(this).val()) < 0 || parseInt($(this).val()) > 300))
      sError = 'Las atenciones del apotecario deben ser un valor numérico entre 0 y 300';

    $(this).validator(sError != '', sError);
  });

  //Validación del número de heridas incapacitantes sufridas por el jugador
  $('#niggling').on('blur', function() {
    var sError = '';
    sError = $(this).isValidNumberNotNull('Las heridas incapacitantes deben ser un valor numérico válido',
      'Debe indicarse cuantas heridas incapacitantes ha sufrido el jugador');

    if (sError == '' && (parseInt($(this).val()) < 0 || parseInt($(this).val()) > 10))
      sError = 'Las heridas incapacitantes deben ser un valor numérico entre 0 y 10';

    $(this).validator(sError != '', sError);
  });

  //Validación del número de pases
  $('#pases').on('blur', function() {
    var sError = '';
    sError = $(this).isValidNumberNotNull('Los pases deben ser un valor numérico válido',
      'Debe indicarse cuantos pases ha realizado el jugador');

    if (sError == '' && (parseInt($(this).val()) < 0 || parseInt($(this).val()) > 1000))
      sError = 'Los pases deben ser un valor numérico entre 0 y 1000';

    $(this).validator(sError != '', sError);
  });

  //Validación del número de yardas de pase
  $('#yardaspase').on('blur', function() {
    var sError = '';
    sError = $(this).isValidNumberNotNull('Las yardas de pase deben ser un valor numérico válido',
      'Debe indicarse cuantas yardas de pase ha conseguido el jugador');

    if (sError == '' && (parseInt($(this).val()) < 0 || parseInt($(this).val()) > 10000))
      sError = 'Las yardas de pase deben ser un valor numérico entre 0 y 10000';

    $(this).validator(sError != '', sError);
  });

  //Validación del número de intercepciones
  $('#intercepciones').on('blur', function() {
    var sError = '';
    sError = $(this).isValidNumberNotNull('Las intercepciones realizadas deben ser un valor numérico válido',
      'Debe indicarse cuantas intercepciones ha realizado el jugador');

    if (sError == '' && (parseInt($(this).val()) < 0 || parseInt($(this).val()) > 300))
      sError = 'Las intercepciones realizadas deben ser un valor numérico entre 0 y 300';

    $(this).validator(sError != '', sError);
  });

  //Validación del número de td
  $('#td').on('blur', function() {
    var sError = '';
    sError = $(this).isValidNumberNotNull('Los touchdowns anotados deben ser un valor numérico válido',
      'Debe indicarse cuantos touchdowns ha anotado el jugador');

    if (sError == '' && (parseInt($(this).val()) < 0 || parseInt($(this).val()) > 300))
      sError = 'Los touchdowns anotados deben ser un valor numérico entre 0 y 300';

    $(this).validator(sError != '', sError);
  });

  //Validación del número de partidos jugados
  $('#jugados').on('blur', function() {
    var sError = '';
    sError = $(this).isValidNumberNotNull('Los partidos jugados deben ser un valor numérico válido',
      'Debe indicarse cuantos partidos ha jugado el jugador');

    if (sError == '' && (parseInt($(this).val()) < 0 || parseInt($(this).val()) > 300))
      sError = 'Los partidos jugados deben ser un valor numérico entre 0 y 300';

    $(this).validator(sError != '', sError);
  });

  //Validación del número de mvp recibidos
  $('#mvp').on('blur', function() {
    var sError = '';
    sError = $(this).isValidNumberNotNull('Los mvp deben ser un valor numérico válido',
      'Debe indicarse cuantas veces ha sido galardonado con el mvp el jugador');

    if (sError == '' && (parseInt($(this).val()) < 0 || parseInt($(this).val()) > 300))
      sError = 'Los mvp deben ser un valor numérico entre 0 y 300';

    $(this).validator(sError != '', sError);
  });
});
