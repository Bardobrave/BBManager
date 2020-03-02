$("document").ready(function () {
  /*Fichero que gestiona las validaciones cliente de los formularios de acta*/

  //Validación de recaudacion
  $('#recaudacion').on('blur', function() {
    var sError = '';
    sError = $(this).isValidNumberNotNull('Debe indicarse un valor válido de recaudación',
      'Debe indicarse la recaudación obtenida');

    if (sError == '' && (parseInt($(this).val()) < 10000 || parseInt($(this).val()) > 140000))
      sError = 'La recaudación debe estar entre 10000 y 140000';

    $(this).validator(sError != '', sError);
  });

  //Validación de tesorería final
  $('#tesofinal').on('blur', function() {
    var sError = '';
    sError = $(this).isValidNumberNotNull('Debe indicarse un valor válido de tesorería final',
      'Debe indicarse la tesorería final del equipo');

    if (sError == '' && (parseInt($(this).val()) < 0))
      sError = 'La tesorería final no puede ser menor que 0';

    $(this).validator(sError != '', sError);
  });

  //Validación de tirada de fan
  $('#espectadores').on('blur', function() {
    var sError = '';
    sError = $(this).isValidNumberNotNull('Debe indicarse un valor válido para la tirada de fan',
      'Debe indicarse la tirada de fan');

    if (sError == '' && (parseInt($(this).val()) < 4 || parseInt($(this).val()) > 30))
      sError = 'La tirada de fan debe estar entre 4 y 30';

    $(this).validator(sError != '', sError);
  });

  //Validación de fan factor final
  $('#fffinal').on('blur', function() {
    var sError = '';
    sError = $(this).isValidNumberNotNull('Debe indicarse un valor válido de fan factor final',
      'Debe indicarse el fan factor final');

    if (sError == '' && (parseInt($(this).val()) < 2 || parseInt($(this).val()) > 18))
      sError = 'El fan factor debe estar entre 2 y 18';

    $(this).validator(sError != '', sError);
  });

  //Validación del gasto en incentivos
  $('#gastoinducements').on('blur', function() {
    var sError = '';
    sError = $(this).isValidNumberNotNull('Debe indicarse un valor válido de gasto en incentivos',
      'Debe indicarse el gasto en incentivos');

    if (sError == '' && (parseInt($(this).val()) < 0))
      sError = 'El gasto en incentivos no puede ser menor que 0';

    $(this).validator(sError != '', sError);
  });

});
